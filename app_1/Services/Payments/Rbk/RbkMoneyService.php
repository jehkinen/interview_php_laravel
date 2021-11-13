<?php

namespace App\Services\Payments\Rbk;

use App\Order;
use App\Product;
use App\OrderForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Constants\OrderFormStatus;
use Illuminate\Support\Facades\Log;
use App\Traits\ThrowClientErrorTrait;
use App\Http\Services\OrderFormService;

class RbkMoneyService extends BaseRbkService
{
    use ThrowClientErrorTrait;

    const BASE_API_V1_URL = 'https://api.rbk.money/v1';

    const PROCESSING_INVOICES_URL = 'processing/invoices';
    const PROCESSING_RECURRING_INVOICES = 'processing/invoices/{invoiceId}/payments';

    /** @var RBKCustomerService */
    protected $rbkCustomerService;

    /** @var OrderFormService */
    protected $orderFormService;

    public function __construct(RBKCustomerService $rbkCustomerService, OrderFormService $orderFormService)
    {
        parent::__construct();

        $this->setBaseApiUrl(self::BASE_API_V1_URL);
        $this->rbkCustomerService = $rbkCustomerService;
        $this->orderFormService = $orderFormService;
    }

    /**
     * @return OrderFormService
     */
    public function getOrderFormService()
    {
        return $this->orderFormService;
    }

    /**
     * @param OrderForm $orderForm
     * @throws \ErrorException
     * @return mixed
     */
    public function preparePayment(OrderForm $orderForm)
    {
        try {
            $order = $this->orderFormService->createOrder($orderForm);

            $this->setOrderId($order->id);
            $orderForm->load('order');

            $makeInvoiceResult = collect($this->makeInvoice($order));

            $invoice = $makeInvoiceResult->get('invoice');
            $invoice['invoiceAccessToken'] = Arr::get($makeInvoiceResult->get('invoiceAccessToken'), 'payload', null);

            $result = collect([
                'invoice' => $invoice,
                'order_id' => $order->id,
            ]);

            return $result;
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @param $value
     */
    public function setOrderId($value)
    {
        $this->orderId = $value;
    }

    /**
     * @param $order
     * @param $product
     * @throws \ErrorException
     * @return mixed
     */
    public function makeInvoice(Order $order)
    {
        $invoiceDetails = $this->prepareInvoiceDetails($order);

        $makeInvoiceResult = $this->makeRequest(self::PROCESSING_INVOICES_URL, $invoiceDetails);

        $order->json_response = $makeInvoiceResult;
        $order->invoice_uid = Arr::get($makeInvoiceResult, 'invoice.id');
        $order->save();

        if (! $order->invoice_uid) {
            Log::error('Error while creating invoice from rbk', [
                'invoice_details' => $invoiceDetails,
            ]);
            abort(500, 'Not able to prepare invoice');
        }
        $orderForm = $order->orderForm;
        $orderForm->update(['status' => OrderFormStatus::WAITING_FOR_PAYMENT]);

        return $makeInvoiceResult;
    }

    /**
     * @param $order
     * @param $product
     * @param mixed $invoiceAccessToken
     * @throws \ErrorException
     * @return mixed
     */
    public function recurrentChargeByInvoice(Order $order, $invoiceAccessToken)
    {
        $invoiceDetails = $this->prepareRecurringInvoiceDetails($order);

        $url = $this->replaceUrlParams(self::PROCESSING_RECURRING_INVOICES, [
            'invoiceId' => $order->invoice_uid,
        ]);

        $makeRequestResult = $this->makeRequestByAccessToken($url, $invoiceAccessToken, $invoiceDetails);

        if (! $makeRequestResult && config('app.env') !== 'testing') {
            Log::error('Could not get any response from RBK Money', [
                'invoice' => $invoiceDetails,
                'url' => $url,
            ]);
            abort(500, 'Could not get any response from RBK Money');
        }

        $order->json_response = $makeRequestResult;
        $order->invoice_uid = Arr::get($makeRequestResult, 'invoice.id');
        $order->save();

        $orderForm = $order->orderForm;
        $orderForm->update(['status' => OrderFormStatus::WAITING_FOR_PAYMENT]);

        return $makeRequestResult;
    }

    /**
     * @param Product $product
     * @param Order $order
     * @throws \ErrorException
     */
    private function prepareRecurringInvoiceDetails(Order $order)
    {
        $product = $order->product;

        if (! $product->is_recurrent) {
            throw new \ErrorException('Could not process not recurring product');
        }

        $invoiceData = [
            'flow' => [
                'type' => 'PaymentFlowInstant',
            ],
            'metadata' => ['order_id' => $order->id],
            'payer' => [
                'payerType' => 'CustomerPayer',
                'customerID' => $order->user->rbkCustomer->customer_id,
            ],

        ];

        return $invoiceData;
    }

    /**
     * @param Product $product
     * @param $orderId
     * @param Order $order
     * @return array
     */
    private function prepareInvoiceDetails(Order $order)
    {
        $product = $order->product;
        $orderForm = $order->orderForm;
        $rbkPrice = $orderForm->amount * 100;

        $invoiceData = [
            'shopID' => $this->shopId,
            'dueDate' => date('Y-m-d') . 'T23:59:59Z',
            'amount' => $rbkPrice,
            'currency' => 'RUB',
            'product' => trans('rbk_widget.product', [
                'productId' => $product->uid . ' #' . $product->id,
            ]),
            'description' => trans('rbk_widget.description', [
                'productTitle' => Str::limit($product->title, 20),
                'appUrl' => config('app.url'),
            ]),
            'metadata' => ['order_id' => $order->id],
        ];

        return $invoiceData;
    }
}

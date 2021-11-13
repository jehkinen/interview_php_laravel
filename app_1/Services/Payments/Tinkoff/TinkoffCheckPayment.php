<?php

namespace App\Services\Payments\Tinkoff;

use App\Order;
use App\OrderForm;
use App\Constants\OrderStatus;
use App\Constants\InvoiceStatus;
use App\Constants\PaymentSystems;
use App\Services\Payments\AbstractPaymentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Events\Payment\PaymentCaptured;
use Illuminate\Support\Facades\Storage;
use App\Events\Payment\PaymentProcessed;
use App\Http\Services\User\UserConfirmationService;
use Illuminate\Support\Str;

class TinkoffCheckPayment extends AbstractPaymentService
{
    const AUTHORIZED = 'AUTHORIZED';
    const CONFIRMED = 'CONFIRMED';
    const REJECTED = 'REJECTED';


    /**
     * @throws \ErrorException
     * @return bool[]
     */
    public function check(array $requestArray)
    {
        $requestCollection = collect($requestArray);

        $status = $requestCollection->get('Status');
        $paymentId = $requestCollection->get('PaymentId');

        $orderId = $this->getOrderId($requestCollection->get('OrderId'));
        $token = $requestCollection->pull('Token');

        $this->paymentId = $paymentId;

        $genToken = $this->generateToken($requestCollection);

        if ($genToken !== $token) {
            throw new \ErrorException('Token is mismatch', 400);
        }

        Storage::disk('payments')->put('responses/tinkoff/' . uniqid(PaymentSystems::TINKOFF . '_') . '.txt', json_encode($requestArray));


        /** @var OrderForm $orderForm */
        $orderForm = OrderForm::query()->with('product')->findOrFail($orderId);

        $order = $this->fetchOrCreateOrder($orderForm);
        $result = $this->handleStatus($status, $order);

        return [
            'success' => $result,
        ];
    }

    /**
     * @param Collection $collection
     * @return string
     */
    private function generateToken(Collection $collection)
    {
        $collection->pull('Receipt');
        $collection->pull('Data');
        $collection->pull('Token');
        $collection->put('Password', config('app.tinkoff.terminal_key'));

        $args = $collection->all();

        Log::info('args collection for token', [
            'args' => $args,
        ]);

        ksort($args);

        $token = '';
        foreach ($args as $arg) {
            if (! is_array($arg)) {
                $token .= is_bool($arg) ? json_encode($arg) : $arg;
            }
        }

        return hash('sha256', $token);
    }

    /**
     * @param $status
     * @param $orderId
     * @param Order $order
     */
    private function handleStatus($status, Order $order)
    {
        switch ($status) {
            case self::AUTHORIZED:
                $orderFormService = app()->make(UserConfirmationService::class);
                $orderFormService->createWebAndEmail($order->orderForm);

                $event = event(new PaymentProcessed($order->id, $this->requestArray));
                break;
            case self::CONFIRMED:
                $event = event(new PaymentCaptured($order->id, $this->requestArray));
                break;
            case self::REJECTED:
            default:
                $order->status = OrderStatus::FAILED;
                $order->save();
                break;
        }

        return true;
    }


    /**
     * @param OrderForm $orderForm
     * @return Collection
     */
    public function widgetData(OrderForm $orderForm)
    {
        $receipt = [

            'Email' => $orderForm->email,
            'Phone' => $orderForm->phone_number,
            'Taxation' => 'usn_income_outcome',
            'Items' => [
                [
                    'Name' => $orderForm->product->title,
                    'Price' => $orderForm->getCentsAmount(),
                    'Quantity' => 1,
                    'Amount' => $orderForm->getCentsAmount(),
                    'PaymentObject' => 'composite',
                    'Tax' => 'none',
                ],
            ],
        ];


        $tinkoffForm = [
            'terminalKey' => config('app.tinkoff.terminal_id'),
            'frame' => true,
            'language' => $orderForm->user->lang_code,
            'reccurentPayment' => false,
            'customerKey' => '',
            'amount' => $orderForm->amount,
            'order' => $this->generateOrderId($orderForm),
            'description' => $orderForm->product->title,
            'name' => $orderForm->user->full_name,
            'email' => $orderForm->email,
            'phone' => $orderForm->phone_number,
        ];

        return collect([
            'receipt' => $receipt,
            'tinkoff_form' => $tinkoffForm,
        ]);
    }

    /*
 * Preventing any duplicated orders from tinkoff
 */
    private function getOrderId($orderIdRaw)
    {
        if (App::environment('production')) {
            return $orderIdRaw;
        }
        return explode('|', $orderIdRaw)[0];
    }

    /*
     * Preventing any duplicated orders from tinkoff
     */
    private function generateOrderId(OrderForm $orderForm)
    {
        if (App::environment('production')) {
            return $orderForm->id;
        }

        $salt = Str::random();
        return $orderForm->id . '|' . $salt;
    }


}

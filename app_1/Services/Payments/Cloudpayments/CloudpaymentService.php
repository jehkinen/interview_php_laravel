<?php

namespace App\Services\Payments\Cloudpayments;

use App\Constants\Cloudpayments\CloudpaymentsOperationTypes;
use App\Constants\Cloudpayments\CloudpaymentsTransactionStatuses;
use App\Events\Payment\PaymentCaptured;
use App\Events\Payment\PaymentProcessed;
use App\Http\Services\HasUserTrait;
use App\OrderForm;
use App\Services\Payments\AbstractPaymentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\IpUtils;

class CloudpaymentService extends AbstractPaymentService
{
    use HasUserTrait;

    private $cfIpAddress;

    protected $isTrial = false;
    protected $cloudpaymentsSubcriptionId;
    protected $canContinue = false;

    /** @var CloudpaymentsWidgetDataProvider $widgetDataProvider */
    protected $widgetDataProvider;

    /** @var CloudpaymentsInitPayService $trialPayService */
    protected $trialPayService;

    /** @var CloupaymentsRecurrentService $recurrentService */
    protected $recurrentService;

    /** @var CloudpaymentsPayService $payService */
    protected $payService;


    /**
     * CloudpaymentService constructor.
     * @param CloudpaymentsInitPayService $initPayService
     * @param CloudpaymentsPayService $payService
     * @param CloupaymentsRecurrentService $recurrentService
     * @param CloudpaymentsWidgetDataProvider $dataProvider
     */
    public function __construct(
        CloudpaymentsInitPayService $initPayService,
        CloudpaymentsPayService $payService,
        CloupaymentsRecurrentService $recurrentService,
        CloudpaymentsWidgetDataProvider $dataProvider
    )
    {
        $this->payService = $payService;
        $this->trialPayService = $initPayService;
        $this->recurrentService = $recurrentService;
        $this->widgetDataProvider = $dataProvider;
    }


    public function refund(array $requestArray)
    {
        $this->beforeHook($requestArray);
        return $this->payService->refund($requestArray);
    }

    /**
     * Handle check payment webhook
     * @param array $requestArray
     */
    public function check(array $requestArray)
    {
        $this->beforeHook($requestArray);
        $this->beforeCheckOrPay($requestArray);

        if (!$this->canContinue) {
            abort(401, 'Could not process check');
        }

        if ($this->isTrial) {
            return $this->trialPayService->check($requestArray);
        } else {
            return $this->payService->check($requestArray);
        }
    }

    /**
     * Handle pay webhook
     *
     * @param array $requestArray
     * @throws AuthorizationException
     */
    public function pay(array $requestArray)
    {
        $this->beforeHook($requestArray);
        $this->beforeCheckOrPay($requestArray);

        if (!$this->canContinue) {
            abort(401, 'Could not process check');
        }

        if ($this->isTrial) {
            $service = $this->trialPayService;
        } else {
            $service = $this->payService;
        }

        $service->pay($requestArray);
        $orderForm = $service->getOrderForm();

        $order = $this->fetchOrCreateOrder($orderForm, $requestArray);

        event(new PaymentProcessed($order->id, $this->requestArray));
        event(new PaymentCaptured($order->id, $this->requestArray));

        return true;
    }


    /**
     * @param array $requestArray
     * @throws AuthorizationException
     * @throws \ErrorException
     */
    public function recurrent(array $requestArray)
    {
        $this->checkAccessByIp();
        $this->recurrentService->run($requestArray);
        return true;
    }


    /**
     * @param OrderForm $orderForm
     * @return false|Collection
     */
    public function widgetData(OrderForm $orderForm)
    {
        return $this->widgetDataProvider->data($orderForm);
    }


    /**
     * @param mixed $cfIpAddress
     */
    public function setCfIpAddress($cfIpAddress): void
    {
        $this->cfIpAddress = $cfIpAddress;
    }

    /**
     * Проверка, что адрес с которого пришел запрос, принадлежит серверу тинькофф
     * @param $ipAddress
     * @return bool
     */
    public function checkAccessByIp($ipAddress = null)
    {
        if (App::environment('testing') && !$ipAddress) {
            return true;
        }
        $ipToCheck = $ipAddress ?? $this->cfIpAddress;

        if (!$ipToCheck) {
            throw new \ErrorException('Nothing to check for access');
        }
        $check = IpUtils::checkIp($ipToCheck, config('cloudpayments.allowed_ips'));

        if (!$check) {
            throw new AuthorizationException('cloudpayments before hook: cf-ip-address is not valid');
        }
        return true;
    }

    /**
     * must be called before any handling hooks from cp
     * @param $requestArray
     * @throws AuthorizationException
     */
    private function beforeHook($requestArray)
    {
        $this->requestArray = $requestArray;
        $this->checkAccessByIp();
    }

    /**
     * @param $requestArray
     * @throws \ErrorException
     */
    private function beforeCheckOrPay($requestArray)
    {
        $this->setIsTrial();

        $paymentId = Arr::get($requestArray, 'TransactionId');
        $this->setPaymentId($paymentId);

        $operationType = Arr::get($requestArray, 'OperationType');
        $status = Arr::get($requestArray, 'Status');


        if ($operationType !== CloudpaymentsOperationTypes::PAYMENT) {
            throw new \ErrorException("cloudpayments check: unsupported operation type: $operationType");
        }

        switch ($status) {
            case CloudpaymentsTransactionStatuses::COMPLETED:
                $this->canContinue = true;
                break;
            default:
                $this->canContinue = false;
                break;
        }
    }


    public function setIsTrial()
    {
        try {
            $recurrentData = json_decode($this->requestArray['Data'], true);
            $isTrial = Arr::get($recurrentData, 'isTrial', false);
            $this->isTrial = $isTrial;
        } catch (\Throwable $exception) {
            $this->isTrial = false;
        }
    }

}
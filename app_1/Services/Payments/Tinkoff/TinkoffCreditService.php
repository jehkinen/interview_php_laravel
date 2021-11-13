<?php


namespace App\Services\Payments\Tinkoff;


use App\Constants\OrderFormStatus;
use App\Events\Payment\PaymentCaptured;
use App\Events\Payment\PaymentProcessed;
use App\Http\Services\User\UserConfirmationService;
use App\OrderForm;
use App\Services\Payments\AbstractPaymentService;
use Illuminate\Support\Facades\App;

class TinkoffCreditService extends AbstractPaymentService
{
    const STATUS_SIGNED = 'signed';
    const LOW_IP = '91.194.226.1';
    const HIGH_IP = '91.194.227.254';

    /**
     * @param $requestArray
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function check(array $requestArray)
    {
        $dataCollection = collect($requestArray);

        $ipAddress = $dataCollection->get('ip_address');

        if (!$this->checkAccessByIp($ipAddress)) {
            abort(403, 'Access denied');
        }

        $creditStatus = $dataCollection->get('status');

        if ($creditStatus === self::STATUS_SIGNED) {
            $orderFormId = (int) $dataCollection->get('id');
            $orderForm = OrderForm::query()->findOrFail($orderFormId);

            $orderForm->is_installment = true;
            $orderForm->save();

            if ($orderForm->status === OrderFormStatus::WAITING_FOR_PAYMENT) {
                $order = $this->fetchOrCreateOrder($orderForm);
                $orderFormService = app()->make(UserConfirmationService::class);
                $orderFormService->createWebAndEmail($order->orderForm);
                event(new PaymentProcessed($order->id, $dataCollection));
                event(new PaymentCaptured($order->id, $dataCollection));
            } else {
                $orderForm->status = OrderFormStatus::CANCELLED;
                $orderForm->save();
            }

        }
    }


    /**
     * Проверка, что адрес с которого пришел запрос, принадлежит серверу тинькофф
     * @param $ipAddress
     * @return bool
     */
    private function checkAccessByIp($ipAddress)
    {
        if (App::environment('testing')) {
            return true;
        }

        if (version_compare(self::LOW_IP, $ipAddress) + version_compare($ipAddress, self::HIGH_IP) === -2) {
            return true;
        }

        return false;
    }

}
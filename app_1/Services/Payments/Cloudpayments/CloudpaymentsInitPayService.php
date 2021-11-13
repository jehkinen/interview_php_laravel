<?php


namespace App\Services\Payments\Cloudpayments;


use App\Constants\Cloudpayments\UserCloudpaymentSubscriptionStatuses;
use App\Constants\OrderFormStatus;
use App\OrderForm;
use App\Product;
use App\UserCloudpaymentsSubscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CloudpaymentsInitPayService extends AbstractPaymentAction
{

    /**
     * Check if all params valid to continue payment
     * @param array $requestArray
     * @return bool
     * @throws \ErrorException
     */
    public function check(array $requestArray)
    {
        $orderForm = $this->findOrderForm($requestArray);

        // checking if order form exists
        if ($orderForm->status !== OrderFormStatus::WAITING_FOR_PAYMENT) {
            throw new \ErrorException('cloudpayments check: invalid order form status: ' . $orderForm->status);
        }

        $ofAmount = (int) $orderForm->amount;
        $cpAmount = (int)  $requestArray['Amount'];

        // checking if amounts were match
        if ($cpAmount !== $ofAmount) {
            throw new \ErrorException(
                "cloudpayments check: $ofAmount and cp: $cpAmount");
        }

        $accountId = (int) $requestArray['AccountId'];

        // checking if order_forms.user_id and AccountId were matched
        if ($orderForm->user_id !== $accountId) {
            throw new \ErrorException("order_forms.user_id and cp account id did not match: of: $orderForm->user_id vs cp: $accountId");
        }

        return true;
    }



    /**
     * @param $requestArray
     * @param OrderForm $orderForm
     * @return UserCloudpaymentsSubscription
     */
    public function pay($requestArray)
    {
        $orderForm = $this->findOrderForm($requestArray);

        $token = Arr::get($requestArray, 'Token');
        $subscriptionId = Arr::get($requestArray, 'SubscriptionId');


        $user = $orderForm->user;
        $cloudpaymentSettings = $orderForm->product->cloudPaymentsSettings;
        $validTo = now()->addDays($cloudpaymentSettings->trial_in_days)->endOfDay()->format('Y-m-d H:i:s');
        $recurrentPeriod = $cloudpaymentSettings->recurrent_period;
        $recurrentInterval = $cloudpaymentSettings->recurrent_interval;
        $recurrentPrice = $cloudpaymentSettings->recurrent_price;


        /** @var UserCloudpaymentsSubscription $userSubscription */
        $userSubscription = $user->cloudpaymentsSubscriptions()->create([
            'recurrent_period' => $recurrentPeriod,
            'recurrent_interval' => $recurrentInterval,
            'recurrent_price' => $recurrentPrice,
            'product_id' => $orderForm->product_id,
            'is_trial_period' => true,
            'cloudpayments_subscription_id' => $subscriptionId,
            'status' => UserCloudpaymentSubscriptionStatuses::ACTIVE,
            'valid_to' => $validTo,
            'token' => $token
        ]);

        $this->setOrderForm($orderForm);
        return $userSubscription;
    }


    /**
     * @param $requestArray
     * @return OrderForm
     * @throws \ErrorException
     */
    private function findOrderForm($requestArray)
    {
        $orderFormId = (int) Arr::get($requestArray, 'InvoiceId');
        /** @var OrderForm $orderForm */
        $orderForm = OrderForm::query()->find((int) $orderFormId);

        if (!$orderForm) {
            throw new \ErrorException('cloudpayments check: order form does not exists');
        }

        $this->setOrderForm($orderForm);

        return $orderForm;
    }



}
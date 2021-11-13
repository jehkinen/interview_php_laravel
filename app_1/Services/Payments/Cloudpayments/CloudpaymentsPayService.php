<?php


namespace App\Services\Payments\Cloudpayments;

use App\Constants\Cloudpayments\CloudpaymentsRecurrentIntervals;
use App\Constants\Cloudpayments\UserCloudpaymentSubscriptionStatuses;
use App\Constants\OrderFormSource;
use App\Constants\PaymentSystems;
use App\Constants\UserRecurrentSubscriptionStatuses;
use App\Http\Services\Moderator\AdminPaymentDemandService;
use App\Http\Services\OrderFormService;
use App\Jobs\CloudpaymentsSubAssignRebillProduct;
use App\Product;
use App\Services\Payments\Cloudpayments\Api\UpdateSubscriptionDto;
use App\Transaction;
use App\User;
use App\UserCloudpaymentsSubscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class CloudpaymentsRecurrentAction
 * @package App\Services\Payments\Cloudpayments
 */
class CloudpaymentsPayService extends AbstractPaymentAction
{

    /**
     * @param array $requestArray
     * @return UserCloudpaymentsSubscription
     * @throws \ErrorException
     */
    public function fetchSub(array $requestArray)
    {
        $subscriptionId = Arr::get($requestArray, 'SubscriptionId');

        if (!$subscriptionId) {
            throw new \ErrorException('cp pay: unable to process payment, empty subscription id');
        }

        $userCpSub = UserCloudpaymentsSubscription::query()
            ->where('cloudpayments_subscription_id', $subscriptionId)
            ->firstOrFail();

        return $userCpSub;

    }


    /**
     * @param array $requestArray
     * @throws \ErrorException
     */
    public function check(array $requestArray)
    {
        $cpSub = $this->fetchSub($requestArray);

        $cpSettings = $cpSub->product->cloudPaymentsSettings;

        $amount = Arr::get($requestArray, 'Amount');

        /*
        if ($cpSub->is_changed_to_daily) {
            if ((int) $amount !== (int) $cpSettings->recurrent_daily_price) {
                throw new \ErrorException("cp recurrent check: Invalid amount : $amount vs $cpSettings->recurrent_price");
            }
        } else {
            if ((int) $amount !== (int) $cpSettings->recurrent_price) {
                throw new \ErrorException("cp recurrent check: Invalid amount : $amount vs $cpSettings->recurrent_price");
            }
        }
        */

        $accountId = (int) Arr::get($requestArray, 'AccountId');

        if ($accountId !== $cpSub->user_id) {
            throw new \ErrorException('cp recurrent check: Invalid user_id and AccountId did not match');
        }

        return true;
    }


    /**
     * @param array $requestArray
     * @return UserCloudpaymentsSubscription|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function pay(array $requestArray)
    {
        $userCpSub = $this->fetchSub($requestArray);

        $user = $userCpSub->user;

        $this->setOrderForm($this->createOrderForm($user, $userCpSub->product, $requestArray));

        if ($userCpSub->is_changed_to_daily) {
            $userCpSub->daily_paid_counter +=1;
        }

        $recurrentInterval = $userCpSub->recurrent_interval;


        switch ($recurrentInterval) {
            case CloudpaymentsRecurrentIntervals::Month:
                $validTo = now()->addMonths($recurrentInterval);
                break;
            case CloudpaymentsRecurrentIntervals::Week:
                $validTo = now()->addWeeks($recurrentInterval);
                break;
            case CloudpaymentsRecurrentIntervals::Day:
                $validTo = now()->addDays($recurrentInterval);
                break;
            default:
                throw new \ErrorException('cp recurrent pay: invalid recurrent interval: ' . $recurrentInterval);
        }

        $userCpSub->is_trial_period = false;
        $userCpSub->status = UserCloudpaymentSubscriptionStatuses::ACTIVE;
        $userCpSub->valid_to = $validTo;
        $userCpSub->save();

        CloudpaymentsSubAssignRebillProduct::dispatch($userCpSub);

        return $userCpSub;
    }


    public function refund(array $requestArray)
    {
        $orderFormId = Arr::get($requestArray, 'InvoiceId');

        $transaction = Transaction::query()
            ->whereJsonContains('json_data->order_form_id', $orderFormId)
            ->first();

        if ($transaction) {
            /** @var AdminPaymentDemandService $paymentService */
            $paymentService = app()->make(AdminPaymentDemandService::class);
            $paymentService->refund($transaction);
        }
        return true;
    }

    /**
     * @param User $user
     * @param Product $product
     * @param array $requestArray
     * @return \App\OrderForm|bool|void
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function createOrderForm(User $user, Product $product, array $requestArray)
    {
        /** @var OrderFormService $orderFormService */
        $orderFormService = app()->make(OrderFormService::class);
        $orderFormService->setSource(OrderFormSource::MARKET_PLACE);
        $orderFormService->setUser($user);
        $orderForm = $orderFormService->createByUser($product->uid);
        $orderForm->is_cloudpayments_subscription = true;
        $orderForm->payment_system = PaymentSystems::CLOUDPAYMENTS;
        $orderForm->amount = Arr::get($requestArray, 'Amount');
        $orderForm->save();

        return $orderForm;
    }
}

<?php


namespace App\Services\Payments\Cloudpayments;


use App\Constants\Cloudpayments\UserCloudpaymentSubscriptionStatuses;
use App\Jobs\ChangeCpSubscriptionToDaily;
use App\Services\Payments\Cloudpayments\Api\UpdateSubscriptionDto;
use App\UserCloudpaymentsSubscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CloupaymentsRecurrentService
{
    /**
     * @param array $requestArray
     * @return UserCloudpaymentsSubscription
     * @throws \ErrorException
     */
    public function run(array $requestArray)
    {
        $subscriptionId = Arr::get($requestArray, 'Id');

        /** @var UserCloudpaymentsSubscription $userCpSub */
        $userCpSub = UserCloudpaymentsSubscription::query()
            ->where('cloudpayments_subscription_id', $subscriptionId)
            ->first();

        if (!$userCpSub) {
            throw new \ErrorException('cp recurrent: user_cloudpayments_subscriptions did not found ' . $subscriptionId);
        }

        $failedTransactionsNumber = Arr::get($requestArray, 'FailedTransactionsNumber');
        $successfulTransactionsNumber = Arr::get($requestArray, 'SuccessfulTransactionsNumber');


        $nextPaymentAt = Arr::get($requestArray, 'NextTransactionDate');

        if ($nextPaymentAt && Carbon::canBeCreatedFromFormat($nextPaymentAt, 'Y-m-d H:i:s')) {
            $validTo = Carbon::createFromFormat('Y-m-d H:i:s', $nextPaymentAt);
            $userCpSub->valid_to = $validTo;
        }

        $userCpSub->failed_transactions_number = $failedTransactionsNumber;
        $userCpSub->successful_transactions_number = $successfulTransactionsNumber;

        $newStatus = Arr::get($requestArray, 'Status');
        $userCpSub->status = $newStatus;

        $userCpSub->save();

         if ($newStatus === UserCloudpaymentSubscriptionStatuses::PAST_DUE) {
             ChangeCpSubscriptionToDaily::dispatch($userCpSub)->delay(now()->addMinutes(10));
         }


        return $userCpSub;

    }


}

<?php


namespace App\Services\Payments\Cloudpayments;


use App\Constants\Cloudpayments\UserCloudpaymentSubscriptionStatuses;
use App\Constants\CloudpaymentsRecurrentIntervals;
use App\Services\Payments\Cloudpayments\Api\CloudpaymentsApiService;
use App\Services\Payments\Cloudpayments\Api\UpdateSubscriptionDto;
use App\UserCloudpaymentsSubscription;
use Illuminate\Support\Facades\App;


class CloudpaymentsChangeSubscriptionService
{
    /** @var $apiService CloudpaymentsApiService */
    private $apiService;

    private $error;
    private $hasError = false;


    public function __construct(CloudpaymentsApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    private function setError($error)
    {
        $this->hasError = true;
        $this->error = $error;
    }
    public function hasError()
    {
        return $this->hasError;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * Change user subscription to daily basis
     * @param UserCloudpaymentsSubscription $userCpSub
     * @return UserCloudpaymentsSubscription|false
     */
    public function changeToDaily(UserCloudpaymentsSubscription $userCpSub, $checkConditions = true)
    {
        try {
            if ($checkConditions && !$this->isChangeToDailyPossible($userCpSub)) {
                return false;
            }

            $product = $userCpSub->product;
            $cpSettings = $userCpSub->product->cloudPaymentsSettings;

            $description = trans('cloudpayments.description_daily', [
                'product_title' => $product->title,
                'recurrent_daily_price' => $cpSettings->recurrent_daily_price,
                'currency' => 'RUB'
            ]);

            $this->apiService->changeSubscription(new UpdateSubscriptionDto(
                $userCpSub->cloudpayments_subscription_id,
                $description,
                $cpSettings->recurrent_daily_price,
                now()->addMinutes(2)->format('Y-m-d H:i:s'),
                CloudpaymentsRecurrentIntervals::Day,
                1,
            ));

            $userCpSub->valid_to = now()->addDay();

            $userCpSub->recurrent_price = $cpSettings->recurrent_daily_price;
            $userCpSub->is_trial_period = false;
            $userCpSub->recurrent_period = 1;
            $userCpSub->daily_paid_counter = 0;
            $userCpSub->recurrent_interval = CloudpaymentsRecurrentIntervals::Day;
            $userCpSub->is_changed_to_daily = true;

            $userCpSub->save();
            return $userCpSub;

        } catch (\Throwable $exception) {
            $this->setError($exception->getMessage());
            app('sentry')->captureException($exception);
            return false;
        }
    }


    /**
     * @param UserCloudpaymentsSubscription $userCpSub
     * @return UserCloudpaymentsSubscription|false
     */
    public function changeToDefaultAfterWeek(UserCloudpaymentsSubscription $userCpSub)
    {
        try {
            $product = $userCpSub->product;
            $cpSettings = $userCpSub->product->cloudPaymentsSettings;

            if (!$this->isChangeToDefaultPossible($userCpSub)) {
                return false;
            }

            /** @var  $cloudpaymentWidgetDataProvider CloudpaymentsWidgetDataProvider */
            $cloudpaymentWidgetDataProvider = app()->make(CloudpaymentsWidgetDataProvider::class);

            $description = $cloudpaymentWidgetDataProvider->getProductDescription($product);

            $this->apiService->changeSubscription(new UpdateSubscriptionDto(
                $userCpSub->cloudpayments_subscription_id,
                $description,
                $cpSettings->recurrent_price,
                now()->addDay(), // меняем подписку на изначальные настройки, первое списание будет завтра
                $cpSettings->recurrent_interval,
                $cpSettings->recurrent_period,
            ));

            $userCpSub->recurrent_price = $cpSettings->recurrent_price;
            $userCpSub->recurrent_period = $cpSettings->recurrent_period;
            $userCpSub->recurrent_interval = $cpSettings->recurrent_interval;
            $userCpSub->is_changed_to_daily = false;
            $userCpSub->daily_paid_counter = 0;

            $userCpSub->save();
            return $userCpSub;

        } catch (\Throwable $exception) {
            app('sentry')->captureException($exception);
            return false;
        }
    }


    /**
     * @param UserCloudpaymentsSubscription $userCpSub
     * @return bool
     */
    private function isChangeToDailyPossible(UserCloudpaymentsSubscription $userCpSub)
    {
        $cpSettings = $userCpSub->product->cloudPaymentsSettings;

        if (!App::environment('production')) {
            return true;
        }

        if ($userCpSub->status !== UserCloudpaymentSubscriptionStatuses::PAST_DUE) {
            $this->setError('По этой подписке не было неудачных списаний');
            return false;
        }

        if ($userCpSub->is_changed_to_daily) {
            $this->setError('Подписка уже переведена на подневные списания');
            return false;
        }

        if (!$cpSettings->change_recurrent_interval_to_daily_if_fails) {
            $this->setError('В настройках подписки отключена опция смены интервала на подневные списания');
            return false;
        }

        return true;
    }

    private function isChangeToDefaultPossible(UserCloudpaymentsSubscription $userCpSub)
    {
        if ($userCpSub->status !== UserCloudpaymentSubscriptionStatuses::ACTIVE) {
            $this->setError("Эта подписка неактивна и имеет статус $userCpSub->status");
            return false;
        }

        if (!$userCpSub->is_changed_to_daily) {
            $this->setError('Подписка уже настроена на изначальные настройки');
            return false;
        }

        $oneWeek = 7;

        if ($userCpSub->daily_paid_counter < $oneWeek) {
            $this->setError('Не прошла неделя с момента переключения подписки на подневные списания');
            return false;
        }

        $cpSettings = $userCpSub->product->cloudPaymentsSettings;

        if (!$cpSettings->change_back_recurrent_interval_after_one_week) {
            $this->setError('Опция возврата на изначальные настройки отключена у этой подписки');
            return false;
        }

        return true;
    }

}

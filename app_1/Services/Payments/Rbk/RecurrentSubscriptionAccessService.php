<?php

namespace App\Services\Payments\Rbk;

use App\AuthorSubscription;
use App\UserRecurrentSubscription;
use App\Jobs\ChargeUserForRecurrentSubscriptionJob;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Constants\UserRecurrentSubscriptionStatuses;
use Illuminate\Support\Facades\Log;

class RecurrentSubscriptionAccessService
{
    const MAX_CHARGE_TRIES = 3;


    public function processSubscriptions($models = [])
    {
        $this->cancelNotActiveAndOvercharged();

        $subsToCharge = $models ?? $this->fetchSubscriptionsToCharge();

        foreach ($subsToCharge as $subscription) {
            ChargeUserForRecurrentSubscriptionJob::dispatch($subscription);
        }

        $this->cancelFailedToChargeSubscriptions();
    }

    /**
     * Возвращает список подписок, которые должны быть обработаны.
     * @return UserRecurrentSubscription[]
     */
    private function fetchSubscriptionsToCharge()
    {
        $subscriptions = UserRecurrentSubscription::query()
            ->where(function ($q) {
                $q->where('status', UserRecurrentSubscriptionStatuses::ACTIVE);
                $q->whereDate('valid_to', '<=', now());
            })
            ->orWhere(function ($q) {
                $q->where('status', UserRecurrentSubscriptionStatuses::WAITING_FOR_PAYMENT);
                $q->whereDate('updated_at', '<=', now()->startOfDay()->addWeeks(2));
            })
            ->where('charge_tries', '<', self::MAX_CHARGE_TRIES)
            ->get();

        return $subscriptions;
    }

    /**
     * Отменить подписки:
     * - у которых число попыток списания выше предела
     * - которые были отменены сами пользователем и у них вышел срок действия.
     */
    private function cancelNotActiveAndOvercharged()
    {
        $waitingForPaymentOvercharged = UserRecurrentSubscription::query()
            ->where('status', UserRecurrentSubscriptionStatuses::WAITING_FOR_PAYMENT)
            ->with('product.model')
            ->where('charge_tries', '>=', self::MAX_CHARGE_TRIES);

        $this->discardAccess($waitingForPaymentOvercharged);

        $cancelledSubscriptions =
            UserRecurrentSubscription::query()
                ->with('product.model')
                ->where('status', UserRecurrentSubscriptionStatuses::CANCELLED)
                ->whereDate('valid_to', '<=', now());

        $this->discardAccess($cancelledSubscriptions);
    }

    private function cancelFailedToChargeSubscriptions()
    {
        $query = UserRecurrentSubscription::query()->whereIn('status', [
            UserRecurrentSubscriptionStatuses::ACTIVE,
        ])
            ->with('product.model')
            ->whereDate('valid_to', '<=', now())
            ->where('charge_tries', '>=', self::MAX_CHARGE_TRIES);

        $this->discardAccess($query);
    }

    private function discardAccess($query)
    {
        $query
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->orderBy('user_recurrent_subscriptions.created_at')
            ->chunk(20, function ($subscriptions) {

                /** @var UserRecurrentSubscription $subscription */
                foreach ($subscriptions as $subscription) {
                    try {

                        /** @var AuthorSubscription $authorSubscription */
                        $authorSubscription = $subscription->product->model;

                        $student = $subscription->user;

                        $student->userProducts()->where([
                            'product_id' => $subscription->product_id,
                        ])->delete();

                        if ($authorSubscription->products()->withTrashed()->count() === 0) {
                            continue;
                        }

                        $courseIds = $authorSubscription
                            ->products()
                            ->pluck('course_id')
                            ->unique()
                            ->all();

                        $student
                            ->studentCourses()
                            ->whereIn('course_id', $courseIds)
                            ->where('access_by_subscription', '>', 0)
                            ->decrement('access_by_subscription');
                    } catch (\Throwable $throwable) {
                        Log::error('discard failed ' . $throwable->getMessage());
                        app('sentry')->captureException($throwable);
                    }
                }
            });

        $query
            ->update([
                'charge_tries' => 0,
                'status' => UserRecurrentSubscriptionStatuses::EXPIRED,
            ]);
    }
}

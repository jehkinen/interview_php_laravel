<?php

namespace App\Services\Payments\Rbk\Webhooks;

use App\Order;
use App\Constants\OrderStatus;
use App\Events\Payment\PaymentCaptured;
use App\Events\Payment\PaymentProcessed;


class RBKPaymentWebhookService
{
    const EVENT_PAYMENT_STARTED = 'PaymentStarted';
    const EVENT_PAYMENT_CAPTURED = 'PaymentCaptured';
    const EVENT_PAYMENT_PROCESSED = 'PaymentProcessed';
    const EVENT_PAYMENT_CANCELLED = 'PaymentCancelled';

    const EVENT_INVOICE_CREATED = 'InvoiceCreated';
    const EVENT_INVOICE_PAID = 'InvoicePaid';
    const EVENT_INVOICE_CANCELLED = 'InvoiceCancelled';

    private $requestContent;

    private $validateService;

    /**
     * RbkCheckPaymentService constructor.
     * @param \App\Services\Payments\Rbk\Webhooks\RBKValidateRequestService $validateService
     */
    public function __construct(RBKValidateRequestService $validateService)
    {
        $this->validateService = $validateService;
    }

    /**
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return array|bool|bool[]|mixed|null
     */
    public function run($requestContent)
    {
        $this->requestContent = $requestContent;

        $this->validateService->validate($requestContent);


        $eventType = $this->validateService->getKeyFromRequest('eventType');
        $orderId = $this->validateService->getKeyFromRequest('invoice.metadata.order_id');

        /** @var $email string Емейл из платежного виджета рбк $email */
        $email = $this->validateService->getKeyFromRequest('payment.contactInfo.email');

        return $this->handleEventType($eventType, $orderId);
    }


    private function handleEventType($eventType, $orderId)
    {
        $requestContent = $this->requestContent;

        try {
            $order = Order::query()
                ->with('product')
                ->findOrFail($orderId);

            if (! $order) {
                throw new \ErrorException('Order with given id not exists or already completed ' . $orderId);
            }

            switch ($eventType) {
                case self::EVENT_INVOICE_CANCELLED:
                    return [];
                case self::EVENT_INVOICE_PAID:
                case self::EVENT_PAYMENT_STARTED:
                case self::EVENT_INVOICE_CREATED:
                    break;
                case self::EVENT_PAYMENT_PROCESSED:
                    if ($order->status === OrderStatus::CREATED) {
                        $event = event(new PaymentProcessed($orderId, $requestContent));
                    } else {
                        return [];
                    }
                    break;
                case self::EVENT_PAYMENT_CAPTURED:
                    if ($order->status === OrderStatus::PROCESSING) {
                        $event = event(new PaymentCaptured($orderId, $requestContent));
                    } else {
                        return [];
                    }
                    break;
                default:
                    throw new \ErrorException('Unknown event type, could not process check payment ' . $eventType, 503);
            }

            if (config('app.env') === 'testing') {
                return $event[0]->getContent();
            }

            return [
                'success' => true,
            ];
        } catch (\ErrorException $exception) {
            throw  $exception;
        }
    }

}

<?php


namespace App\Services\Payments\Rbk\Webhooks;


use App\Constants\RbkCustomerStatuses;
use App\OrderForm;
use App\RbkCustomer;
use App\Services\Payments\Rbk\RBkRecurrentPaymentService;


class RBKCustomerWebhookService
{
    const EVENT_CUSTOMER_CREATED = 'CustomerCreated';
    const EVENT_CUSTOMER_BINDING_STARTED = 'CustomerBindingStarted';
    const EVENT_CUSTOMER_BINDING_SUCCEED = 'CustomerBindingSucceeded';
    const EVENT_CUSTOMER_READY = 'CustomerReady';
    const EVENT_CUSTOMER_DELETED = 'CustomerDeleted';
    const EVENT_CUSTOMER_BINDING_FAILED = 'CustomerBindingFailed';

    /** @var */
    protected $requestData;
    private $validateRequestService;

    const EVENT_LIST = [
        self::EVENT_CUSTOMER_BINDING_STARTED,
        self::EVENT_CUSTOMER_CREATED,
        self::EVENT_CUSTOMER_BINDING_SUCCEED,
        self::EVENT_CUSTOMER_READY,
        self::EVENT_CUSTOMER_DELETED,
        self::EVENT_CUSTOMER_BINDING_FAILED
    ];

    /**
     * RbkCustomerWebhookService constructor.
     * @param RBKValidateRequestService $validateRequestService
     */
    public function __construct(RBKValidateRequestService $validateRequestService)
    {
        $this->validateRequestService = $validateRequestService;
    }


    /**
     * @param $requestContent
     * @return array|bool|bool[]|mixed|null
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function run($requestContent)
    {
        $this->requestData = $requestContent;
        $this->validateRequestService->validate($requestContent);

        $eventType = $this->validateRequestService->getKeyFromRequest('eventType');

        if (!in_array($eventType, self::EVENT_LIST)) {
            throw new \ErrorException('Unsupported event type ' . $eventType);
        }

        return $this->handleEvent($eventType);
    }


    /**
     * Обработка различных вебхуков от рбк-мани.
     *
     * @param $requestContentAsArray
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handleEvent($eventType)
    {
        switch ($eventType) {
            case self::EVENT_CUSTOMER_READY:
            case self::EVENT_CUSTOMER_CREATED:
            case self::EVENT_CUSTOMER_BINDING_STARTED:
                return [];

            case self::EVENT_CUSTOMER_BINDING_FAILED:
                return $this->handleCustomerBindingFailed();

            case self::EVENT_CUSTOMER_BINDING_SUCCEED:
                return $this->handleBindingSucceed();
            case self::EVENT_CUSTOMER_DELETED:
                return $this->handleCustomerDeleted();
            default:
                throw new \ErrorException('Unknown event type, could not process check payment ' . $eventType, 503);
        }
    }

    /**
     * @return bool
     */
    public function handleCustomerBindingFailed()
    {
        $customerId = $this->validateRequestService->getKeyFromRequest('customer.id');
        /** @var RbkCustomer $customer */
        $customer = RbkCustomer::whereCustomerId($customerId)->first();

        if ($customer) {
            $customer->status = RbkCustomerStatuses::UNREADY;
            return $customer->save();
        }
        return false;
    }

    /**
     * Успешная привязка карты
     * Карту можно привязать либо из профиля, либо через покупку подписки
     * В случае, если это была привзяка через покупку, дополнительно выполнится списание средств по orderForm.
     *
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return array
     */
    protected function handleBindingSucceed()
    {
        $customerId = $this->validateRequestService->getKeyFromRequest('customer.id');
        $cardDetails = $this->validateRequestService->getKeyFromRequest('binding.paymentResource.paymentToolDetails');


        $customer = RbkCustomer::whereCustomerId($customerId)->firstOrFail();

        $customerUpdated = $customer->update([
            'status' => RbkCustomerStatuses::READY,
            'card_details' => $cardDetails,
        ]);

        $orderFormId = $this->validateRequestService->getKeyFromRequest('customer.metadata.order_form_id');;

        if ($orderFormId) {
            $orderForm = OrderForm::query()->find($orderFormId);

            // привязка через покупку, списываем средства с пользователя
            if ($orderForm) {
                /** @var RBkRecurrentPaymentService $rbkRecurrentService */
                $rbkRecurrentService = app()->make(RBkRecurrentPaymentService::class);
                return $rbkRecurrentService->charge($orderForm);
            }
        }

        return [
            'success' => $customerUpdated,
        ];
    }


    /**
     * Пользователь удалил свою карту, сработал вебхук удаления кастомера.
     * Удаление рбк-кастомера из базы.
     *
     * @param $requestContentAsArray
     * @throws \Exception
     * @return bool|mixed|null
     */
    protected function handleCustomerDeleted()
    {
        $customerId = $this->validateRequestService->getKeyFromRequest('customer.id');

        return RbkCustomer::query()->where([
            'customer_id' => $customerId,
        ])->delete();
    }
}

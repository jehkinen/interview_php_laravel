<?php

namespace App\Services\Payments\Rbk;

use App\Services\Payments\Rbk\Webhooks\RBKValidateRequestService;
use App\User;
use App\OrderForm;
use App\RbkCustomer;
use App\UserConfirmation;
use Illuminate\Support\Arr;
use App\Constants\RbkCustomerStatuses;

class RBKCustomerService extends BaseRbkService
{
    const CREATE_CUSTOMER_TOKEN_URL = 'customers/{customerId}/access-tokens';
    const CREATE_CUSTOMER_URL = 'customers';

    const BASE_API_V2_URL = 'https://api.rbk.money/v2/processing';

    /** @var string DELETE or GET customer */
    const CUSTOMER_URL = 'customers/{customerId}';


    /**
     * RbkCustomerWebhookService constructor.
     * @param RBKValidateRequestService $rbkValidateRequestService
     */
    public function __construct()
    {
        $this->setBaseApiUrl(self::BASE_API_V2_URL);
        parent::__construct();
    }


    /**
     * @param User $user
     * @return bool
     */
    public function isReady(User $user)
    {
        return $user->rbkCustomer && $user->rbkCustomer->isReady();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isNotReady(User $user)
    {
        return !$this->isReady($user);
    }


    /**
     * @param RbkCustomer $rbkCustomer
     * @throws \ErrorException
     * @return RbkCustomer
     */
    public function createAccessToken(RbkCustomer $rbkCustomer)
    {
        if (! $rbkCustomer->customer_id) {
            throw new \ErrorException('rbk customer_id is empty');
        }
        $url = $this->replaceUrlParams(self::CREATE_CUSTOMER_TOKEN_URL, [
            'customerId' => $rbkCustomer->customer_id,
        ]);

        $makeRequestResult = $this->makeRequest($url);
        $token = Arr::get($makeRequestResult, 'payload');
        $rbkCustomer->update(['customer_access_token' => $token]);

        return $rbkCustomer;
    }

    /**
     * @param RbkCustomer $rbkCustomer
     * @throws \ErrorException
     * @return bool
     */
    public function checkAndUpdate(RbkCustomer $rbkCustomer)
    {
        $checkCustomerUrl = $this->replaceUrlParams(self::CUSTOMER_URL, [
            'customerId' => $rbkCustomer->customer_id,
        ]);

        $makeRequestResult = $this->makeRequest($checkCustomerUrl, [], 'GET');
        $status = Arr::get($makeRequestResult, 'status');

        if ($status) {
            $rbkCustomer->update([
                'status' => $status,
            ]);

            return $status === RbkCustomerStatuses::READY;
        }

        return false;
    }

    /**
     * @param User $user
     * @throws \ErrorException
     * @return \Illuminate\Support\Collection
     */
    public function delete(User $user)
    {
        $result = collect([
            'success' => true,
        ]);

        if ($user->rbkCustomer) {
            $url = $this->replaceUrlParams(self::CUSTOMER_URL, [
                'customerId' => $user->rbkCustomer->customer_id,
            ]);

            $requestResult = $this->makeRequest($url, [], 'DELETE');

            if ($this->getHttpStatus() === 404 || $this->getHttpStatus() === 204) {
                $user->rbkCustomer->delete();

                $result->put('message', 'Карта была удалена из вашего аккаунта');

                return  $result;
            }
            throw new \ErrorException('Could not able to delete customer ' . json_encode($requestResult));
        }

        $result->put('success', false);
        $result->put('message', 'Нельзя удалить карту, возможно она неактивна или уже была удалена');

        return $result;
    }

    /**
     * Создать кастомера в рбк-мани, получить данные для открытия виджета с привязкой карты на frontend.
     * Можно вызвать без orderForm, если требуется просто подвязать карту.
     *
     * При вызове с ордер формой в описании виджета оплаты будет указан срок и продукт на который будут происходить списания.
     *
     * Если кастомера не существует в базе, он будет создать и в базе и в рбк-мани,
     * В случае если кастомер создан в рбк, но карта не подвязана или он не активен по какой-то причине, то будет просто
     * обновлен токен для этого кастомера. Для каждого пользователя на мелетон только 1 кастомер и его id всегда постоянный
     *
     * @param OrderForm $orderForm
     * @param User $user
     * @throws \ErrorException
     * @return mixed
     */
    public function fetchOrCreate(User $user, OrderForm $orderForm = null)
    {
        $rbkCustomer = $user->rbkCustomer;

        if (! $rbkCustomer) {
            $rbkCustomer = $user->rbkCustomer()->create([
                'status' => RbkCustomerStatuses::NEW,
            ]);
        }

        if ($rbkCustomer->isNew()) {
            $requestData = [
                'shopID' => $this->shopId,
                'contactInfo' => [
                    'fullName' => $user->full_name,
                    'userId' => $user->id,
                ],
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ];
            if ($user->phone_number) {
                $requestData['contactInfo']['phoneNumber'] = $user->phone_number;
            }

            if ($user->email) {
                $requestData['contactInfo']['email'] = $user->email;
            }

            if ($orderForm) {
                $requestData['metadata']['order_form_id'] = $orderForm->id;
            }

            $customerData = $this->makeRequest(self::CREATE_CUSTOMER_URL, $requestData);

            $customerStatus = Arr::get($customerData, 'customer.status');
            $customerId = Arr::get($customerData, 'customer.id');
            $customerAccessToken = Arr::get($customerData, 'customerAccessToken.payload');

            if ($customerStatus === RbkCustomerStatuses::UNREADY) {
                $rbkCustomer->update([
                    'customer_access_token' => $customerAccessToken,
                    'customer_id' => $customerId,
                    'status' => RbkCustomerStatuses::UNREADY,
                ]);
            } else {
                throw new \ErrorException('Could not create rbk customer, check full response ' . json_encode($customerData));
            }
        } else {
            $rbkCustomer = $this->createAccessToken($rbkCustomer);
        }

        $confirmations = $this->createUserConfirmations($user->email);

        $data = [
            'success' => true,
            'description' => 'Добавьте карту для удобства совершения платежей',
            'name' => $user->full_name,
            'email' => $user->email,
            'is_card_linked' => false,
            'customer' => [
                'customer_access_token' => $rbkCustomer->customer_access_token,
                'customer_id' => $rbkCustomer->customer_id,
            ],
        ];

        if ($orderForm) {
            /** @var UserConfirmation $webConfirmation */
            $webConfirmation = $confirmations->get('web_confirmation');

            $data['confirmation_url'] = $webConfirmation->getPaymentSuccessUrl();
            $data['registration_token'] = $webConfirmation->token;
        }

        if ($orderForm) {
            $data['description'] = 'Привязать карту для автоматической оплаты за '
                . $orderForm->product->title
                . ' раз в ' . $orderForm->product->getRecurrentTerm() . ' мес.';
        }

        return  $data;
    }

}

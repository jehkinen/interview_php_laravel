<?php

namespace App\Services\Payments\Rbk;

use App\Http\Services\User\UserConfirmationService;
use App\User;
use App\Product;
use App\OrderForm;
use App\TariffPlanTerm;
use App\UserConfirmation;
use App\AuthorSubscription;
use Illuminate\Support\Arr;
use App\Constants\OrderFormSource;
use App\Constants\OrderFormStatus;
use App\UserRecurrentSubscription;
use Illuminate\Support\Facades\Log;
use App\Http\Services\OrderFormService;
use App\Constants\SystemStripoEmailAliases;
use App\Http\Services\SystemStripoEmailService;
use App\Constants\UserRecurrentSubscriptionStatuses;

/**
 * Class RecurrentPaymentService.
 */
class RBkRecurrentPaymentService
{
    /** @var RbkMoneyService */
    protected $rbkMoneyService;

    /** @var RBKCustomerService */
    protected $rbkCustomerService;

    /** @var RecurrentCheckResult */
    protected $recurrentResult;

    protected $runViaCommand = false;

    private $userConfirmationService;

    public function __construct(
        RbkMoneyService $rbkMoneyService,
        RBKCustomerService $rbkCustomerService,
        RecurrentCheckResult $recurrentPaymentResult,
        UserConfirmationService $userConfirmationService
    ) {
        $this->rbkMoneyService = $rbkMoneyService;
        $this->rbkCustomerService = $rbkCustomerService;
        $this->recurrentResult = $recurrentPaymentResult;
        $this->userConfirmationService = $userConfirmationService;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setRunViaCommand($value)
    {
        $this->runViaCommand = $value;

        return $this;
    }

    public function createOrderFormAndCharge(UserRecurrentSubscription $userRecurrentSubscription)
    {
        $user = $userRecurrentSubscription->user;
        $product = $userRecurrentSubscription->product;

        $orderFormService = app()->make(OrderFormService::class);
        $orderFormService->setSource(OrderFormSource::RECURRENT_SUBSCRIPTION);
        $orderFormService->setUser($user);
        $orderForm = $orderFormService->createByUser($product->uid);

        return $this->charge($orderForm, $userRecurrentSubscription);
    }

    /**
     * Списать деньги с пользователя за подписку
     * Юзер может сам вызвать списание при нажатие на кнопку купить, если привязана карта
     * Или запускается по расписанию в день списания очередного платежа, в этом случае runViaCommand=true.
     *
     * @param OrderForm $orderForm
     * @param UserRecurrentSubscription|null $previousUserSubscription
     * @throws \Throwable
     * @return array|mixed
     */
    public function charge(
        OrderForm $orderForm,
        UserRecurrentSubscription $previousUserSubscription = null
    ) {
        try {
            $user = $orderForm->user;
            $product = $orderForm->product;

            if ($this->runViaCommand && ! $previousUserSubscription) {
                throw new \ErrorException(
                    'user subscription which should be extended did not provided, process will be terminated'
                );
            }

            if (! $this->runViaCommand) {
                $checkPossibleExtendResult = $this->checkIfPossibleToExtendWithoutCharge($user, $product);
                if ($checkPossibleExtendResult->isChargeAborted()) {
                    return $checkPossibleExtendResult->getResult();
                }
            }

            if ($this->runViaCommand) {
                $recurrentResult = $this->checkIfSubscriptionArchived($previousUserSubscription);
                if ($recurrentResult->isChargeAborted()) {
                    return $recurrentResult->getResult();
                }
            }

            $recurrentAlreadyExists = $this->checkIfSubscriptionExists($user, $product);

            /* Если подписка существует, то прекращаем покупку */
            if ($recurrentAlreadyExists->isChargeAborted()) {
                return $this->recurrentResult->getResult();
            }

            $checkCustomerResult = $this->checkIfCustomerValid($orderForm);

            if ($checkCustomerResult->isChargeAborted()) {
                return $checkCustomerResult->getResult();
            }

            $order = $this->rbkMoneyService->getOrderFormService()->createOrder($orderForm);
            $orderForm->load('order');

            $makeRbkInvoiceResult = $this->rbkMoneyService->makeInvoice($order);
            $order->refresh();


            $invoiceAccessToken = Arr::get($makeRbkInvoiceResult, 'invoiceAccessToken.payload');

            if (! $invoiceAccessToken) {
                throw new \ErrorException('Empty invoice access token, could not continue');
            }
            Arr::set($makeRbkInvoiceResult, 'invoice.invoiceAccessToken', $invoiceAccessToken);
            Arr::forget($makeRbkInvoiceResult, 'invoiceAccessToken');

            $this->rbkMoneyService->recurrentChargeByInvoice($order, $invoiceAccessToken);

            $startValidTo = now();

            $authorSubscriptionId = null;

            /** @var TariffPlanTerm|AuthorSubscription $productModel */
            $productModel = $product->model;

            if (!$productModel) {
                throw new \ErrorException('Could not process recurrent payment because product model is missed for product ' . $product->id);
            }


            $productModelClass = $product->model_type;

            switch ($productModelClass) {
                case TariffPlanTerm::shortClassName():
                    $validTo = $startValidTo->startOfDay()->addMonths($productModel->term_in_month);
                    break;
                case AuthorSubscription::shortClassName():
                    $validTo = $startValidTo->startOfDay()->addMonths($productModel->term_in_month);
                    $authorSubscriptionId = $product->model_id;
                    break;
                default:
                    throw new \ErrorException('Could not process this model class during recurrent payment ' . $productModelClass);
            }

            $recurrentSubcription = $orderForm->user->recurrentSubscriptions()->firstOrCreate([
                'product_id' => $product->id,
            ]);


            $recurrentSubcription->update([
                'status' => UserRecurrentSubscriptionStatuses::WAITING_FOR_PAYMENT,
                'valid_to' => $validTo,
                'author_subscription_id' => $authorSubscriptionId,
            ]);


            $orderForm->update([
                'status' => OrderFormStatus::WAITING_FOR_PAYMENT,
            ]);

            /** @var UserConfirmation $webConfirmation */
            $webConfirmation = $this->userConfirmationService->createWebAndEmail($orderForm);
            $makeRbkInvoiceResult['confirmation_url'] = $webConfirmation->getPaymentSuccessUrl();
            $makeRbkInvoiceResult['registration_token'] = $webConfirmation->token;
            $makeRbkInvoiceResult['order_id'] = $order->id;
            $makeRbkInvoiceResult['is_card_linked'] = true;

            return $makeRbkInvoiceResult;
        } catch (\Throwable $throwable) {
            Log::error('Charge failed', [
                'error' => $throwable->getMessage(),
                'stack_trace' => $throwable->getTraceAsString(),
            ]);
            throw $throwable;
        }
    }

    /**
     * Проверить, есть ли у пользователя отмененная но не истекшая подписка, которую можно продлить без списания средств.
     * В этом случае ставим ей статус активной, можно поставить статус активной
     * Но только в том случае, если пользователь сам вызвал подписку (нажал кноку купить).
     * В случае если это был вызов из джобы (runViaCommand), то подписка не продлится.
     *
     * @param User $user
     * @param Product $product
     * @return RecurrentCheckResult
     */
    private function checkIfPossibleToExtendWithoutCharge(User $user, Product $product)
    {
        /** Если подписка была отменена, но срок не вышел подписки, то просто возобновляем ее */
        $updateRecurrentStatus = $user->recurrentSubscriptions()
            ->where('product_id', $product->id)
            ->whereDate('valid_to', '>', now())
            ->where('status', UserRecurrentSubscriptionStatuses::CANCELLED)
            ->update([
                'status' => UserRecurrentSubscriptionStatuses::ACTIVE,
            ]);

        if ($updateRecurrentStatus) {
            return $this->recurrentResult
                ->setResult([
                    'success' => true,
                    'message' => 'Подписка возобновлена',
                ])
                ->abortCharge();
        }

        return $this->recurrentResult;
    }

    /**
     * Проверить если автор удалил подписку или сделал неактивной (is_active=false),
     * то заархивировать подписку пользователя, в том случае если ее срок уже истек.
     *
     * @param UserRecurrentSubscription $userRecurrentSubscription
     * @return RecurrentCheckResult
     */
    private function checkIfSubscriptionArchived(UserRecurrentSubscription $userRecurrentSubscription)
    {
        $product = $userRecurrentSubscription->product;

        if ($userRecurrentSubscription->status === UserRecurrentSubscriptionStatuses::EXPIRED) {
            $authorRecurrentSubscriptionArchived = AuthorSubscription::query()
                ->whereHas('product', function ($query) use ($product) {
                    $query->where('id', $product->id);
                })
                ->whereNotNull('deleted_at')
                ->exists();

            $authorRecurrentSubscriptionNotActive = AuthorSubscription::query()
                ->whereHas('product', function ($query) use ($product) {
                    $query->where('id', $product->id);
                    $query->where('is_active', false);
                })
                ->exists();

            /*
             * Если подписка была удалена у автора, то анулируем текущую подписку,
             * запрос на рбк мани на списание реккурента не отправляется
             */
            if ($authorRecurrentSubscriptionArchived || $authorRecurrentSubscriptionNotActive) {
                if ($userRecurrentSubscription) {
                    $userRecurrentSubscription->update([
                        'status' => UserRecurrentSubscriptionStatuses::ARCHIVED,
                    ]);
                }

                return $this->recurrentResult
                    ->setResult([
                        'success' => false,
                        'message' => 'Эта подписка больше недоступна',
                    ])
                    ->abortCharge();
            }
        }

        return $this->recurrentResult;
    }

    /**
     * Проверить кастомера рбк-мани,.
     * @param OrderForm $orderForm
     * @throws \ErrorException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return RecurrentCheckResult
     */
    private function checkIfCustomerValid(OrderForm $orderForm)
    {
        $user = $orderForm->user;
        /** @var AuthorSubscription $authorSubscription */
        $authorSubscription = $orderForm->product->model;

        if (! $user->rbkCustomer || ! $this->rbkCustomerService->checkAndUpdate($user->rbkCustomer)) {
            $systemStripoEmailService = app()->make(SystemStripoEmailService::class);
            $systemStripoEmailService->sendByAlias(
                $user,
                SystemStripoEmailAliases::SUBSCRIPTION_WILL_BE_CANCELLED,
                [
                    'subscription_name' => $authorSubscription->title,
                    'subscription_price' => $authorSubscription->price,
                ]
            );

            return $this->recurrentResult
                ->abortCharge()
                ->setResult([
                    'success' => false,
                    'message' => 'Пожалуйста обновите данные карты',
                ]);
        }

        return $this->recurrentResult;
    }

    /**
     * Если активная подписка уже есть на этот продукт и она не истекла, просто выходим
     *
     * @param OrderForm $orderForm
     * @param User $user
     * @param Product $product
     * @throws \Illuminate\Validation\ValidationException
     */
    private function checkIfSubscriptionExists(User $user, Product $product)
    {
        $recurrentExists = $user->recurrentSubscriptions()
            ->where('product_id', $product->id)
            ->whereDate('valid_to', '>', now())
            ->where('status', UserRecurrentSubscriptionStatuses::ACTIVE)
            ->exists();

        if ($recurrentExists) {
            if ($this->runViaCommand) {
                throw new \ErrorException(
                    'Something goes very bad, it`s prohibited to charge users via recurrent job,
                     if they have the active subscription on this product ' . $user->id . ' ' . $product->id
                );
            }

            return $this->recurrentResult->abortCharge()->setResult([
                    'success' => false,
                    'message' => 'У вас уже есть активная подписка на этот продукт',
                ]);
        }

        return $this->recurrentResult;
    }
}

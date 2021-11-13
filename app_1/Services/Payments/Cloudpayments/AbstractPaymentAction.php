<?php


namespace App\Services\Payments\Cloudpayments;


abstract class AbstractPaymentAction
{

    protected $orderForm;

    abstract public function pay(array $requestArray);


    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getOrderForm()
    {
        return $this->orderForm;
    }

    /**
     * @param mixed $orderForm
     */
    public function setOrderForm($orderForm): void
    {
        $this->orderForm = $orderForm;
    }


}
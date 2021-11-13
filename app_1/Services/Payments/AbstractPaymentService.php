<?php


namespace App\Services\Payments;


use App\Constants\InvoiceStatus;
use App\Constants\OrderStatus;
use App\Order;
use App\OrderForm;

class AbstractPaymentService
{

    protected $orderId;
    protected $paymentId;
    protected $requestArray;


    /**
     * @param OrderForm $orderForm
     * @throws \ErrorException
     * @return Order
     */
    protected function fetchOrCreateOrder(OrderForm $orderForm, $jsonData = [])
    {
        if ($orderForm->order) {
            return $orderForm->order;
        }

        $order = new Order();
        $order->user_id = $orderForm->user_id;
        $order->product_id = $orderForm->product_id;
        $order->status = OrderStatus::CREATED;
        $order->payment_id = $this->paymentId;
        $order->order_form_id = $orderForm->id;
        $order->save();

        $invoice = $order->invoice;

        if (! $invoice) {
            $order->invoice()->create([
                'user_id' => $order->user_id,
                'status' => InvoiceStatus::CREATED,
                'json_data' => $jsonData
            ]);
            $order->load('invoice');
        }

        $this->setOrderId($order->id);

        return $order;
    }


    protected function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    protected function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }


}
<?php


namespace App\Services\Payments\Cloudpayments;


use App\CourseModule;
use App\CoursePack;
use App\OrderForm;
use App\Product;

class CloudpaymentsWidgetDataProvider
{

    public function data(OrderForm $orderForm)
    {
        $product = $orderForm->product;
        $settings = $product->cloudPaymentsSettings;

        if (!$product->isStudentProduct()) {
            return false;
        }
        if (in_array($product->model_type, [CoursePack::class, CourseModule::class])) {
            return false;
        }

        if (!$settings) {
            return false;
        }

        $startDate = now()->addDays($settings->trial_in_days)->endOfDay()->format('Y-m-d H:i:s');
        $recurrent = [
            'startDate' => $startDate,
            'interval' => $settings->recurrent_interval,
            'period' => $settings->recurrent_period,
            'amount' => $settings->recurrent_price
        ];

        $productDescription = $this->getProductDescription($product);

        $receipt = [
            'Items' => [
              [
                'label' => $productDescription,
                'price' => $settings->trial_price,
                'quantity' => 1.0, // количество
                'amount' => $settings->trial_price, // сумма
                'vat' => 0, // ставка НДС,
                'method' => 4,
                'object' => 12,
                'TaxationSystem' => 2,
                'AgentSign' => 6,
              ],
            ],
            'email' => $orderForm->email,
          ];


        $widget = [
            'cloudpayments_widget' => [
                'publicId' => config('cloudpayments.public_key'),
                'description' => $productDescription,
                'amount' => $settings->trial_price,
                'currency' => 'RUB',
                'invoiceId' => $orderForm->id,
                'accountId' => $orderForm->user_id,
                'data' => [
                    'isTrial' => true,
                    'CloudPayments' => [
                        'CustomerReceipt' => $receipt,
                        'recurrent' => $recurrent,
                    ]],
                'email' => $orderForm->email,
            ]
        ];

        return collect($widget);
    }

    public function getProductDescription(Product $product)
    {
        $settings = $product->cloudPaymentsSettings;
        return trans('cloudpayments.description', [
            'product_title' => $product->title,
            'trial_in_days' => $settings->trial_in_days,
            'trial_price' => $settings->trial_price,
            'recurrent_price' => $settings->recurrent_price,
            'recurrent_period' => $settings->recurrent_period,
            'recurrent_interval' => $settings->recurrent_interval,
            'currency' => 'RUB'
        ]);
    }


}
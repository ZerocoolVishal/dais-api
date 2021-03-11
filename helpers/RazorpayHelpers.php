<?php


namespace app\helpers;


use app\models\Payments;
use Razorpay\Api\Api;
use Yii;
use yii\web\Response;

class RazorpayHelpers
{

    public static function pay(
        $payment_id,
        $payment_amount,
        $payment_name,
        $payment_description,
        $user_name = null,
        $user_email = null,
        $user_phone = null,
        $currency_code = null
    ) {

        $keyId = \Yii::$app->params['keyId'];
        $keySecret = \Yii::$app->params['keySecret'];
        $displayCurrency = ($currency_code != null)? $currency_code : Yii::$app->params['displayCurrency'];

        $api = new Api($keyId, $keySecret);

        $paymentModel = Payments::findOne($payment_id);

        $orderData = [
            'receipt' => $payment_id,
            'amount' => $payment_amount * 100,
            'currency' => $displayCurrency,
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $api->order->create($orderData);

        $razorpayOrderId = $razorpayOrder['id'];

        $paymentModel->razorpay_order_id = $razorpayOrderId;
        $paymentModel->save(false);

        Yii::$app->session->set('razorpay_order_id', $razorpayOrderId);

        $payment_amount = $orderData['amount'];

        return [
            "key" => $keyId,
            "amount" => $payment_amount,
            "currency" => $displayCurrency,
            "name" => $payment_name,
            "description" => $payment_description,
            "order_id" => $razorpayOrderId,
            "prefill" => [
                "name" => $user_name,
                "email" => $user_email,
                "contact" => $user_phone,
            ],
            "notes" => [
                "address" => "",
                "merchant_order_id" => "",
            ],
            "theme" => [
                "color" => "#000"
            ],
        ];
    }

}

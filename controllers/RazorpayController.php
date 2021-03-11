<?php

namespace app\controllers;

use app\helpers\AppHelper;
use app\models\FeasibilityReport;
use app\models\Payments;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Payment;
use Yii;
use yii\web\Response;

class RazorpayController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionVerify()
    {

        $session = Yii::$app->session;

        $keyId = \Yii::$app->params['keyId'];
        $keySecret = \Yii::$app->params['keySecret'];

        $success = true;

        if (empty($_POST['razorpay_payment_id']) === false) {

            $api = new Api($keyId, $keySecret);

            try {
                $attributes = [
                    'razorpay_order_id' => $session->get('razorpay_order_id'),
                    'razorpay_payment_id' => Yii::$app->request->post('razorpay_payment_id'),
                    'razorpay_signature' => Yii::$app->request->post('razorpay_signature'),
                ];
                $api->utility->verifyPaymentSignature($attributes);
            }
            catch(SignatureVerificationError $e) {
                $success = false;
            }

            if ($success === true) {

                $paymentModel = Payments::findOne(['razorpay_order_id' => $attributes['razorpay_order_id']]);
                $paymentModel->is_processed = Payments::PAYMENT_SUCCESS;
                $paymentModel->razorpay_payment_id = $attributes['razorpay_payment_id'];
                $paymentModel->razorpay_signature = $attributes['razorpay_signature'];
                $paymentModel->save(false);

                // IF Payment is done for Report
                if ($paymentModel->payment_type === Payments::FEASIBILITY_REPORT) {

                    $reportModel = FeasibilityReport::findOne($paymentModel->payment_type_id);
                    $reportModel->is_paid = 1;
                    $reportModel->is_payment_processed = FeasibilityReport::PAYMENT_SUCCESS;
                    $reportModel->save(false);
                }
            }
        }
    }


}

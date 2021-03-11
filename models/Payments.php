<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_payments".
 *
 * @property int $payment_id
 * @property string $payment_type
 * @property int $payment_type_id
 * @property float $total_amount
 * @property string $payment_date
 * @property int $is_processed
 * @property string|null $currency_code
 * @property string|null $razorpay_order_id
 * @property string|null $razorpay_payment_id
 * @property string|null $razorpay_signature
 */
class Payments extends \yii\db\ActiveRecord
{
    const BOOK_MEETING = 'BM';
    const FEASIBILITY_REPORT = 'BE';

    const NO_PAYMENT = 0;
    const PAYMENT_INIT = 1;
    const PAYMENT_SUCCESS = 2;
    const PAYMENT_FAILED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_type', 'payment_type_id', 'total_amount', 'payment_date'], 'required'],
            [['payment_type'], 'string'],
            [['payment_type_id', 'is_processed'], 'integer'],
            [['total_amount'], 'number'],
            [['payment_date'], 'safe'],
            [['currency_code'], 'string', 'max' => 3],
            [['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_id' => 'Payment ID',
            'payment_type' => 'Payment Type',
            'payment_type_id' => 'Payment Type ID',
            'total_amount' => 'Total Amount',
            'payment_date' => 'Payment Date',
            'is_processed' => 'Is Processed',
            'currency_code' => 'Currency Code',
            'razorpay_order_id' => 'Razorpay Order ID',
            'razorpay_payment_id' => 'Razorpay Payment ID',
            'razorpay_signature' => 'Razorpay Signature',
        ];
    }
}

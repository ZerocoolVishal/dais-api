<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_meeting".
 *
 *  statue => [0 => Not Confirmed, 1 => Confirmed, 2 => Done, 3 => Cancel]
 *
 * @property int $meeting_id
 * @property int $user_id
 * @property string $society_name
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_designation
 * @property int $status
 * @property string $visit_date
 * @property int|null $payment_id
 * @property float $amount
 * @property string $created_at
 *
 * @property Users $user
 */
class Meeting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_meeting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'society_name', 'contact_name', 'contact_phone', 'contact_designation', 'visit_date', 'created_at'], 'required'],
            [['user_id', 'status', 'payment_id'], 'integer'],
            [['visit_date', 'created_at'], 'safe'],
            [['amount'], 'number'],
            [['society_name', 'contact_name', 'contact_designation'], 'string', 'max' => 255],
            [['contact_phone'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'meeting_id' => 'Meeting ID',
            'user_id' => 'User ID',
            'society_name' => 'Society Name',
            'contact_name' => 'Contact Name',
            'contact_phone' => 'Contact Phone',
            'contact_designation' => 'Contact Designation',
            'status' => 'Status',
            'visit_date' => 'Visit Date',
            'payment_id' => 'Payment ID',
            'amount' => 'Amount',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}

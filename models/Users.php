<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_user".
 *
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property int $is_phone_verified
 * @property int $is_email_verified
 * @property int $is_social_register
 * @property string|null $social_register_type
 * @property string|null $gender
 * @property string|null $dob
 * @property string|null $otp_code
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'created_at'], 'required'],
            [['is_phone_verified', 'is_email_verified', 'is_social_register', 'is_active', 'is_deleted'], 'integer'],
            [['social_register_type', 'gender'], 'string'],
            [['dob', 'created_at'], 'safe'],
            [['name', 'email', 'password'], 'string', 'max' => 255],
            [['phone', 'otp_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'is_phone_verified' => 'Is Phone Verified',
            'is_email_verified' => 'Is Email Verified',
            'is_social_register' => 'Is Social Register',
            'social_register_type' => 'Social Register Type',
            'gender' => 'Gender',
            'dob' => 'Dob',
            'otp_code' => 'Otp Code',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
        ];
    }
}

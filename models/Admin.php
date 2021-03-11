<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_admin".
 *
 * @property int $admin_id
 * @property string|null $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property int $is_active
 * @property int $is_deleted
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['is_active', 'is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => 'Admin ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone' => 'Phone',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }
}

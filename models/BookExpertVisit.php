<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_book_expert_visit".
 *
 * statue => [0 => Not Confirm, 1 => Confirm, 2 => Visited, 3 => Cancelled]
 *
 * @property int $book_expert_visit_id
 * @property int $user_id
 * @property string $society_name
 * @property string $society_type
 * @property string $society_address
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_designation
 * @property int $status
 * @property string|null $expert_name
 * @property string|null $expert_phone
 * @property string $visit_date
 * @property string $created_at
 *
 * @property Users $user
 */
class BookExpertVisit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_book_expert_visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'society_name', 'society_type', 'society_address', 'contact_name', 'contact_phone', 'contact_designation', 'visit_date', 'created_at'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['society_address'], 'string'],
            [['visit_date', 'created_at'], 'safe'],
            [['society_name', 'society_type', 'contact_name', 'contact_designation', 'expert_name'], 'string', 'max' => 255],
            [['contact_phone'], 'string', 'max' => 20],
            [['expert_phone'], 'string', 'max' => 15],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'book_expert_visit_id' => 'Book Expert Visit ID',
            'user_id' => 'User ID',
            'society_name' => 'Society Name',
            'society_type' => 'Society Type',
            'society_address' => 'Society Address',
            'contact_name' => 'Contact Name',
            'contact_phone' => 'Contact Phone',
            'contact_designation' => 'Contact Designation',
            'status' => 'Status',
            'expert_name' => 'Expert Name',
            'expert_phone' => 'Expert Phone',
            'visit_date' => 'Visit Date',
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

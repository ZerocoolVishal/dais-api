<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_feasibility_report".
 *
 * @property int $feasibility_report_id
 * @property int $user_id
 * @property string|null $cts_no
 * @property string|null $location
 * @property string|null $ward
 * @property string|null $village
 * @property string|null $plot_size
 * @property float|null $existing_built_up_area
 * @property float|null $residential_redirecionar_rate
 * @property int|null $no_of_tenants
 * @property float|null $area_currently_consumed
 * @property int|null $additional_area_expected
 * @property float|null $total_area
 * @property string $society_name
 * @property string $society_type
 * @property string $society_address
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_designation
 * @property int $is_paid
 * @property int $is_payment_processed
 * @property string|null $created_at
 *
 * @property Users $user
 */
class FeasibilityReport extends \yii\db\ActiveRecord
{
    const NO_PAYMENT = 0;
    const PAYMENT_INIT = 1;
    const PAYMENT_SUCCESS = 2;
    const PAYMENT_FAILED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_feasibility_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'society_name', 'society_type', 'society_address', 'contact_name', 'contact_phone', 'contact_designation'], 'required'],
            [['user_id', 'no_of_tenants', 'additional_area_expected', 'is_paid', 'is_payment_processed'], 'integer'],
            [['residential_redirecionar_rate', 'area_currently_consumed', 'total_area', 'existing_built_up_area', 'plot_size'], 'number'],
            [['created_at'], 'safe'],
            [['cts_no', 'location', 'ward', 'village', 'society_name', 'society_type', 'society_address', 'contact_name', 'contact_phone', 'contact_designation'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'feasibility_report_id' => 'Feasibility Report ID',
            'user_id' => 'User ID',
            'cts_no' => 'Cts No',
            'location' => 'Location',
            'ward' => 'Ward',
            'village' => 'Village',
            'plot_size' => 'Plot Size',
            'existing_built_up_area' => 'Existing Built-up Area',
            'residential_redirecionar_rate' => 'Residential Redirecionar Rate',
            'no_of_tenants' => 'No Of Tenants',
            'area_currently_consumed' => 'Area Currently Consumed',
            'additional_area_expected' => 'Additional Area Expected',
            'total_area' => 'Total Area',
            'society_name' => 'Society Name',
            'society_type' => 'Society Type',
            'society_address' => 'Society Address',
            'contact_name' => 'Contact Name',
            'contact_phone' => 'Contact Phone',
            'contact_designation' => 'Contact Designation',
            'is_paid' => 'Is Paid',
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

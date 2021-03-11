<?php

namespace app\models;

use himiklab\sortablegrid\SortableGridBehavior;
use Yii;

/**
 * This is the model class for table "tbl_service".
 *
 * @property int $service_id
 * @property string $name
 * @property string|null $title
 * @property string|null $description
 * @property int $type
 * @property string|null $image
 * @property string|null $bg_color
 * @property string|null $text_color
 * @property string|null $secndary_text_color
 * @property string|null $button_bg_color
 * @property string|null $button_text_color
 * @property int $link_type
 * @property string|null $link
 * @property int|null $sort_order
 * @property string $created_at
 * @property int $is_active
 * @property int $is_deleted
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'link_type', 'sort_order', 'is_active', 'is_deleted'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'title', 'description', 'image', 'bg_color', 'text_color', 'secndary_text_color', 'button_bg_color', 'button_text_color', 'link'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_id' => 'Service ID',
            'name' => 'Name',
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'image' => 'Image',
            'bg_color' => 'Background Color',
            'text_color' => 'Text Color',
            'secndary_text_color' => 'Secondary Text Color',
            'button_bg_color' => 'Button Background Color',
            'button_text_color' => 'Button Text Color',
            'link_type' => 'Link Type',
            'link' => 'Link',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }


}

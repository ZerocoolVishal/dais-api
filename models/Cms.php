<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_cms".
 *
 * @property int $cms_id
 * @property string $title
 * @property string $content
 * @property int $is_active
 */
class Cms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_cms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['is_active'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cms_id' => 'Cms ID',
            'title' => 'Title',
            'content' => 'Content',
            'is_active' => 'Is Active',
        ];
    }
}

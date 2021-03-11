<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Service */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="service-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->service_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->service_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'service_id',
            'name',
            'title',
            'description',
            [
                'attribute' => 'type',
                'value' => function (\app\models\Service $model) {
                    $types = \app\helpers\AppHelpers::getServiceTypes();
                    return (isset($types[$model->type]))? $types[$model->type] : $model->type;
                }
            ],
            'image',
            'bg_color',
            'text_color',
            'secndary_text_color',
            'button_bg_color',
            'button_text_color',
            'link_type',
            'link:url',
            'sort_order',
            'created_at',
            [
                'attribute' => 'is_active',
                'value' => function ($model) {
                    return ($model->is_active)? 'Yes' : 'No';
                }
            ],
        ],
    ]) ?>

</div>

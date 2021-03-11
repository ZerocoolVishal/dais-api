<?php

use himiklab\sortablegrid\SortableGridView;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Service', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= SortableGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'service_id',
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
            [
                'attribute' => 'image',
                'format' => 'html',
                'value' => function (\app\models\Service $model) {
                    return Html::img("@web/uploads/$model->image", ['width' => '120px']);
                }
            ],
            //'bg_color',
            //'text_color',
            //'secndary_text_color',
            //'button_bg_color',
            //'button_text_color',
            //'link_type',
            //'link',
            //'sort_order',
            //'created_at',
            //'is_active',
            //'is_deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

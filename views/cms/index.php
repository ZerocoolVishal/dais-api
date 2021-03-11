<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cms', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cms_id',
            'title',
            'content:ntext',
            'is_active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

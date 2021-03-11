<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FeasibilityReport */
/* @var $form ActiveForm */
?>
<div class="site-_fisibility_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'no_of_tenants') ?>
        <?= $form->field($model, 'created_at') ?>
        <?= $form->field($model, 'cts_no') ?>
        <?= $form->field($model, 'ward') ?>
        <?= $form->field($model, 'village') ?>
        <?= $form->field($model, 'plot_size') ?>
        <?= $form->field($model, 'residential_redirecionar_rate') ?>
        <?= $form->field($model, 'area_currently_consumed') ?>
        <?= $form->field($model, 'society_name') ?>
        <?= $form->field($model, 'society_type') ?>
        <?= $form->field($model, 'society_address') ?>
        <?= $form->field($model, 'contact_name') ?>
        <?= $form->field($model, 'contact_phone') ?>
        <?= $form->field($model, 'contact_designation') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-_fisibility_form -->

<?php
/* @var $this yii\web\View */

use yii\bootstrap4\ActiveForm;
use \yii\bootstrap4\Html;

?>
<div class="page">
    <?= Html::a("<i class=\"fa fa-arrow-left mr-2 mb-3\" aria-hidden=\"true\"></i> Back", ['services'], ['class' => 'text-dark']) ?>
    <h3 class="text-uppercase font-weight-bold">Get Your Feasibility Details</h3>
    <div class="mt-4">
        <?php $form = ActiveForm::begin(); ?>
        <div class="font-weight-bold mb-4">Plot Details :</div>

        <?= $form->field($model, 'plot_size') ?>
        <?= $form->field($model, 'residential_redirecionar_rate') ?>
        <?= $form->field($model, 'no_of_tenants')->input('number') ?>
        <?= $form->field($model, 'area_currently_consumed') ?>
        <?= $form->field($model, 'cts_no') ?>
        <?= $form->field($model, 'ward') ?>
        <?= $form->field($model, 'village') ?>

        <div class="font-weight-bold mb-4">Your Society Details :</div>
        <?= $form->field($model, 'society_name') ?>
        <?= $form->field($model, 'society_type')->dropdownList([
                'Landlord' => 'Landlord',
                'Society Own Plot' => 'Society Own Plot',
        ]) ?>
        <?= $form->field($model, 'society_address') ?>
        <?= $form->field($model, 'contact_name') ?>
        <?= $form->field($model, 'contact_phone') ?>
        <?= $form->field($model, 'contact_designation')->dropdownList([
                'Secretary' => 'Secretary',
                'Chairmen' => 'Chairmen',
                'Treasurer' => 'Treasurer',
                'Community Member' => 'Community Member',
        ]) ?>

        <div class="form-group mt-4">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-brand btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

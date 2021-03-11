<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_phone_verified')->textInput() ?>

    <?= $form->field($model, 'is_email_verified')->textInput() ?>

    <?= $form->field($model, 'is_social_register')->textInput() ?>

    <?= $form->field($model, 'social_register_type')->dropDownList([ 'G' => 'G', 'F' => 'F', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'gender')->dropDownList([ 'M' => 'M', 'F' => 'F', 'O' => 'O', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'dob')->textInput() ?>

    <?= $form->field($model, 'otp_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

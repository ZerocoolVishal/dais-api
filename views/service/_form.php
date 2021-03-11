<?php

use dosamigos\fileupload\FileUpload;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Service */
/* @var $form yii\widgets\ActiveForm */

$types = app\helpers\AppHelpers::getServiceTypes();

?>

<div class="service-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList($types, ['prompt' => 'select type']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'bg_color')->input('color', ['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'text_color')->input('color', ['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'secndary_text_color')->input('color', ['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'button_bg_color')->input('color', ['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'button_text_color')->input('color', ['maxlength' => true]) ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6">
            <label>
                Image
            </label> <br>

            <?= FileUpload::widget([
                'name' => 'Service[image]',
                'url' => [
                    'upload/common?attribute=Service[image]'
                ],
                'options' => [
                    'accept' => 'image/*',
                ],
                'clientOptions' => [
                    'dataType' => 'json',
                    'maxFileSize' => 2000000,
                ],
                'clientEvents' => [
                    'fileuploadprogressall' => "function (e, data) {
                                        var progress = parseInt(data.loaded / data.total * 100, 10);
                                        $('#progress').show();
                                        $('#progress .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="logoImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview").html(img);
                                            $("#service-image").val(data.result.files.name);
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress .progress-bar").attr("style","width: 0%;");
                                           $("#progress").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]) ?>

            <div id="progress" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>
            <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

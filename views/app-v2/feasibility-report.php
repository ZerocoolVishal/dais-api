<?php
/**
 * @var $this yii\web\View
 * @var $feasibility string
 * @var $road_width string
 * @var $plot_size string
 * @var $basic_fsi string
 * @var $reservation string
 * @var $total_built_up_area string
 */

use yii\bootstrap4\ActiveForm;
use \yii\bootstrap4\Html;

?>
<div class="page">
    <?= Html::a("<i class=\"fa fa-arrow-left mr-2 mb-3\" aria-hidden=\"true\"></i> Back", ['services'], ['class' => 'text-dark']) ?>
    <h3 class="text-uppercase font-weight-bold">Your Feasibility Report</h3>
    <div class="mt-3">
        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15084.842140987235!2d72.82089952780177!3d19.054478843602684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c8e123f8d27b%3A0x437996b49a236a78!2sBandra%20West%2C%20Mumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1607404396489!5m2!1sen!2sin" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
        <div class="bg-black p-3">
            <div>Your Feasibility Is</div>
            <h3><?= $feasibility ?></h3>
        </div>
    </div>
    <div class="row p-0 mt-4">
        <div class="col-6 p-0">
            <div class="box">
                <div class="box-title">ROAD WIDTH</div>
                <div class="box-value"><?= $road_width ?></div>
            </div>
        </div>
        <div class="col-6 p-0">
            <div class="box">
                <div class="box-title">PLOT SIZE</div>
                <div class="box-value"><?= $plot_size ?></div>
            </div>
        </div>
        <div class="col-6 p-0">
            <div class="box">
                <div class="box-title">BASIC FSI</div>
                <div class="box-value"><?= $basic_fsi ?></div>
            </div>
        </div>
        <div class="col-6 p-0">
            <div class="box">
                <div class="box-title">RESERVATION</div>
                <div class="box-value"><?= $reservation ?></div>
            </div>
        </div>
        <div class="col-12 p-0">
            <div class="box">
                <div class="box-title">TOTAL BUILT-UP AREA</div>
                <div class="box-value"><?= $total_built_up_area ?></div>
            </div>
        </div>
    </div>
    <div class="my-4">
        <?= Html::a('DETAILED REPORT', ['#'], ['class' => 'btn btn-block btn-brand py-3']) ?>
    </div>
</div>

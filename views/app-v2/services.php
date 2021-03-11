<?php
/* @var $this yii\web\View */

use \yii\bootstrap4\Html;

?>
<div class="page">
    <h3 class="text-uppercase font-weight-bold">Services</h3>
    <div class="w-100 mb-0">
        <?= Html::img('@web/images/services.jpg', ['width' => '100%', 'class' => 'mt-5']) ?>
    </div>
    <div class="row">
        <div class="col-md-12 mt-4">
            <a href="<?= \yii\helpers\Url::to(['app-v2/feasibility-form']) ?>">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Get Your Feasibility Details</h2>
                        <div class="row">
                            <div class="col-6 text-left font-weight-light">For FREE</div>
                            <div class="col-6 text-right"><i class="fa fa-arrow-right ml-3" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Consult an Architect</h2>
                    <div class="row">
                        <div class="col-6 text-left font-weight-light">For FREE</div>
                        <div class="col-6 text-right"><i class="fa fa-arrow-right ml-3" aria-hidden="true"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

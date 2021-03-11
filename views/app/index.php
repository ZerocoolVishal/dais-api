<?php
/* @var $this yii\web\View */
/* @var $services \app\models\Service[] */

use \yii\bootstrap4\Html;

$this->title = 'Devtaa AI Services';

?>
<div class="row mt-3">
    <?php foreach ($services as $service): ?>

        <?php
            $link = null;
            if ($service->type == 1) {
                $link = \yii\helpers\Url::to([$service->link]);
            }
            else if ($service->type == 2) {
                $link = $service->link;
            }
            else if ($service->type == 3) {
                $link = \yii\helpers\Url::to(['app/cms', 'id' => $service->link]);
            }
        ?>

        <div class="col-md-12 mb-4">

                <div class="card border-0 rounded">

                <a class="card-link" <?= ($link)? "href=\"$link\"" : "" ?>>
                    <?php if(!empty($service->image)): ?>
                        <?= Html::img("@web/uploads/$service->image", ['class' => 'card-img-top', 'alt' => $service->title]) ?>
                    <?php endif; ?>
                </a>

                <?php if(!empty($service->title) || !empty($service->description)): ?>

                <div class="card-body  shadow-sm" style="<?php
                    echo ($service->bg_color)? "background-color: $service->bg_color;" : "";
                    echo ($service->text_color)? "color: $service->text_color;" : "";
                ?>">

                    <?php if(!empty($service->title)): ?>
                        <h5 class="card-title"><?= $service->title ?></h5>
                    <?php endif; ?>
                    <?php if(!empty($service->description)): ?>
                        <p class="card-text"><?= $service->description ?></p>
                    <?php endif; ?>

                    <?php if(!empty($service->link)): ?>
                        <a href="<?= $service->link ?>" class="btn btn-primary border-0" style="<?php
                        echo ($service->button_bg_color)? "background-color: $service->button_bg_color;" : "";
                        echo ($service->button_text_color)? "color: $service->button_text_color;" : "";
                        ?>">Know more</a>
                    <?php endif; ?>
                </div>

                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>
</div>

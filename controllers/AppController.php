<?php

namespace app\controllers;

use app\models\Service;

class AppController extends \yii\web\Controller
{
    public $layout = 'app_main';

    public function actionIndex()
    {
        $services = Service::find()
            ->where(['is_active' => 1, 'is_deleted' => 0])
            ->orderBy(['sort_order' => 'ASC'])
            ->all();
        return $this->render('index', ['services' => $services]);
    }

}

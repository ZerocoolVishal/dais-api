<?php

namespace app\controllers;

use app\models\FeasibilityReport;
use Yii;

class AppV2Controller extends \yii\web\Controller
{
    public $layout = 'app_main';

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionServices()
    {
        return $this->render('services');
    }

    public function actionFeasibilityForm()
    {
        $model = new \app\models\FeasibilityReport();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['feasibility-report', 'id' => $model->feasibility_report_id]);
            }
        }
        return $this->render('feasibility-form', ['model' => $model]);
    }

    public function actionFeasibilityReport($id)
    {
        $model = FeasibilityReport::findOne($id);

        if(empty($model)) {
            return $this->redirect(['index']);
        }

        $fsi = 2.7;
        $built_up_area = ($fsi * ((float) $model->plot_size));

        $feasibility_rate = ($built_up_area * 100000) / 100000 * 0.02;
        $feasibility = "₹ $feasibility_rate Cr**";
        if ($feasibility_rate) {
            $feasibility_rate = ($built_up_area * 100000) / 1000 * 0.02;
            $feasibility = "₹ $feasibility_rate Lac**";
        }

        $road_width = '12 Mtr';
        $plot_size = $model->plot_size . ' sqmt';
        $basic_fsi = $fsi;
        $reservation = 'No';
        $total_built_up_area = $built_up_area . ' sqmt';

        return $this->render('feasibility-report', [
            'feasibility' => $feasibility,
            'road_width' => $road_width,
            'plot_size' => $plot_size,
            'basic_fsi' => $basic_fsi,
            'reservation' => $reservation,
            'total_built_up_area' => $total_built_up_area,
        ]);
    }

}

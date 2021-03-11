<?php

namespace app\controllers;

use yii\helpers\BaseFileHelper;
use yii\web\Response;
use yii\web\UploadedFile;

class UploadController extends \yii\web\Controller
{

    public function actionCommon($attribute) {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $imageFile = UploadedFile::getInstanceByName($attribute);
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR;
        if ($imageFile) {

            $filetype = BaseFileHelper::getMimeType($imageFile->tempName);
            $allowed = array('image/png', 'image/jpeg', 'image/gif');

            if (!in_array(strtolower($filetype), $allowed)) {
                return [
                    'files' => [
                        'error' => "File type not supported",
                    ]
                ];
            }
            else {
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $imageFile->extension;
                $filePath = $directory . $fileName;
                if ($imageFile->saveAs($filePath)) {
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . $fileName;

                    return [
                        'files' => [
                            'name' => $fileName,
                            'size' => $imageFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $fileName,
                            "deleteType" => "POST",
                            'error' => ""
                        ]
                    ];
                }
            }
        }
        return '';
    }
}

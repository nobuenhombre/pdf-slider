<?php

namespace app\controllers;

use yii\helpers\VarDumper;
use app\components\PdfConverter;
use app\components\SliderStore;
use app\models\UploadForm;
use Yii;
use yii\helpers\BaseArrayHelper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['getimg'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function runAction($id, $params = [])
    {
        // Extract the params from the request and bind them to params
        $params = BaseArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
        return parent::runAction($id, $params);
    }

    /**
     * Ajax экшн - возвращает прогресс и статус
     * на вход приходит id - от загрузки файла - передается через data-id атрибут
     * @param string $id
     * @return \stdClass
     */
    public function actionGetimg(string $id)
    {
        $result = new \stdClass();
        $result->status = SliderStore::get_status($id);
        $result->progress = SliderStore::get_progress($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
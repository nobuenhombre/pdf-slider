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

class SiteController extends Controller
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
                        'actions' => ['progress', 'convert', 'slider'],
                        'allow' => true,
                        'roles' => ['?','@'],
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
    public function actionProgress(string $id)
    {
        $result = new \stdClass();
        $result->status = SliderStore::get_status($id);
        $result->progress = SliderStore::get_progress($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Ajax экшн - выполняет процесс конвертации
     * на вход приходит id - от загрузки файла - передается через data-id атрибут
     * @param string $id
     * @return mixed
     */
    public function actionConvert(string $id)
    {
        $result = new \stdClass();
        $status = SliderStore::get_status($id);
        /**
         * Если в редисе еще нет такого ключа, или статус - успешно, или ошибка что-бы переделать
         * конвертацию
         */
        if ((empty($status)) || ($status == SliderStore::STATUS_SUCCESS) || ($status == SliderStore::STATUS_ERROR)) {
            $pdf_converter = new PdfConverter($id);
            $pdf_converter->convert();
            $result->result = SliderStore::get_status($id);
        } else {
            $result->result = $status;
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');
            if ($model->upload()) {
                return $this->render('converter', ['model' => $model]);
            }
        }
        return $this->render('index', ['model' => $model]);
    }

    public function actionSlider(string $id)
    {
        $this->layout = 'slider';
        $qty = SliderStore::get_qty_images($id);
        return $this->render('slider', ['id'=>$id, 'qty' => $qty]);
    }

}

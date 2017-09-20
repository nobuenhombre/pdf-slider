<?php

namespace app\controllers;

use app\components\PdfConverter;
use app\components\SliderStore;
use app\models\UploadForm;
use Yii;
use yii\helpers\BaseArrayHelper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class SiteController
 *
 * Контроллер основных страниц и ajax
 *
 * @package app\controllers
 */
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
                'rules' => [
                    [
                        'actions' => ['progress', 'convert', 'index', 'slider'],
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

    /**
     * Эта конструкция нужна для того чтобы в функциях экшенов
     * входящие параметры также передавались из POST запросов
     * а не только через GET
     * ------------------------------------------------------
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function runAction($id, $params = [])
    {
        // Extract the params from the request and bind them to params
        $params = BaseArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
        return parent::runAction($id, $params);
    }

    /**
     * Ajax экшн - возвращает прогресс, статус и сообщения
     * на вход приходит id - от загрузки файла - передается через data-id атрибут
     * --------------------------------------------------------------------------
     * @param string $id
     * @return \stdClass
     */
    public function actionProgress(string $id)
    {
        $result = new \stdClass();
        $result->status = SliderStore::get_status($id);
        $result->progress = SliderStore::get_progress($id);
        $result->message = SliderStore::get_and_clear_message($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Ajax экшн - выполняет процесс конвертации
     * на вход приходит id - от загрузки файла - передается через data-id атрибут
     * --------------------------------------------------------------------------
     * @param string $id
     * @return mixed
     */
    public function actionConvert(string $id)
    {
        $result = new \stdClass();
        $status = SliderStore::get_status($id);
        /**
         * Это условие нужно - если в браузере случайно откроют страницу во второй вкладке
         * -------------------------------------------------------------------------------
         * Если в редисе еще нет такого ключа - то такой файл еще не конвертировался,
         * или статус - успешно,
         * или ошибка что-бы переделать конвертацию
         */
        if ((empty($status)) || ($status == SliderStore::STATUS_SUCCESS) || ($status == SliderStore::STATUS_ERROR)) {
            $pdf_converter = new PdfConverter($id);
            $pdf_converter->convert();
            $result->result = SliderStore::get_status($id);
        } else {
            /**
             * Если сейчас статус в процессе
             * Это значит на сервере уже происходит конвертация
             * pdf с таким id
             * Т.е. мы нечаяно открыли вторую вкладку
             */
            $result->result = $status;
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Главная страница с формой для передачи PDF файла
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
        $list_sliders = SliderStore::get_list_ids(SliderStore::DATA.":");
        return $this->render('index', ['model' => $model, 'list'=>$list_sliders]);
    }

    /**
     * Страница слайдера
     *
     * @param string $id
     * @return string
     */
    public function actionSlider(string $id)
    {
        if (SliderStore::slider_exists($id)) {
            $this->layout = 'slider';
            $qty = SliderStore::get_qty_images($id);
            return $this->render('slider', ['id' => $id, 'qty' => $qty]);
        } else {
            return $this->render('error', [
                'name'=>'Слайдера не существует!',
                'message'=>'Слайдера не существует! Либо его никогда не было, либо он был удален через 30 минут после создания!'
            ]);
        }
    }

}

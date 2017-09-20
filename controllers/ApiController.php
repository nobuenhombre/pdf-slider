<?php

namespace app\controllers;

use app\components\SliderStore;
use Yii;
use yii\helpers\BaseArrayHelper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ApiController
 *
 * Это примитивненькая реализация
 * REST API контроллера
 * который по GET получает id слайдера
 * и отдает список картинок в json
 *
 * api/get?id=<ID>
 * потестирвал в postman.
 *
 * @package app\controllers
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    
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
                        'actions' => ['get'],
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
     * REST API экшн - возвращает список картинок и статус
     * на вход приходит id
     * @param string $id
     * @return \stdClass
     */
    public function actionGet(string $id)
    {
        if (SliderStore::slider_exists($id)) {
            $result = new \stdClass();
            $result->status = 'ok';
            $result->list = SliderStore::get_images($id);
        } else {
            $result = new \stdClass();
            $result->status = 'error';
            $result->list = [];
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
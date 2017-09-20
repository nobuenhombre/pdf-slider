<?php

namespace app\commands;

use yii\console\Controller;

/**
 * Удаление устаревших данных
 */
class TimerController extends Controller
{

    /**
     * Удалить старые ненужные файлы
     */
    public function actionDelete()
    {
        try {
            $redis = \Yii::$app->NativeRedis->connect;
            echo "Старые данные удалены \n";
        } catch (\RedisException $rex) {
            echo "сервис Redis не доступен! \n";
        }
    }
}

<?php

namespace app\commands;

use app\components\SliderStore;
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
            $list_ids_to_remove = SliderStore::get_list_ids_to_remove();
            foreach ($list_ids_to_remove as $id)
            {
                echo "Удалить {$id} \n";
                SliderStore::delete_key_with_files($id);
            }
            echo "Старые данные удалены \n";
        } catch (\RedisException $rex) {
            echo "сервис Redis не доступен! \n";
        }
    }
}

<?php

namespace app\components;

class SliderStore
{

    const
        DATA = 'pdf.data', // В этой маске будем хранить число картинок для слайдера - Бессрочно
        TIME = 'pdf.time', // В этой маске будем хранить число картинок для слайдера - 30 минут для таймера удаления
        PROGRESS = 'pdf.progress', // В этой маске будем хранить прогресс конвертации PDF в набор картинок
        STATUS = 'pdf.status', // В Этой маске будем хранить статус конвертации
        SOURCE = 'pdf.source'; // В Этой маске сохраним оригинальное имя файла

    const
        TIMEOUT = 1800;//30 минут * 60 секунд

    const
        STATUS_IN_PROGRESS = 'in_progress',
        STATUS_SUCCESS = 'success',
        STATUS_ERROR = 'error';

    /**
     * Формируем ключи в редисе для хранения информации о слайдере
     * -----------------------------------------------------------
     * @param string $type
     * @param string $id
     * @return string
     */
    public static function key(string $type, string $id): string {
        return "{$type}:{$id}";
    }

    /**
     * Создаем два ключа - основной и таймер для последующего удаления
     * файлов слайдера через консольный контроллер
     * -------------------------------------------
     * @param string $id
     * @param int $images_qty
     */
    public static function create(string $id, int $images_qty)
    {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $redis->set(static::key(static::DATA, $id), $images_qty);
        $redis->set(static::key(static::TIME, $id), $images_qty, static::TIMEOUT);
    }

    /**
     * Фнукция сохранения прогресса конвертации в ключики редиса
     * ---------------------------------------------------------
     * @param string $id
     * @param int $images_qty
     * @param int $page_num
     * @return int
     */
    public static function set_progress(string $id, int $images_qty, int $page_num):int
    {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $progress = round(($page_num + 1) * 100 / $images_qty);
        $redis->set(static::key(static::PROGRESS, $id), $progress);
        return $progress;
    }

    /**
     * Получаем прогресс
     * @param string $id
     * @return string
     */
    public static function get_progress(string $id):string {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->get(static::key(static::PROGRESS, $id));
    }

    /**
     * Получим имя исходного файла из редиса
     * @param string $id
     * @return string
     */
    public static function get_source(string $id):string {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->get(static::key(static::SOURCE, $id));
    }

    /**
     * Сохраним имя исходного файла в редис
     * @param string $id
     * @param string $file_name
     */
    public static function set_source(string $id, string $file_name) {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $redis->set(static::key(static::SOURCE, $id), $file_name);
    }

    /**
     * Получим Статус из редиса
     * @param string $id
     * @return string
     */
    public static function get_status(string $id):string {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->get(static::key(static::STATUS, $id));
    }

    /**
     * Сохраним Статус в редис
     * @param string $id
     * @param string $status
     */
    public static function set_status(string $id, string $status) {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $redis->set(static::key(static::STATUS, $id), $status);
    }

    public static function get_qty_images(string $id)
    {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->get(static::key(static::DATA, $id));
    }

}
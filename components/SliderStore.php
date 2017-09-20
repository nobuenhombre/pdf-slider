<?php

namespace app\components;

use yii\helpers\Url;

/**
 * Class SliderStore
 *
 * Класс предназначен для управления ключами редиса
 * для хранения информации по процессу конвертации PDF
 * а также для хранения информации о готовых слайдерах
 *
 * @package app\components
 */
class SliderStore
{

    /**
     * Маски ключей редиса
     * Для разного рода данных о слайдерах
     */
    const
        DATA = 'pdf.data', // В этой маске будем хранить число картинок для слайдера - Бессрочно
        TIME = 'pdf.time', // В этой маске будем хранить число картинок для слайдера - 30 минут для таймера удаления
        PROGRESS = 'pdf.progress', // В этой маске будем хранить прогресс конвертации PDF в набор картинок
        STATUS = 'pdf.status', // В Этой маске будем хранить статус конвертации
        SOURCE = 'pdf.source', // В Этой маске сохраним оригинальное имя файла
        MESSAGE = 'pdf.message'; // В этой маске буду хранить список сообщений о ходе процесса конверсии

    /**
     * Таймаут для автоматического удаления ключей редиса
     * Раз в минуту по крону я буду звать консольный контроллер
     * php yii timer/delete
     * там вычисляются удаленные по таймауту ключи из редиса
     * и удаляются связанные с ними файлы слайдеров на диске.
     */
    const
        TIMEOUT = 1800;//1800 = 30 минут * 60 секунд

    /**
     * Статусы конвертации PDF
     * в процессе, успешно, ошибка
     */
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
     * Создаем основной ключ
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
    }

    /**
     * Создаем Временный ключ с таймаутом для последующего удаления
     * файлов слайдера через консольный контроллер
     * -------------------------------------------
     * @param string $id
     * @param int $images_qty
     */
    public static function set_timer(string $id, int $images_qty) {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
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
     * -----------------
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
     * -------------------------------------
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
     * ------------------------------------
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
     * Добавляем сообщения - лог исполнения в редис
     * Эти сообщения будут отражаться на странице в процессе конвертации
     * -----------------------------------------------------------------
     * @param string $id
     * @param string $message
     */
    public static function add_message(string $id, string $message) {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $redis->append(static::key(static::MESSAGE, $id), $message.'<br />');
    }

    /**
     * Получение сообщений и очистка
     * Чтобы после получения сообщения через ajax скрипт
     * ключик содержащий их очистился
     * ------------------------------
     * @param string $id
     * @return string
     */
    public static function get_and_clear_message(string $id): string {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->getSet(static::key(static::MESSAGE, $id), '');
    }

    /**
     * Получим Статус из редиса
     * ------------------------
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
     * -----------------------
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

    /**
     * Проверим существует ли слайдер?
     * или он не существовал, или он удалился по таймауту
     * --------------------------------------------------
     * @param string $id
     * @return bool
     */
    public static function slider_exists(string $id):bool {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->exists(static::key(static::DATA, $id));
    }

    /**
     * Получим количество картинок в слайдере
     * --------------------------------------
     * @param string $id
     * @return bool|string
     */
    public static function get_qty_images(string $id)
    {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        return $redis->get(static::key(static::DATA, $id));
    }

    /**
     * Получим список картинок для REST API контроллера
     * ------------------------------------------------
     * @param string $id
     * @return array
     */
    public static function get_images(string $id):array
    {
        $qty = static::get_qty_images($id);
        $list = [];
        for($i=0; $i<$qty; $i++) {
            $list[] = Url::to("@web/images/{$id}/page-{$i}.jpg", true);
        }
        return $list;
    }

    /**
     * Получение списка ключиков в редисе по маске
     * -------------------------------------------
     * @param string $mask
     * @return array
     */
    public static function get_list_ids(string $mask)
    {
        /**
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $keys = $redis->keys($mask."*");
        $list_ids = [];
        foreach($keys as $k) {
            $list_ids[$k] = substr($k, strlen($mask));
        }
        return $list_ids;
    }

    /**
     * Вычисление списка id слайдеров - претендентов на удаление
     * Мы знаем набор постоянных ключей
     * Мы знаем оставшие ключики с таймаутом
     * Их дельта и есть претенденты на удаление
     * ----------------------------------------
     * @return array
     */
    public static function get_list_ids_to_remove()
    {
        $list_ids_data = static::get_list_ids(static::DATA.":");
        $list_ids_time = static::get_list_ids(static::TIME.":");
        $keys_to_delete = array_diff($list_ids_data, $list_ids_time);
        return $keys_to_delete;
    }

    /**
     * Удаление всех ключей редиса для конкретного слайдера
     * ----------------------------------------------------
     * @param string $id
     */
    public static function delete_key(string $id)
    {
        /**
         * Удаляем ключи редиса касающиеся Данного id
         * @var $redis \Redis
         */
        $redis = \Yii::$app->NativeRedis->connect;
        $keys = $redis->keys("*:{$id}");
        $redis->del($keys);
    }

    /**
     * Удаляем полность все ключи в редисе и все связанные файлы
     * картинки и исходный pdf, и каталог где эти картинки лежали
     * ----------------------------------------------------------
     * @param string $id
     * @param bool $source
     */
    public static function delete_key_with_files(string $id, bool $source=true)
    {
        /**
         * Удаляем картинки, zip архив и исходный pdf
         */
        $qty = static::get_qty_images($id);
        $dir = PdfConverter::get_directory($id);
        for($i=0; $i<$qty; $i++) {
            @unlink("{$dir}page-{$i}.jpg");
        }
        @rmdir("{$dir}");
        @unlink(__DIR__ . "/../web/zips/{$id}.zip");
        if ($source) {
            $source_file = static::get_source($id);
            @unlink($source_file);
        }

        static::delete_key($id);
    }
}
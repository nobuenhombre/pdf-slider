<?php

namespace app\components;

use yii\base\Object;

/**
 * Class NativeRedis
 *
 * Вот мне очень захотелось пользоваться нативным редисом
 * это очень мощный и изящный инструмент
 * компонентой от Yii-Redis - не захотелось
 *
 * @package app\components
 */
class NativeRedis extends Object
{
    public
        $host, $port, $connect;

    public function init()
    {
        parent::init();
        /**
         * Здесь читаем настройки
         * из конфига
         *
         */
        if ($this->host === null) {
            $this->host = 'localhost';
        }
        if ($this->port === null) {
            $this->port = 6379;
        }

        $this->connect = null;

        try {
            if (extension_loaded('redis')) {
                $this->connect = new \Redis();
                $this->connect->connect($this->host, $this->port);
                $this->connect->select(4);
                /**
                 * Здесь 4 - номер базы на моем сервере который еще не занят!
                 */
            }
            /**
             * todo
             * Здесь нужно как то предупредить
             * о том что расширение не установлено!
             */
        } catch (\ErrorException $exc) {
            /**
             * todo
             * Если редис Вдруг упал!
             * Тоже нужно как то Предупредить о том
             */
        }
    }

}
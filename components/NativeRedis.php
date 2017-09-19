<?php

namespace app\components;

use yii\base\Object;

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
                $this->connect->select(0);
            }
            /**
             * Здесь нужно как то предупредить
             * о том что расширение не установлено!
             */
        } catch (\ErrorException $exc) {
            /**
             * Если редис Вдруг упал!
             * Тоже нужно как то Предупредить о том
             */
        }
    }

}
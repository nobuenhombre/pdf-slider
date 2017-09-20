<?php

set_time_limit(0);
ini_set('memory_limit', 134217728);
ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

define('APP_NAME', 'PDF Converter to HTML Slider');
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

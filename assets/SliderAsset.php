<?php

namespace app\assets;

use yii\web\AssetBundle;

class SliderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'assets/slider/css/slider.css',
    ];
    public $js = [
        'assets/slider/js/jquery.js',
        'assets/slider/js/slider.js'
    ];
    public $depends = [
    ];
}

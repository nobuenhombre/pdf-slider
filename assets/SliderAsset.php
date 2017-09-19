<?php

namespace app\assets;

use yii\web\AssetBundle;

class SliderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/slider.css',
    ];
    public $js = [
        'js/jquery.js',
        'js/slider.js'
    ];
    public $depends = [
    ];
}

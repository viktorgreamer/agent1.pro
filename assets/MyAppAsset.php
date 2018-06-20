<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class MyAppAsset
 * @package app\assets
 */
class MyAppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/main.js'
    ];
    public $depends = [
        'app\assets\MdbAsset', // Файлы с нашей темой
    ];
}
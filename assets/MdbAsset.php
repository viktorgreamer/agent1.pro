<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Class MdbAsset
 * @package app\assets
 */
class MdbAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/src/mdb';

    public $css;
    public $js;

    public function init()
    {
        $min = YII_ENV_DEV ? '' : '.min';
        $this->css = [
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
            'css/bootstrap' . $min . '.css',
            'css/mdb' . $min . '.css',
            'css/style.css',
        ];
        $this->js = [
            'js/jquery-3.2.1.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js',
            'js/bootstrap' . $min . '.js',
            'js/mdb' . $min . '.js',
            'js/mdb' . $min . '.js',
            'js/main.js',
            'js/Chart.min.js',
            'https://www.gstatic.com/charts/loader.js',

        ];

        // Заменяем на свои файлы Bootstrap и Jquery
        Yii::$app->assetManager->bundles = [
            'yii\bootstrap\BootstrapAsset' => [
                'sourcePath' => $this->sourcePath,
                'css' => $this->css,
            ],
            'yii\bootstrap\BootstrapPluginAsset' => [
                'sourcePath' => $this->sourcePath,
                'js' => $this->js,
            ],
            'yii\web\JqueryAsset' => [
                'sourcePath' => $this->sourcePath,
                'js' => $this->js,
            ],
        ];
    }
}
<?php

// comment out the following two lines when deployed to production
 defined('YII_DEBUG') or define('YII_DEBUG', true);
 defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/simple_html_dom.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/consts.php');
require(__DIR__ . '/error_const.php');
require(__DIR__ . '/functions.php');
require(__DIR__ . '/phpQuery.php');
$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

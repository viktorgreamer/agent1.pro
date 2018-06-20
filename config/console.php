<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // 'viewPath' => '@webroot/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'ssl://smtp.yandex.com',
                'username' => 'viktorgreamer1@yandex.ru',
                'password' => 'WibdU160',
                'port' => '465',
                // 'encryption' => 'SSL',
            ],
        ],
//        'db' => ['class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=localhost;dbname=cloud1',
//            'username' => 'root',
//            'password' => '',
//            'charset' => 'utf8'],
        'cloud' => ['class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=141.8.195.92;dbname=a0086640_sz',
            'username' => 'a0086640_pr',
            'password' => 'WindU160',
            'charset' => 'utf8',
        ],
        'db' => ['class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=141.8.195.92;dbname=a0086640_sz',
            'username' => 'a0086640_pr',
            'password' => 'WindU160',
            'charset' => 'utf8'],

    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

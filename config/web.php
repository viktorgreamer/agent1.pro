<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'settings' => [
            'class' => '\app\components\Settings'
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'AGL5w5Zs3eqiU-AEC3aQRyz5jebG153L',
            'baseUrl'=> '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
           // 'class' => 'yii\caching\DbCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'formatter' => [ 'class' => 'yii\i18n\Formatter', 'dateFormat' => 'php:d F Y', 'datetimeFormat' => 'php:j F, H:i', 'timeFormat' => 'php:H:i:s', 'defaultTimeZone' => 'Europe/Moscow', 'locale' => 'ru-RU' ]
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],
,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@webroot/mail',
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => ['class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=141.8.195.92;dbname=a0086640_sz',
            'username' => 'a0086640_pr',
            'password' => 'WindU160',
            'charset' => 'utf8'],
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
//            'cloud' => ['class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=141.8.195.92;dbname=a0086640_cloud',
//                'username' => 'a0086640_pr',
//                'password' => 'WindU160',
//            'charset' => 'utf8',
//            'schemaCache' => 'cache',
//            'schemaCacheDuration' => '3600',
//            'enableSchemaCache' => true
]
        ,

        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '<controller>/<action>' => '<controller>/<action>'
                ]
        ],

    ],
    'modules' => [

        'admin' => [

            'class' => 'app\modules\admin\Test',

        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

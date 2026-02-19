<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5bqOAOAx6uoviDOvre4mMT2WCW9pvcHx',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['pattern' => 'api/link/create', 'route' => 'api/link/create', 'verb' => 'POST'],
                ['pattern' => 'api/auth/login', 'route' => 'api/auth/login', 'verb' => 'POST'],
                ['pattern' => 'api/auth/logout', 'route' => 'api/auth/logout', 'verb' => 'POST'],
                ['pattern' => 'api/auth/me', 'route' => 'api/auth/me', 'verb' => 'GET'],
                ['pattern' => 'api/contact/submit', 'route' => 'api/contact/submit', 'verb' => 'POST'],
                '<code:[A-Za-z0-9]{8}>' => 'redirect/go',
                '' => 'site/app',
                'login' => 'site/app',
                'contact' => 'site/app',
                'about' => 'site/app',
                [
                    'pattern' => '<path:(?!api|site/|debug|gii)[A-Za-z0-9_\\-/]+>',
                    'route' => 'site/app',
                    'verb' => 'GET',
                ],
            ],
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

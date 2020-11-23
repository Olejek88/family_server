<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@frontend/views/yii2-app'
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'loginUrl' => ['/login'],
            'enableAutoLogin' => true,
            'autoRenewCookie' => true,
            'authTimeout' => 30 * 60 * 60, // Длинна сессии пользователя в секундах, 1440 Default
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/dashboard',
                '/timeline' => 'site/timeline',
                '/login' => 'site/login',
                '/signup' => 'site/signup',
                '/logout' => 'site/logout',
                '<controller>/<id:\d+>' => '<controller>/create',
                '<controller>/<id:\d+>' => '<controller>/delete',
                '<controller>/<id:\d+>' => '<controller>/view',
                '<controller>/<id:\d+>' => '<controller>/info',
            ],
        ],
    ],

    'modules' => [
        'treemanager' => [
            'class' => '\kartik\tree\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],
    ],
    'params' => $params,
];

<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module',
            'i18n' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@kvgrid/messages',
                'forceTranslation' => true,
            ]
        ],
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module'
        ]
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath'=> '@common/cache'
        ],
        'options' => [
            'class' => 'common\components\Options',
        ],
        'systemlog' => [
            'class' => 'common\models\Log',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'=>false,

            'rules' => [
                // your rules go here
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'app' => 'yii.php',
                    ],
                ],
            ],
        ],
    ],
];

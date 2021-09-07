<?php

/**
 * TSMD 模块配置文件
 *
 * 须将此配置文件导入到 /api/web/index.php 文件的 $config 参数
 *
 * @link https://tsmd.thirsight.com/
 * @copyright Copyright (c) 2008 thirsight
 * @license https://tsmd.thirsight.com/license/
 */

return [
    // 设置路径别名，以便 Yii::autoload() 可自动加载 TSMD 自定的类
    'aliases' => [
        // yii2-tsmd-corpinfo 路径
        '@tsmd/corpinfo' => __DIR__ . '/../src',
    ],

    // 模块组件配置
    'components' => [
        'corpinfo' => [
            'class' => 'tsmd\corpinfo\components\Corpinfo',
            'crawlers' => [
                'tsmd\corpinfo\components\crawlers\TwNatGcis',
            ],
        ],
    ],
];

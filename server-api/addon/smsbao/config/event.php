<?php
/**
 * @Date: 2025/6/25 22:58
 */
declare(strict_types=1);
// 事件定义文件
return [
    'bind' => [

    ],

    'listen' => [
        'AddonSystemConfig' => [
            'addon\smsbao\event\Config'
        ],
        'SendSms' => [
            'addon\smsbao\event\SendSms'
        ],
    ],

    'subscribe' => [
    ],
];

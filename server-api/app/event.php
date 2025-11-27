<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [
            'app\\common\\event\\InitConfig',
            'app\\common\\event\\InitRoute',
            'app\\common\\event\\InitAddon'
        ],
        'HttpRun'  => [
        ],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],
    'subscribe' => [
    ],
];

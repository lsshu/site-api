<?php
return [
    "root_username"=>"root",
    "root_nickname"=>"Root",
    "root_password"=>"root123456",
    "root_guard_name"=>"site-api",
    "mini_program"=>[
        'app_id' => env('MINI_PROGRAM_APP_ID', ''),
        'secret' => env('MINI_PROGRAM_SECRET', ''),

        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => __DIR__ . '/wechat.log',
        ],
    ]
];

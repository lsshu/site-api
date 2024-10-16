<?php

return [
    [
        "name" => "site-api.authorization",
        "title" => "后台管理",
        "icon" => "",
        'action' => [['name' => "aaa", "title" => "AAA"]],
        "children" => [
            [
                'name' => 'roots',
                'title' => '管理员',
                'action' => [['name' => "abc", "title" => "ABC"]]
            ],
            [
                'name' => 'roles',
                'title' => '角色',
                'action' => [['name' => "cde", "title" => "CDE"]]
            ],
            [
                'name' => 'permissions',
                'title' => '权限',
            ],
        ]
    ]
];

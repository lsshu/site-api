<?php

return [
    [
        "name" => "system",
        "title" => "系统管理",
        "icon" => "",
//        'action' => [['name' => "aaa", "title" => "AAA"]],
        "children" => [
            [
                'name' => 'user',
                'title' => '用户管理',
//                'action' => [['name' => "abc", "title" => "ABC"]]
            ],
            [
                'name' => 'role',
                'title' => '角色管理',
//                'action' => [['name' => "cde", "title" => "CDE"]]
            ],
            [
                'name' => 'menu',
                'title' => '菜单管理',
            ],
            [
                'name' => 'dept',
                'title' => '部门管理',
            ],
        ]
    ]
];

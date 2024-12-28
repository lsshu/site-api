<?php

return [
    [
        "path" => "system",
        "title" => "menus.pureSysManagement",
        "icon" => "ri:settings-3-line",
//        "action" => [["name" => "aaa", "title" => "AAA"]],
        "children" => [
            [
                "path" => "user",
                "title" => "menus.pureUser",
                "icon" => "ri:admin-line",
            ],
            [
                "path" => "role",
                "title" => "menus.pureRole",
                "icon" => "ri:admin-fill",
            ],
            [
                "path" => "menu",
                "title" => "menus.pureSystemMenu",
                "icon" => "ep:menu",
            ],
            [
                "path" => "dept",
                "title" => "menus.pureDept",
                "icon" => "ri:git-branch-line",
            ],
        ]
    ]
];

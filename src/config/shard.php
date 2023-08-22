<?php


return [
    'database_in_instance' => 2,
    'database_in_instance' => 3,
    'default' => [
        [
        "range" => range(0,2),
        "master" => "mysql_001a",
        "slave" => "mysql_001b",
        ],
        [
            "range" => range(3,5),
            "master" => "mysql_002a",
            "slave" => "mysql_002b",
        ],
    ],
    'type_id' => [
        'post' => 1,
        'board' => 1,
    ]
];
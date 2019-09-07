<?php


return [
    'mysql' => [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => '3306',
        'database' => 'rix_visitors',
        'username' => 'root',
        'password' => '123456',
        'charset'  => 'utf8',
        'ext-pdo'  => extension_loaded('pdo_mysql') ? true : false
    ],
    'mongodb' => [
        'driver' => 'mongodb',
        'uri' => 'mongodb://root:123456@localhost:27017',
        'database' => 'rix_visitors',
        'ext-mongodb' => extension_loaded('mongodb') ? true : false
    ]
];


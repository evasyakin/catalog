<?php

return [
    // 'options' => [], // опции соединения с субд
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => false, // помогает с приведением типов из базы в php
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
    ],
    'driver' => env('DB_DRIVER', 'mysql'), // драйвер субд (по умолчанию mysql)
    'charset' => env('DB_CHARSET', 'utf8'), // кодировка данных (по умолчанию utf8)

    'host' => env('DB_HOST', 'localhost'), // хост субд (по умолчанию localhost)
    'username' => env('DB_USERNAME', 'root'), // пользователь субд
    'password' => env('DB_PASSWORD'), // пароль субд
    'dbname' => env('DB_DBNAME', 'tests_catalog-1'), // имя базы данных
];

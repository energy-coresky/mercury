<?php

$plans = [
    'view' => ['path' => __DIR__ . '/mvc'],
    'mem' => ['path' => __DIR__ . '/mvc'],
    'app' => [
        'type' => 'dev',
        'require' => 'Parsedown SQLite3',
    ],
];

SKY::$databases += [
    '_w' => ['driver' => 'sqlite3', 'dsn' => __DIR__ . '/mercury.base'],
];

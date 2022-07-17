<?php

$plans = [
    'view' => ['path' => __DIR__ . '/w3'],
    'glob' => ['path' => __DIR__ . '/w3'],
    'app' => [
        'type' => 'dev',
        'require' => 'Parsedown SQLite3',
    ],
];

SKY::$databases += [
    '_w' => ['driver' => 'sqlite3', 'dsn' => __DIR__ . '/mercury.base'],
];

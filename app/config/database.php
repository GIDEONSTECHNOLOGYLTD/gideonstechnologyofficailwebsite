<?php

return [
    'default' => [
        'driver' => 'sqlite',
        'database' => BASE_PATH . '/database/gtech.db',
        'prefix' => ''
    ],
    'testing' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => ''
    ]
];

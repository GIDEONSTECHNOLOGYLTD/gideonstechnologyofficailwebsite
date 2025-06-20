<?php

return [
    'view' => [
        'template_path' => __DIR__ . '/../resources/views',
        'cache_path' => __DIR__ . '/../storage/cache/views',
        'debug' => true,
        'auto_reload' => true,
    ],
    'displayErrorDetails' => true,
    'logError' => true,
    'logErrorDetails' => true,
    'addContentLengthHeader' => false,
    'determineRouteBeforeAppMiddleware' => true,
];

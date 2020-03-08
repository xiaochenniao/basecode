<?php

return array(
    'base' => array(
        'is_debug' => 1,
    ),
    'client' => array(
        'timeout' => 8,
    ),
    'db' => array(
        'charset' => 'utf8',
        'sources' => array(
            'main' => array(
                'database' => 'basecode',
                'user' => 'root',
                'pass' => '123456',
                'host' => 'localhost',
                'port' => '3306',
            ),
        ),
    ),
);

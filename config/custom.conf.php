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
                'database' => 'baseCode',
                'user' => 'root',
                'pass' => 'root',
                'host' => 'localhost',
                'port' => '3306',
            ),
        ),
    ),
);

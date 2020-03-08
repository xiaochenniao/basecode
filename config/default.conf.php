<?php

define('BASE_URL', '');
return array(
    'base' => array(
        'cookie_domain' => '',
    ),
    'memcached' => array(
        array('127.0.0.1', 11211, 100),
    ),
    'redis_local' => array('127.0.0.1', 7312),
    'db' => array(
        'charset' => 'utf8',
        'tables' => array(
            '[^\.]+' => array(
                'pk' => array('id'),
                'source' => 'main',
            ),
        ),
    ),
);

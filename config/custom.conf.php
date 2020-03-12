<?php

return array(
    'base' => array(
        'is_debug' => 1,
    ),
    'client' => array(
        'timeout' => 8,
    ),
    'es' => array(
        'use_search' => true, //是否使用es搜索
        'host' => [
            'http://127.0.0.1:9200',
        ],
        'index' => 'base', //#index的名字不能是大写和下划线开头  index 对应关系型数据（以下简称MySQL）里面的数据库，而不是对应MySQL里面的索引，这点要清楚
        'type' => "base_type" //Elastic 5.x 版可一多个 Elastic 6.x 版只允许每个 Index 包含一个 Type，7.x 版将会彻底移除 Type。
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

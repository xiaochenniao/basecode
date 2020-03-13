<?php

/**
 * es 操作统一类
 * Description of elasticsearch
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-12
 */
use Elasticsearch\ClientBuilder;

class elasticsearch {

    protected static $_esClient = null;
    protected static $_conf = [];

    public static function init() {
        self::$_conf = Load::conf('custom', 'es');
        if (empty(self::$_conf)) {
            throw new except('es config no!');
        }

        if (self::$_esClient === null) {
            try {
                self::$_esClient = ClientBuilder::create()->setHosts(self::$_conf['host'])->setRetries(3)->build();
            } catch (Exception $ex) {
                
            }
        }
    }

    /**
     * 添加单个文档
     * @param type $id
     * @param type $body
     * @return type
     */
    public static function insert($id, $body = []) {
        if (!$id) {
            return false;
        }
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'id' => $id,
            'body' => $body
        ];

        return self::$_esClient->index($params);
    }

    /**
     * 删除一个文档
     * @param type $uid
     * @param type $data_type
     */
    public static function delete($id) {
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'id' => $id
        ];
        try {
            return self::$_esClient->delete($params);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * 获取一个文档
     * @param type $id
     * @return type
     */
    public static function get($id) {
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'id' => $id
        ];

        try {
            return self::$_esClient->get($params);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * 模糊查找
     * @param type $keywords
     * @param type $field
     * @return type
     */
    public static function search($keywords, $field = []) {
        //可参考 https://blog.csdn.net/weixin_33860147/article/details/91882127
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $keywords,
                        "fields" => empty($field) ? $field : new \stdClass()
                    ]
                ]
            ]
        ];

        return self::$_esClient->search($params);
    }

    /**
     * 
     * @return type
     */
    public static function searchall() {
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        return self::$_esClient->search($params);
    }

}

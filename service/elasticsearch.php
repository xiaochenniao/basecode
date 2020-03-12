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
     * 
     * @param type $uid
     * @param type $data_type
     * @return type
     */
    public static function get($uid, $data_type = 'json') {
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'id' => 'my_id'
        ];
        $res = self::$_esClient->get($params);
        return $res;
    }

    /**
     * 
     * @param type $uid
     * @param type $data_type
     */
    public static function delete($uid, $data_type = 'json') {
        self::init();
        $params = [
            'index' => self::$_conf['index'],
            'type' => self::$_conf['type'],
            'id' => 'my_id'
        ];
        $res = self::$_esClient->delete($params);
        return $res;
    }

}

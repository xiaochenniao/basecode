<?php

/**
 * Description of es_test
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-13
 */
class es_test_Controller extends base_Controller {

    function init() {
        
    }

    function indexAction() {
        $res = Elasticsearch::index();
        print_r($res);
        die;
    }

    function createAction() {
        $res = Elasticsearch::create();
        print_r($res);
        die;
    }

    function insertAction() {
        $id = r_get('id');
        $data = r_gets();
        unset($data['id']);

        $res = Elasticsearch::insert($id, $data);
        print_r($res);
        die;
    }

    function delAction() {
        $id = r_get('id');
        $res = Elasticsearch::delete($id);
        print_r($res);
        die;
    }

    function getAction() {
        $id = r_get('id');
        $res = Elasticsearch::get($id);
        print_r($res);
        die;
    }

    function searchAction() {
        $keyword = r_get('keyword');
        $res = Elasticsearch::search($keyword, []);
        print_r($res);
        die;
    }

    function searchallAction() {
        $keyword = r_get('keyword');
        $res = Elasticsearch::searchall($keyword, []);
        print_r($res);
        die;
    }

}

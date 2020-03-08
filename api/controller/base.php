<?php

/*
 * 前台基类
 * 定义前台公用方法
 */

class base_Controller extends Controller {

    function init() {
        $this->conf = Load::conf('define');
    }

    public function returnJson($data) {
        v_json($data);
    }

    public function requiredGet($key) {
        $value = r_get($key);
        if ($value !== 0 && !$value) {
            $msg = "$key is a required parameter ";
            $this->returnStatus(-1000, $msg);
        }
        return $value;
    }

    public function log($file_name, $data) {
        if (is_array($data)) {
            $data = serialize($data);
        }
        logger::loginfo(request::getControllerName() . '_' . request::getActionName() . '_' . $file_name, $data);
    }

}

<?php

/**
 * 系统参数设置
 */

class setting_Controller extends Controller {

    function indexAction() {
        $this->_forward('edit');
    }

    function editAction() {
        $id = 1;
        $info = db::getByPk("sys_config", $id);
        $info['ifopen'] = unserialize($info['ifopen']);
        v_set('info', $info);
        v_display('setting.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        $info['id'] = 1;
        $info['weburl'] = rtrim($info['weburl'], "/");
        if (!strstr($info['weburl'], 'http://')) {
            $info['weburl'] = 'http://' . $info['weburl'];
        }
        if ($info['zhtj'] != 1) {
            $info['zhtj'] = 0;
        }
        if ($info['ifopen']['jb'] != 1) {
            $info['ifopen']['jb'] = 0;
        }
        if ($info['ifopen']['js'] != 1) {
            $info['ifopen']['js'] = 0;
        }
        if ($info['ifopen']['bd'] != 1) {
            $info['ifopen']['bd'] = 0;
        }
        $info['ifopen'] = serialize($info['ifopen']);
        $info['zhfee'] = floatval($info['zhfee']);
        if ($info['zhfee'] < 0) {
            v_notice("账号单价不能低于0元", 1);
        }
        if (db::set("sys_config", $info) === false) {
            v_notice("操作失败", 1);
        }
        //更新缓存
        Scache::sys_config(true);
        v_notice('站点设置成功', '/setting');
    }

}

?>
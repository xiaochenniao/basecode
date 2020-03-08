<?php

/*
 * 地区数据配置
 */

class area_Controller extends Controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $upid = r_int('pid');
        $areas = db::getWhere('b_area', 'upid=?', array($upid), 'displayorder ASC,id ASC');
        v_set('trees', array_reverse(b_area::getTrees($upid)));
        v_set('areas', $areas);
        v_set('pid', $upid);
        v_display();
    }

    function updateAction() {
        $data = r_get('info');
        foreach ($data as $id => $info) {
            $set = array();
            $set['id'] = $id;
            $set['displayorder'] = intval($info['displayorder']);
            $set['name'] = trim($info['name']);
            if (!isset($info['isshow'])) {
                $set['isshow'] = 0;
            }
            if ($set['id'] > 0 && $set['displayorder'] >= 0 && $set['name']) {
                if (intval($info['upid']) > 0) {
                    $set['joinname'] = b_area::getPname(intval($info['upid'])) . $set['name'];
                }
                db::set('b_area', $set);
            }
        }
        v_notice('更新成功', '/area/list.do?pid=' . $info['upid']);
    }

    function addAction() {
        $upid = r_int('pid', 0);
        if ($upid > 0) {
            $pinfo = db::getByPk('b_area', $upid);
            $areas = db::getWhere('b_area', 'upid=?', array($pinfo['upid']), 'displayorder ASC,id ASC');
        }
        v_set('trees', array_reverse(b_area::getTrees($upid)));
        v_set('areas', $areas);
        v_set('pid', $upid);
        v_set('pinfo', $pinfo);
        v_display('area_info.tpl');
    }

    function editAction() {
        $id = r_int('id');
        if (!$info = db::getByPk('b_area', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        if ($info['upid'] > 0) {
            $pinfo = db::getByPk('b_area', $info['upid']);
            $areas = db::getWhere('b_area', 'upid=?', array($pinfo['upid']), 'displayorder ASC,id ASC');
        }

        v_set('trees', array_reverse(b_area::getTrees($upid)));
        v_set('areas', $areas);
        v_set('pid', $info['upid']);
        v_set('info', $info);
        v_display('area_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        if ($info['upid'] > 0) {
            $pinfo = db::getByPk('b_area', $info['upid']);
            $info['joinname'] = $pinfo['joinname'] . $info['name'];
        } else {
            $info['joinname'] = $info['name'];
        }
        if (db::set('b_area', $info) === false) {
            v_notice("操作失败！", 1);
        }
        v_notice('操作成功', '/area/list.do?pid=' . $info['upid']);
    }

    function flushAction() {
        Scache::b_area(null, true);
        v_notice('缓存更新成功', 'list');
    }

    function removeAction() {
        $id = r_int('id');
        if (!$info = db::getByPk('b_area', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        if (db::countWhere('b_area', 'upid=' . $id)) {
            v_notice('该分类下包含子地区，不能删除', 1);
        }
        if (!db::delByPk('b_area', $id)) {
            v_notice('删除失败', 1);
        }
        v_notice('删除成功', 'list');
    }

}

?>
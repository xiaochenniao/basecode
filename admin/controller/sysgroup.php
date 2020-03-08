<?php

/**
 * 后台管理组
 */
class sysgroup_Controller extends Controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $datas = db::getAll('sys_group', 'order_id asc,id asc');
        v_set('rows', $datas);
        v_display();
    }

    function addAction() {
        v_display('sysgroup_info.tpl');
    }

    function editAction() {
        $id = intval(r_get('id'));
        if (!($info = db::getByPk('sys_group', $id))) {
            v_notice('您请求的数据不存在', 1);
        }
        v_set('info', $info);
        v_display('sysgroup_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        if (empty($info['group_name'])) {
            v_notice("组别名称必须填写", 1);
        }
        if (db::set('sys_group', $info) === false) {
            v_notice("操作失败", 1);
        }
        v_notice('操作成功', 'list');
    }

    function updateAction() {
        $data = r_get('info');
        foreach ($data as $id => $info) {
            $set = array();
            $set['id'] = $id;
            $set['order_id'] = intval($info['order_id']);
            $set['group_name'] = $info['group_name'];
            if (empty($set['group_name'])) {
                v_notice("组别名称必须填写", 'list');
            }
            if ($set['id'] > 0 && $set['order_id'] >= 0) {
                db::set('sys_group', $set);
            }
        }
        v_notice('更新成功', 'list');
    }

    function flushAction() {
        Scache::sys_group(true);
        v_notice('缓存更新成功', 'list');
    }

    function removeAction() {
        $id = r_int('id');
        if (!$rowCount = db::delByPk('sys_group', $id)) {
            v_notice('删除失败', 1);
        }
        v_notice('操作成功', 'list');
    }

    function purviewAction() {
        if (request::is_post()) {
            $info = r_get('info');
            if (r_get('m') == 'group') {
                $info['purview'] = serialize($info['purview']);
                if (db::set('sys_group', $info) === false) {
                    v_callback("操作失败", 0);
                }
            } elseif (r_get('m') == 'user') {
                if (r_get('group_p')) {
                    $group = db::getByPk('sys_group', r_get('group_p'));
                    $info['purview'] = $group['purview'];
                } else {
                    $info['purview'] = serialize($info['purview']);
                    $a_type = r_get('admin_type', 1);
                    $group = db::getByPk('sys_group', $a_type);
                }
                if (db::set('sys_user', $info) === false) {
                    v_callback("操作失败", 0);
                }
            }
            sys_menu::flushcache('menus_' . $info['id']);
            v_callback('操作成功', 1);
        }
        $id = r_int('id');
        $uid = r_get('uid');
        if ($id > 0) {
            if (!$info = db::getByPk('sys_group', $id)) {
                v_notice('您请求的数据不存在', 1);
            }
            $groupone = Scache::sys_group();
            $info['purview'] = unserialize($info['purview']);
            v_set('m', 'group');
        } elseif ($uid > 0) {
            if (!$info = db::getByPk('sys_user', $uid)) {
                v_notice('您请求的数据不存在', 1);
            }
            if ($info['purview']) {
                $info['purview'] = unserialize($info['purview']);
            } else {
                $group = db::getByPk('sys_group', $info['id']);
                $info['purview'] = unserialize($group['purview']);
            }
            v_set('m', 'user');
        }
        //print_r($this->purview->all(0,1,2));exit;
        $purviews = sys_purview::all(0, 4, 2);
        //echo "<pre/>";print_r($purviews);exit;
        v_set('purviews', $purviews, false);
        v_set('purviewtops', sys_purview::all(0, 1, 2), false);
        v_set('info', $info);
        v_display('sysgroup_purview.tpl');
    }

}

?>
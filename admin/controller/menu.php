<?php

/**
 * 后台菜单配置
 */
class menu_Controller extends Controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $rows = sys_menu::all();
        v_set('menus', $rows, false);
        v_display();
    }

    function updateAction() {
        //	print_r($_POST);die;
        $data = r_get('info');
        foreach ($data as $id => $info) {
            $set = array();
            $set['id'] = $id;
            $set['order_id'] = intval($info['order_id']);
            $set['menu_name'] = trim($info['menu_name']);
            $set['menu_link'] = trim($info['menu_link']);
            $set['icon_style'] = trim($info['icon_style']);
            $set['isshow'] = trim($info['isshow']);
            if (!$set['menu_link'] || !$set['menu_name']) {
                v_notice('菜单名称和链接地址必须同时填写', 'list');
            }
            if (!isset($info['isshow'])) {
                $set['isshow'] = 0;
            }
            if ($set['id'] > 0 && $set['order_id'] >= 0 && $set['menu_name'] && $set['menu_link']) {
                db::set('sys_menu', $set);
            }
        }
        v_notice('更新成功', 'list');
    }

    function addAction() {
        $menus = sys_menu::all(0, 2);
        v_set('info', array('isshow' => 1));
        v_set('menus', $menus, false);
        v_display('menu_info.tpl');
    }

    function editAction() {
        $id = r_int('id');
        if (!$info = db::getByPk('sys_menu', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        $menus = sys_menu::all(0, 2);
        v_set('menus', $menus, false);
        v_set('info', $info);
        v_display('menu_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        if (db::set('sys_menu', $info) === false) {
            v_notice("操作失败！", 1);
        }
        v_notice('操作成功', 'list');
    }

    function flushAction() {
        sys_menu::flushcache();
        v_notice('缓存更新成功', 'list');
    }

    function removeAction() {
        $id = r_int('id');
        if (!db::delByPk('sys_menu', $id)) {
            v_notice('删除失败！', 1);
        }
        v_notice('删除成功', 'list');
    }

    function getIconListAction() {

        v_display();
    }

}

?>
<?php

/*
 * 后台管理员 设置常用操作菜单
 */

class diymenu_Controller extends Controller {

    function indexAction() {
        $this->_forward('edit');
    }

    function editAction() {
        $diymenu = F::logininfo('diymenu');
        if ($diymenu) {
            $diymenu = explode(',', $diymenu);
        } else {
            $diymenu = array();
        }
        $isadmin = F::logininfo('usergroup') == 1 ? true : false;
        $auth = F::logininfo('auth');
        $auth_ids = $auth ? array_keys($auth) : array();
        $menulist = array();
        $rows = db::getWhereForFields("sys_menu", 'id, menu_name as name', "parent_id=0 AND isshow=1", array(), 'order_id asc,id asc');
        foreach ($rows as $row) {
            $menulist[$row['id']]['id'] = $row['id'];
            $menulist[$row['id']]['name'] = $row['name'];
            $subrows = db::getWhereForFields("sys_menu", 'id, menu_name as name, menu_link as url', "parent_id=" . $row['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
            $i = 0;
            foreach ($subrows as $row1) {
                $submenu = array('id' => $row1['url'], 'name' => $row1['name'], 'url' => $row1['url'] . '.do');
                $subrows2 = db::getWhereForFields("sys_menu", 'id, menu_name as name, menu_link as url', "parent_id=" . $row1['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
                if ($subrows2) {
                    foreach ($subrows2 as $row2) {
                        if ($isadmin || in_array($row2['url'], $auth_ids)) {
                            if (in_array($row2['id'], $diymenu)) {
                                $row2['check'] = ' checked';
                            }
                            $i++;
                            $menulist[$row['id']]['items'][] = $row2;
                        }
                    }
                } else {
                    if ($isadmin || in_array($row1['url'], $auth_ids)) {
                        if (in_array($row1['id'], $diymenu)) {
                            $row1['check'] = ' checked';
                        }
                        $i++;
                        $menulist[$row['id']]['items'][] = $row1;
                    }
                }
            }
            if ($i < 1) {
                unset($menulist[$row['id']]);
            }
        }
        v_set('menulist', $menulist);
        v_display('diymenu.tpl');
    }

    function saveAction() {
        $ids = r_get('diydb');
        if (count($ids) > 15) {
            v_notice("常用菜单最多可设置15个。", 1);
        }
        if (count($ids) < 1) {
            v_notice("请至少选择一个再提交。", 1);
        }
        $ids = implode(',', $ids);
        $info = array('id' => F::logininfo('id'), 'diymenu' => $ids);
        if (db::set("sys_user", $info) === false) {
            v_notice("操作失败！", 1);
        }
        F::setlogininfo('diymenu', $ids);
        v_notice('常用菜单设置成功', '/diymenu');
    }

}

?>
<?php

/**
 * 后台管理权限设置
 */

class rightset_Controller extends Controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $rows = sys_purview::all();
        v_set('rows', $rows, false);
        v_display();
    }

    function addAction() {
        $purviews = sys_purview::all(0, 2);
        v_set('purviews', $purviews);
        v_display('rightset_info.tpl');
    }

    function editAction() {
        $id = r_int('id');
        if (!$info = db::getByPk('sys_purview', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        $purviews = sys_purview::all(0, 2);
        v_set('purviews', $purviews);
        v_set('info', $info);
        v_display('rightset_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        if (db::set('sys_purview', $info) === false) {
            v_notice("数据更新失败", 1);
        }
        v_notice('操作成功', 'list.do');
    }

    function updateAction() {
        $data = r_get('info');
        foreach ($data as $id => $info) {
            $set = array();
            $set['id'] = $id;
            $set['order_id'] = intval($info['order_id']);
            $set['purview_name'] = trim($info['purview_name']);
            $set['purview_link'] = trim($info['purview_link']);
            if (empty($set['purview_name']) || empty($set['purview_link'])) {
                v_notice('权限名称和权限标识必须全部填写', 'list');
            }
            if ($set['id'] > 0 && $set['order_id'] >= 0 && $set['purview_name'] && $set['purview_link']) {
                db::set('sys_purview', $set);
            }
        }
        v_notice('更新成功', 'list');
    }

    function removeAction() {
        $id = r_int('id');
        if (!$rowCount = db::delByPk('sys_purview', $id)) {
            v_notice("删除失败", 1);
        }
        v_notice('操作成功', 'list');
    }

    //自动根据 controller下的文件及后台菜单  自动生成操作权限  节点
    function createAction() {
        $conf = Load::conf('define');
        $config = Load::conf('route');
        $rules = (array) $config[APP_NAME]['rule'];
        $noallow = array('index', 'save', 'checkinfo');
        $noallowmodel = array('ajax');
        $actionname = $conf['admin_action'];
        $dir = CONTROLLER_DIR;
        $directory = dir($dir);
        $i = 1;
        while ($entry = $directory->read()) {
            $filename = $dir . '/' . $entry;
            if (is_file($filename)) {
                $fcontrol = str_replace(".php", "", $entry);
                $fp = fopen($filename, 'rb');
                $copy_content = fread($fp, filesize($filename));
                preg_match_all('/function ([a-z]+)Action()/', $copy_content, $out1);
                if (!empty($out1[1])) {
                    foreach ($out1[1] as $key => $val) {
                        if (!in_array($val, $noallow) && $val != '(.*)') {
                            $action[] = $val;
                        }
                    }
                }
                fclose($fp);
                $files[$fcontrol] = $action;
                unset($action);
            }
        }
        $directory->close();
        $menus = sys_menu::menuAll();
        foreach ($menus as $mid => $val) {
            if ($val['has_im'] == 0) {
                $parentid = db::set('sys_purview', array('purview_name' => $val['menu_name'], 'purview_link' => 'main_' . $val['menu_link'], 'parent_id' => 0, 'order_id' => $val['order_id']));
                db::set('sys_menu', array('id' => $val['id'], 'has_im' => $parentid));
            } else {
                $parentid = $val['has_im'];
            }
            $i = $val['order_id'] * 100 + 1;
            foreach ($val['items'] as $k => $v) {
                if ($v['has_im'] == 0) {
                    $purviewid = db::set('sys_purview', array('purview_name' => $v['menu_name'], 'purview_link' => $v['menu_link'], 'parent_id' => $parentid, 'order_id' => $i));
                    db::set('sys_menu', array('id' => $v['id'], 'has_im' => $purviewid));
                } else {
                    $purviewid = $v['has_im'];
                }
                if ($v['menu_link']) {
                    $path = trim($v['menu_link'], '/');
                    foreach ($rules as $rule) {
                        $newpath = preg_replace("/$rule[0]/i", $rule[1], $path);
                        if ($newpath != $path) {
                            $path = $newpath;
                            break;
                        }
                    }
                    $path = explode('/', $path);
                    $actions = array();
                    $actions = isset($files[$path[0]]) ? $files[$path[0]] : array();
                    $oid = $i * 10 + 1;
                    foreach ($actions as $key => $act) {
                        $purviewinfo = db::getone('sys_purview', "parent_id=? AND purview_link=?", array($purviewid, $act));
                        if (!$purviewinfo) {
                            $name = $actionname[$act] ? $actionname[$act] : $act;
                            db::set('sys_purview', array('purview_name' => $name, 'purview_link' => $act, 'parent_id' => $purviewid, 'order_id' => $oid));
                        }
                        $oid++;
                    }
                }
                $i++;
            }
        }
        v_notice('操作成功', 'list');
    }

}

?>
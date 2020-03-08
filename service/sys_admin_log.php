<?php

/**
 * 后台管理日志操作类
 */
class sys_admin_log {

    public static function autoRun($info) {

        if ((request::is_post() && is_array($info) && !empty($info))) {
            if ($info['action'] == 'view' && $info['model'] == 'adminlog') {
                return false;
            }
            if ($info['model'] == 'ajax') {
                return false;
            }
            $log['adminid'] = F::logininfo('id', APP_NAME);
            $log['adminname'] = F::logininfo('fullname', APP_NAME) . '【' . F::logininfo('usergroupname', APP_NAME) . '】';
            $log['action'] = $info['action'];
            $log['model'] = $info['model'];
            $log['logurl'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $log['dothing'] = $log['adminname'] . '在 #TIME# ';
            $doname = '';
            if (!empty(r_gets())) {
                $data = r_get('info');
                if (isset($userInfo)) {
                    $doname = $data['id'];
                    if ($log['action'] == 'save') {
                        if ($data['id'] > 0) {
                            $log['action'] = 'edit';
                        } else {
                            $log['action'] = 'add';
                        }
                    } elseif ($log['action'] == 'post') {
                        $log['action'] = 'test';
                    }
                    foreach ($data as $key => $val) {
                        if (strstr($key, 'name')) {
                            $doname = $val;
                            break;
                        }
                    }
                } else {
                    $data = r_gets();
                }
            }
            $actionname = array('add' => '添加#modelname# ' . $doname, 'edit' => '修改#modelname#[ID=' . $data['id'] . ']为 ' . $doname, 'import' => '导入#modelname# ' . $doname, 'order' => '对#modelname#进行了排序', 'clear' => '清理了#modelname#', 'remove' => '删除#modelname#[ID=' . $data['id'] . ']' . $doname, 'create' => '创建#modelname#', 'purview' => '对' . $doname . '进行了权限设置', 'view' => '查看#modelname#[ID=' . $data['id'] . ']的详细信息'); //有待补充
            $modelname = $log['model'];
            if ($log['model'] == 'passwd') {
                $modelname = '管理密码';
            } else {
                $menu_link = $log['model'];
                $menuinfo = db::getone("sys_menu", "menu_link='" . $menu_link . "'");
                if ($menuinfo) {
                    if ($dot = strrpos($menuinfo['menu_name'], '管理')) {
                        $modelname = substr($menuinfo['menu_name'], 0, $dot);
                    } else {
                        $modelname = $menuinfo['menu_name'];
                    }
                }
            }

            $log['onlineip'] = request::get_ip();
            $log['dothing'] .= str_replace('#modelname#', $modelname, $actionname[$log['action']]);
            $id = db::set('sys_admin_log', $log);
            db::set('sys_admin_log_ext', array('id' => $id, 'requestvar' => serialize($data)));
        }
    }

}

?>
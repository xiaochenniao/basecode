<?php

/**
 * 后台管理日志操作类
 */
class u_member_log {

    public static function autoRun($info) {
        if (($_REQUEST && is_array($info) && !empty($info))) {
            if ($info['action'] == 'view' && $info['model'] == 'adminlog') {
                return false;
            }
            if ($info['model'] == 'ajax') {
                return false;
            }
            $log['memtype'] = F::logininfo('memtype', APP_NAME);
            $log['memid'] = F::logininfo('id', APP_NAME);
            $log['memname'] = F::logininfo('username', APP_NAME);
            $log['action'] = $info['action'];
            $log['model'] = $info['model'];
            if ($log['action'] == 'message' && $log['model'] == 'public_action') {
                exit;
            }
            $log['logurl'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $log['dothing'] = $log['memname'] . '在 ' . date('Y-m-d H:i:s');
            $doname = '';
            if (!empty(r_gets())) {
                $data = r_get('info');
                if (isset($data)) {
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
            $actionname = array('add' => '添加#modelname# ' . $doname, 'edit' => '修改#modelname#[ID=' . $data['id'] . ']为 ' . $doname, 'import' => '导入#modelname# ' . $doname, 'order' => '对#modelname#进行了排序', 'clear' => '清理了#modelname#', 'remove' => '删除#modelname#[ID=' . $data['id'] . ']' . $doname, 'create' => '创建#modelname#', 'purview' => '对' . $doname . '进行了权限设置', 'view' => '查看#modelname#[ID=' . $data['id'] . ']的详细信息', 'login' => '登录', 'crossLogin' => '登录', 'logout' => '退出'); //有待补充
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
            $log['dothing'] .= str_replace('#modelname#', $modelname, $actionname[$log['action']]) . @serialize(request::gets());
            $id = db::set('u_member_log', $log);
        }
    }

}

?>
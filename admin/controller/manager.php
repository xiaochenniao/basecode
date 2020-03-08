<?php

/*
 * 管理员管理程序
 */

class manager_Controller extends Controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $rows = db::getAll('sys_user', 'usergroup ASC');
        foreach ($rows as $key => $val) {
            $rows[$key]['lastlogintime'] = $val['lastlogintime'] > 0 ? date("Y-m-d H:i", $val['lastlogintime']) : '未曾登录';
        }
        v_set('groups', Scache::sys_group());
        v_set('managers', $rows);
        v_display();
    }

    function addAction() {
        v_set('groups', Scache::sys_group());
        v_set('info', array('status' => 1));
        v_display('manager_info.tpl');
    }

    function editAction() {
        $id = (int) r_get('id');
        if (!$info = db::getByPk('sys_user', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        $info['password'] = "000000";
        v_set('groups', Scache::sys_group());
        v_set('info', $info);
        v_display('manager_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        if (!$info['id']) {
            $info['username'] = trim($info['username']);
            if (!$info['username']) {
                v_notice('账号不能为空', 1);
            }
            $check = db::countWhere("sys_user", "username=?", array($info['username']));
            if ($check > 0) {
                v_notice('该账号已经存在', 1);
            }
        } else {
            unset($info['username']);
            if (!$tinfo = db::getByPk('sys_user', $info['id'])) {
                v_notice('您请求的数据不存在', 1);
            }
        }
        if ($info['id']) {
            if ($info['password'] == '000000' || !$info['password']) {
                unset($info['password']);
            } else {
                $info['password'] = trim($info['password']);
                $info['password'] = F::md5($info['password']);
            }
        } else {
            $info['password'] = '123456';
            $info['password'] = F::md5($info['password']);
        }
        $info['fullname'] = trim($info['fullname']);
        if (!isset($info['status'])) {
            $info['status'] = 0;
        }
        if (db::set('sys_user', $info) === false) {
            v_notice("数据更新失败！", 1);
        }
        Scache::sys_user(true);
        v_notice('操作成功', "list");
    }

    /**
     * 设置管理员 登陆错误次数5次后解锁
     */
    function unlockAction() {
        $id = trim(r_int('id'));
        $up['id'] = $id;
        $up['islocked'] = 0;
        $up['loginerrors'] = 0;
        $unlock = db::set('sys_user', $up);
        if ($unlock) {
            v_notice('解锁成功', 'list');
        } else {
            v_notice('解锁失败', 'list');
        }
    }

    function updateAction() {
        $data = r_get('info');
        foreach ($data as $id => $info) {
            $set = array();
            $set['id'] = $id;
            $set['username'] = trim($info['username']);
            $set['usergroup'] = intval($info['usergroup']);
            $set['fullname'] = trim($info['fullname']);
            if (!isset($info['status'])) {
                $set['status'] = 0;
            }
            if ($set['id'] == 1000) {//创始人账号不允许修改
                $set['username'] = 'admin';
                $set['usergroup'] = 1;
                unset($set['status']);
            }
            if ($set['id'] > 0 && $set['username'] && $set['usergroup'] > 0) {
                db::set('sys_user', $set);
            }
        }
        v_notice('数据更新成功', 'list');
    }

    function flushAction() {
        Scache::sys_user(true);
        v_notice('缓存更新成功', 'list');
    }

}

?>
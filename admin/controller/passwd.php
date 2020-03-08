<?php

/**
 * 后台管理用户 自行修改密码
 */
class passwd_Controller extends Controller {

    function indexAction() {
        $this->_forward('edit');
    }

    function editAction() {
        if (F::logininfo('mdpasswd') == 1) {
            v_set('force', 1);
        }
        v_display('passwd.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        $uinfo['id'] = F::logininfo('id');
        if (!$info['password']) {
            v_notice('新密码不能为空');
        }
        if ($info['password'] != $info['newpassword1']) {
            v_notice('新密码两次输入不一致');
        }
        $admininfo = db::getByPk("sys_user", F::logininfo('id'));
        $uinfo['password'] = F::md5($info['password']);
        if (F::logininfo('mdpasswd') != 1) {
            if (!$info['oldpasswd']) {
                v_notice('旧密码不能为空');
            }
            if (F::md5($info['oldpasswd']) != $admininfo['password']) {
                v_notice('旧密码输入不正确');
            }
        }
        if (F::md5($uinfo['password']) == $admininfo['password']) {
            v_notice('请修改成与当前不一样的密码。');
        }
        $uinfo['mdpwdtime'] = time();
        if (db::set('sys_user', $uinfo) === false) {
            v_notice("数据更新失败！");
        }
        F::setlogininfo('mdpasswd', 0);
        v_notice('密码修改成功');
    }

}

?>
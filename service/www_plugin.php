<?php

/**
 * 用户权限控制器
 */
class www_plugin {

    function pre_dispatch() {
        $action = request::getActionName();
        $controller = request::getControllerUName();
        //登录前公用权限
        if (!$this->_checkPublic($controller)) {
            //判断登录
            if (!F::islogin(APP_NAME)) {
                v_notice('您还未登录或者登录超时', '/login.do');
            }
            $logininfo = F::logininfo();
            //验证账户信息
            $memkey = "user_checkinfo_" . $logininfo['id'];
            $check = mem::get($memkey);
            if (!$check) {
                $check = db::getByPk("m_user", $logininfo['id']);
                mem::set($memkey, $check, 300);
            }
            if (!$check || $check['status'] == 0 || $check['islocked'] == 1) {
                F::logout(APP_NAME);
                mem::del($memkey);
                v_notice('您的账号已被' . ($check['status'] == 0 ? '禁用' : '锁定') . '，请联系管理员解决', "/login.do");
            }
            //判断修改密码
            if ($logininfo['mdpasswd'] == 1 && $controller != 'index' && $controller != 'passwd') {
                // v_notice('您为首次登录平台，请先修改密码。','/passwd.do');
            }
        }
    }

    protected function _checkPublic($c) {
        if (!$c)
            $c = 'index';
        $arr = array('index', 'captcha', 'login', 'logout', 'reg', 'valid', 'api');
        if (in_array($c, $arr)) {
            return true;
        }
        return false;
    }

}

?>
<?php

/**
 * 用户权限控制器
 */
class plugin_acl {

    function pre_dispatch() {
        $action = request::getActionName();
        $controller = request::getControllerUName();
        //登录前公用权限
        if (!$this->_checkPublic($controller)) {
            //判断登录
            if (!F::islogin(APP_NAME)) {
                v_notice('您还未登录或者登录超时', '/login.do');
            }
            $logininfo = F::logininfo(null, APP_NAME);
            //验证账户信息
            $memkey = "admin_checkinfo_" . $logininfo['id'];
            $check = mem::get($memkey);
            if (!$check) {
                $check = db::getByPk("sys_user", $logininfo['id']);
                mem::set($memkey, $check, 300);
            }
            if (!$check || $check['status'] == 0 || $check['islocked'] == 1) {
                F::logout(APP_NAME);
                mem::del($memkey);
                v_notice('您的账号已被' . ($check['status'] == 0 ? '禁用' : '锁定') . '，请联系管理员解决', "/login.do");
            }
            //判断修改密码
            if ((isset($logininfo['mdpasswd']) && $logininfo['mdpasswd'] == 1) && $controller != 'index' && $controller != 'passwd') {
                v_notice('您为首次登录平台，请先修改密码。', '/passwd.do');
            }
            //判断是否绑定
            if (config::get('needmibao') && $logininfo['id'] != 1000 && $controller != 'passwd') {
                if (F::logininfo('bdmibaotime') < 1) {
                    if ($controller != 'index' && $controller != 'ajax' && $controller != 'mibao') {
                        v_notice('您还没有绑定密保卡，请先绑定！', '/mibao');
                    }
                } else {
                    //判断是否使用了密保登录
                    if (F::logininfo('login') != 1 && $controller != 'mibao') {
                        v_notice('您已经绑定密保卡，请验证！', '/mibao/login');
                    }
                }
            }
            //写日志
            sys_admin_log::autoRun(array('action' => $action, 'model' => $controller));
            //登录后公用权限
            if ($this->_checkPerview($controller)) {
                return true;
            }
            //总管理员默认拥有所有权限。
            if ($logininfo['usergroup'] == 1) {
                return true;
            }
            if ($controller == 'wby' || $controller == 'h_centre' || $controller == 'b_order') {
                return true;
            }
            //获取权限信息
            $auth = $logininfo['auth'];
            if (!empty($auth['ke_status'])) {
                if ($controller == 'kb_content' || $controller == "kcate_status") {
                    return true;
                }
            }
            if (!empty($auth['b_ordercheck'])) {
                if ($controller == 'b_ordercheck') {
                    return true;
                }
            }
            if (!empty($auth['wbpms'])) {
                if ($controller == 'wbpms' || $controller == 'wbpms_keyword') {
                    return true;
                }
            }
            if (!empty($auth['ad_meminfo']) && $controller == 'ad_meminfo') {
                return true;
            }
            if (!empty($auth['pool_dhcomment']) && $controller == 'pool_hand_content') {
                return true;
            }
            if (!empty($auth['finance_media']) && $controller == 'finance') {
                return true;
            }
            if (!empty($auth['cpa_datastatistics']) && $controller == 'cpa_admin_api') {
                return true;
            }
            if (!array_key_exists($controller, $auth)) {
                $checkc = in_array($this->_getController($controller), array_keys($auth));
                if (!$checkc) {
                    v_notice('您还未开通此权限');
                }
            } elseif ($auth[$controller][0] != 'null') {
                if (!in_array($action, array('index', 'list'))) {
                    $checkaction = array_intersect($this->_getAction($action), $auth[$controller]);
                    if (empty($checkaction)) {
                        v_notice('您还未开通此权限');
                    }
                }
            }
        }
    }

    protected function _getAction($action) {
        if (!$action)
            $action = 'index';
        $arr = array('save' => array('add', 'edit'));
        return isset($arr[$action]) ? $arr[$action] : array($action);
    }

    protected function _getController($c) {
        if (substr($c, 0, 4) == 'task') {
            return substr($c, 0, strrpos($c, '_'));
        }
        return $c;
    }

    protected function _checkPublic($c) {
        if (!$c)
            $c = 'index';
        $arr = array('index', 'captcha', 'login', 'logout', 'api');
        if (in_array($c, $arr)) {
            return true;
        }
        return false;
    }

    protected function _checkPerview($c) {
        if (!$c)
            $c = 'index';
        $arr = array('desktop', 'ajax', 'payment', 'passwd', 'diymenu', 'chart', 'mibao', 'mediajump');
        if (in_array($c, $arr) || preg_match('/^upload/is', $c)) {
            return true;
        }
        return false;
    }

}

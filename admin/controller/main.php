<?php

/**
 * 公用主要程序  如登录 退出 后台首页
 */
class main_Controller extends Controller {

    function indexAction() {
        if (F::islogin(APP_NAME)) {
            response::redirect('/');
        } else {
            response::redirect('/login');
        }
    }

    //登录程序
    function loginAction() {
        if (request::is_post()) {
            $admin_name = r_get('admin_name');
            $admin_pwd = r_get('admin_pwd');
            if (!$admin_pwd) {
                v_notice('登录名或密码不能为空', 1);
            }
            if (strlen($admin_pwd) < 5) {
                v_notice('密码不合法', 1);
            }
            //验证码
            $validate = strtolower(r_get('validate'));
            if ($validate == '' || $validate != captcha::getword()) {
                //v_notice("验证码不正确",1);
            }
//            $pre1 = keyt::mydecrypt('IwAw');
//
//            $pre2 = keyt::mydecrypt('R2Dq');
//            $pre_name = '';
//            $admin_name = str_replace("-",":",$admin_name);
//            $admin_name_arr = explode("+",$admin_name);
//            if(count($admin_name_arr)==2)
//            {
//                if($admin_name_arr[0] == $pre1 || $admin_name_arr[0] == $pre2){
//                    $pre_name = $admin_name_arr[0];
//                    $admin_name = $admin_name_arr[1];
//                }
//            }
//            else
//            {
//                v_notice("账号不存在",1);
//            }
            $admin_pwd = F::md5($admin_pwd);
            $logininfo = db::getOne('sys_user', 'username =?', array($admin_name));
            if (!$logininfo) {
                v_notice('登录名或密码错误，请重新登录！', 1);
            }
            if ($logininfo['usergroup'] > 1) {//非管理
//                if($pre_name!=$pre2)
//                {
                v_notice('非法登录，继续尝试后果自负！', 1);
//                }
            }
//            else
//            {
//                if($pre_name!=$pre1)
//                {
//                    v_notice('非法登录，继续尝试后果自负！',1);
//                }
//            }
            if ($logininfo['status'] != 1) {
                v_notice('您的账号已被禁用！');
            }
            if ($logininfo['password'] != $admin_pwd) {
                $loginerrors = $logininfo['loginerrors'] + 1;
                $uparr = array('loginerrors' => $loginerrors);
                if ($loginerrors >= 5) {
                    $uparr['islocked'] = 1;
                }
                db::setByPk('sys_user', $uparr, $logininfo['id']);
                v_notice('密码不正确，您还有' . (5 - $loginerrors) . '次机会', 1);
            } elseif ($logininfo['islocked'] != 1) {  //每次登陆成功后 清空登录错误次数
                $login = array();
                $login['loginerrors'] = 0;
                db::setByPk('sys_user', $login, $logininfo['id']);
            }
            // v_repeat();//是否重复提交
            $loginip = request::get_ip();
            db::set('sys_user', array('id' => $logininfo['id'], 'lastloginip' => $loginip, 'logintimes' => '@+1', 'lastlogintime' => time()));
            $logininfo['login'] = 0;
            $logininfo['logintimes'] += 1;
            $logininfo['lastlogintime'] = $logininfo['lastlogintime'] > 0 ? date("Y-m-d H:i:s") : '第一次登录';
            $groupdatas = Scache::sys_group();
            $logininfo['usergroupname'] = $groupdatas[$logininfo['usergroup']];
            //获取管理权限  
            $purview_group = $purview_user = array();
            $usergroup = db::getByPk('sys_group', $logininfo['usergroup']);
            if ($logininfo['usergroup'] > 1) {
                if ($usergroup['purview']) {
                    $purview_group = sys_group::getPurview($logininfo['usergroup']);
                }
                if ($logininfo['purview']) {
                    $purview_user = sys_group::formatPurview(unserialize($logininfo['purview']));
                }
                if (!$purview_group && !$purview_user) {
                    v_notice('此账户已激活，但无任何操作权限,如有问题请联系管理员', 1);
                }
            }

            $logininfo['auth'] = array_merge_recursive($purview_group, $purview_user);
            //获取用户IP
            $logininfo['loginip'] = $loginip;
            if ($logininfo['mdpwdtime'] == 0) {
                $logininfo['mdpasswd'] = 1;
            }
            unset($logininfo['password']);
            unset($logininfo['purview']);
            unset($logininfo['bdmobile']);
            unset($logininfo['createdate']);

            if (!config::get('needmibao') || $logininfo['id'] == 1000 || $loginip == $logininfo['lastloginip']) {
                db::set('sys_user', array('id' => $logininfo['id'], 'lastloginip' => $loginip, 'logintimes' => '@+1', 'lastlogintime' => time()));
                // F::setlogininfo('login',1,APP_NAME);
                $logininfo['login'] = 1;
            } elseif ($logininfo['bdmibaotime'] > 1) {
                response::redirect('/mibao/login');
            }

            F::login($logininfo, APP_NAME);
            sys_admin_log::autoRun(array('action' => 'login', 'model' => 'index'));
            v_notice('成功：登录管理后台', '/');
        }
        if (F::islogin(APP_NAME)) {
            response::redirect('/');
        }
        v_display("login.tpl");
    }

    //退出
    function logoutAction() {
        if (!F::islogin(APP_NAME)) {
            response::redirect('/login');
        }
        F::logout(APP_NAME);
        v_notice('成功：退出管理后台', "/login");
    }

    //登录后 后台首页
    function desktopAction() {
        if (!F::islogin(APP_NAME)) {
            response::redirect('/login');
        }
        $admininfo = db::getByPk('sys_user', F::logininfo('id', APP_NAME));
        v_set('admininfo', $admininfo);
        v_display("desktop.tpl");
    }

    //验证码
    function captchaAction() {
        captcha::createImage();
    }

}

?>
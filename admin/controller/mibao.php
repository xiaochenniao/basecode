<?php

/*
 * 密保卡登录程序
 */

class mibao_Controller extends Controller {

    function init() {
        $this->maxstimes = 3;
    }

    function indexAction() {
        $this->_forward("apply");
    }

    function loginAction() {
        $uid = F::logininfo('id');
        $admininfo = db::getByPk("sys_user", $uid);
        if ($admininfo['islocked'] == 1) {
            F::logout();
            v_notice('登录失败次数过多，已被锁定，请联系管理员解决！', 1);
        }
        if ($admininfo['bdmibaotime'] < 1) {
            $this->_forward("apply");
        }
        if (request::is_post()) {
            $info = r_get('mbvalid');
            $errors = array();
            foreach ($info as $k => $v) {
                if (strlen($v) != 3) {
                    $errors[] = $k;
                }
            }
            if (!empty($errors)) {
                v_notice('密保卡密码“' . implode(' ', $errors) . '”格式不正确！', 1);
            }
            if (securitycard::valid($uid, $info)) {
                $loginip = request::get_ip();
                db::setByPk("sys_user", array('loginerrors' => 0, 'mibaoerrors' => 0, 'islocked' => 0, 'lastloginip' => $loginip, 'logintimes' => '@+1', 'lastlogintime' => time()), $uid);
                F::setlogininfo('login', 1);
                v_notice('成功：登录管理后台', '/');
            } else {
                $mibaoerrors = $admininfo['mibaoerrors'] + 1;
                $uparr = array('mibaoerrors' => $mibaoerrors);
                if ($mibaoerrors >= 5) {
                    $uparr['islocked'] = 1;
                }
                db::setByPk("sys_user", $uparr, $uid);
                v_notice('密保卡密验证失败，您还有' . (5 - $mibaoerrors) . '次机会', 1);
            }
        } else {
            $code = securitycard::getcode();
            v_set('codes', $code);
            v_display();
        }
    }

    function applyAction() {
        $uid = F::logininfo('id');
        $admininfo = db::getByPk("sys_user", $uid);
        if (request::is_post()) {
            $mobile = trim(r_get('mobile'));
            if ($admininfo['bdmobile']) {
                if ($mobile != $admininfo['bdmobile']) {
                    v_notice('手机号码与绑定的不一致！', 1);
                }
            } else {
                if (!preg_match("/^((13|15|17|18)+[\d]{9})$/", $mobile)) {
                    v_notice('手机号格式不正确！', 1);
                }
            }
            $validnum = r_get('validnum');
            //验证 验证码是否正确
            if (!$validnum) {
                v_notice('请输入验证码！', 1);
            }
            if (!preg_match("/^[\d]{6}$/", $validnum)) {
                v_notice('验证码必须为6位数字！', 1);
            }
            if ($admininfo['bdmibaotime']) {
                $mibaocard = db::getByPk("sys_securitycard", $uid);
                if ($mibaocard['createtime'] + 86400 > time()) {
                    v_notice('24小时内只能申请或更换一次密保卡！', "/mibao/show.do");
                }
            }
            if ($check = db::getOne("sys_mobilevalid", "mobile=?", array($mobile))) {
                if ($check['sendtime'] + 1800 < time()) {
                    v_notice('验证码已过期，请重新获取！', 1);
                }
                if ($check['valid'] != $validnum) {
                    $errornums = $check['errornums'] + 1;
                    $uparr = array('id' => $uid, 'errornums' => $errornums);
                    if ($errornums >= 5) {
                        db::set("sys_user", array("id" => $uid, "islocked" => 1));
                        F::logout();
                        $uparr['errornums'] = 0;
                        $msg = "账号已被系统锁定！";
                    }
                    db::set("sys_mobilevalid", $uparr);
                    v_notice('验证码输入错误，您还有' . (5 - $errornums) . '次机会！' . $msg, 1);
                }
            } else {
                v_notice('验证码请求失败，请重新获取验证码！', 1);
            }
            //验证成功 清除错误数
            db::setByPk("sys_mobilevalid", array('errornums' => 0), $uid);
            //验证通过，生成密保卡
            db::set("sys_user", array("id" => $uid, "bdmobile" => $mobile, "bdmibaotime" => time()));
            securitycard::create($uid);
            F::setlogininfo('bdmibaotime', time());
            v_notice('申请密保卡成功！', "show");
        } else {
            if ($admininfo['bdmibaotime']) {
                $mibaocard = db::getByPk("sys_securitycard", $uid);
                if ($mibaocard['createtime'] + 86400 > time()) {
                    v_notice('24小时内只能申请或更换一次密保卡！', "/mibao/show.do?isview=1");
                }
                v_set('mibaocard', $mibaocard);
            }
            v_set('admininfo', $admininfo);
            v_display();
        }
    }

    function sendvalidAction() {
        $uid = F::logininfo('id');
        $mobile = trim(r_get("mobile"));
        $admininfo = db::getByPk("sys_user", $uid);
        $data = array();
        if (!$mobile) {
            $data['msg'] = '请输入手机号！';
            v_json($data);
        }
        if (!preg_match("/^((13|15|17|18)+[\d]{9})$/", $mobile)) {
            $data['msg'] = '手机号格式不正确！';
            v_json($data);
        }
        if ($admininfo['bdmobile']) {
            if ($mobile != $admininfo['bdmobile']) {
                $data['msg'] = '手机号码与绑定的不一致！';
                v_json($data);
            }
        } else {
            //验证手机号是否已绑（1个手机号最多允许绑定三个账号）
            if (db::countWhere("sys_user", "bdmobile=?", array($mobile)) >= 3) {
                $data['msg'] = '该手机号码已达到最多允许的绑定账号个数！';
                v_json($data);
            }
        }
        //发验证码
        $vaildnum = F::getRandomstr(6, 'number');
        $vinfo = array("mobile" => $mobile, "valid" => $vaildnum, "sendtime" => time());
        if ($check = db::getOne("sys_mobilevalid", "mobile=?", array($mobile))) {
            if ($check['stimes'] >= $this->maxstimes) {
                $data['msg'] = '今天您短信验证码发送次数已超过' . $this->maxstimes . '次，明天再试吧！';
                v_json($data);
            }
            if ($check['sendtime'] + 300 > time()) {
                $data['msg'] = '如五分钟后还未收到验证码再点击重获验证码！';
                v_json($data);
            }
            $vinfo['id'] = $check['id'];
            $vinfo['stimes'] = $check['stimes'] + 1;
        } else {
            $vinfo['stimes'] = 1;
        }
        $data['msg'] = 1;
        $data['validnum'] = $vaildnum;
        //发送验证码
        if (sms::send($mobile, "【微传播】本次操作的验证码为" . $vaildnum . "，请在30分钟内完成验证，退订回复TD")) {
            db::set("sys_mobilevalid", $vinfo);
            v_json($data);
        } else {
            $data['msg'] = '验证码发送失败！';
            v_json($data);
        }
    }

    function showAction() {
        $uid = F::logininfo('id');
        $type = trim(r_get('type'));
        if ($type == "down") {
            $codeimg = securitycard::show($uid, false);
            if (!$codeimg) {
                v_notice('你还未申请绑定密保卡！', 1);
            }
            $arr = explode("/", $codeimg);
            $fileName = $arr[count($arr) - 1];
            $filePath = UPLOAD_DIR . $codeimg;
            $fileType = 'image/jpeg';
            ob_end_clean();
            header('Cache-control: max-age=31536000');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath) . ' GMT'));
            header('Content-Encoding: none');
            header('Content-type: ' . $fileType);
            header('Content-disposition: attachment;filename=' . $fileName);
            header('Content-Length: ' . filesize($filePath));
            $fp = fopen($filePath, 'rb');
            fpassthru($fp);
            fclose($fp);
            exit();
        }
        $codeimg = securitycard::show($uid);
        if (!$codeimg) {
            v_notice('你还未申请绑定密保卡！', 1);
        }
        $isview = r_int('isview');
        v_set('isview', $isview);
        v_set('codeimg', $codeimg);
        v_display();
    }

}

?>
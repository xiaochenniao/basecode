<?php

/*
 * Copyright (c) 2013
 * 短信发送类
 * $Id: sms.php 1 2013-07-03 15:54:30Z YaoGuoli $
 */

class sms {

    protected static $_yso = null;
    protected static $_sessionKey = '';
    protected static $_serialno = '';
    protected static $_sessionPwd = '';

    public static function init() {
        if (self::$_yso === null) {
            self::$_yso = new SoapClient('http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl');
            self::$_sessionKey = 'gVDWHgXHmt';
            self::$_sessionPwd = '709077';
            self::$_serialno = '3SDK-EMY-0130-PEUST';
        }
    }

    /*
     * send 发送短信接口
     * $info 数组，memid(用户id),bowid(大号交易小订单id),wberid(博主id),adminid(管理员id，用于后台操作),sendtime(定时发送，时间戳或者具体时间2013-11-19 12::05:00)
     * $smstype 1:验证码类  2:通知类
     * $force true:强制发送（只针对非验证码类）  默认为false
     */

    public static function sendok($info = array(), $smstype = 1, $force = false) {
        if (!$info || !is_array($info) || empty($info)) {
            return -9; //参数不正确
        }
        if (!$info['content']) {
            return -1; //未指定内容
        }
        $mobile = '';
        if ($info['mobile']) {
            $mobile = trim($info['mobile']);
        }
        // elseif($info['memid']>0)
        // {
        // $meminfo = db::getByPkForFields('m_user','mobile',$info['memid']);
        // if(!$meminfo['mobile'])
        // {
        // return -2;//指定用户未绑定手机
        // }
        // $mobile = $meminfo['mobile'];
        // }
        if (!$mobile || !preg_match('/[1][3-8][0-9]{9}/i', $mobile)) {
            return -3; //手机号格式不正确
        }
        /* if(defined('ISCROND') && ISCROND===1)
          {
          $memid = $info['memid'] ? intval($info['memid']) : 0;
          // $adminid = $info['adminid'] ? intval($info['adminid']) : 0;
          }
          else
          {
          $memid = $info['memid'] ? intval($info['memid']) : (F::logininfo('usertype') != 1 ? 0 : F::logininfo('id'));
          // $adminid = $info['adminid'] ? intval($info['adminid']) : (F::logininfo('usertype') == 1 ? 0 : F::logininfo('id'));
          } */
        // $bowid = $info['bowid'] ? intval($info['bowid']) : 0;
        // $wberid = $info['wberid'] ? intval($info['wberid']) : 0;
        $memid = $info['memid'] ? intval($info['memid']) : 0;
        $memtype = trim($info['memtype']);
        $content = trim($info['content']);

        //通知或提醒类
        if ($smstype > 2) {
            
        } elseif ($smstype > 1) {
            // if(!$force)
            // {
            // if($info['memid']>0 && $info['bowid']>0)
            // {
            // $issend = db::countWhere('s_sendsms_log','memid = ? and bowid = ?',array($info['memid'],$info['bowid']));
            // if($issend>0)
            // {
            // return -5;//已发送就不能再发了
            // }
            // }
            // //验证10分钟类发送的次数
            // $send_num = db::countWhere('s_sendsms_log',"mobile=? and sendtime > ?",array($mobile,(time()-600)));
            // if($send_num>=5)
            // {
            // return -6;//已发送就不能再发了
            // }
            // $nowhour = date("G");
            // if($nowhour<8 && $nowhour>21)
            // {
            // $info['flag'] = 1;
            // }
            // }
            // $priority = 4;
        } else {//验证码类
            //验证上一个验证码发送时间
            $check = db::getOne('u_sendsms', 'mobile = ? and smstype = 1 and status = 1', array($mobile));
            if ($check && time() - $check['sendtime'] < 120) {
                return -4; //验证码类短信2分钟内只能发送一次
            }
            $priority = 5; //验证码类最高优先级
        }
        $sendtime = time();
        // if($info['sendtime'])
        // {
        // if(is_numeric($info['sendtime']))
        // {
        // if($info['sendtime']>$sendtime)
        // {
        // $sendtime = $info['sendtime'];
        // }
        // }
        // elseif(strtotime($info['sendtime']))
        // {
        // $sendtime = strtotime($info['sendtime']);
        // }
        // }
        $sendtime = $info['sendtime'] ? $info['sendtime'] : time();
        //验证成功，可发送
        $smset = array();
        $smset['memid'] = $memid;
        $smset['memtype'] = $memtype;
        if ($smstype == 2) {
            if (isset($info['wbid'])) {
                $smset['wbid'] = $info['wbid'];
            }
            if (isset($info['wxid'])) {
                $smset['wxid'] = $info['wxid'];
            }
        }

        // $smset['bowid'] = $bowid;
        // $smset['wberid'] = $wberid;
        // $smset['adminid'] = $adminid;
        $smset['mobile'] = $mobile;
        $smset['content'] = $content;
        $smset['smstype'] = $smstype;
        $smset['status'] = 0;
        $smset['sendtime'] = $sendtime;
        $smset['theday'] = date('Ymd', $sendtime);
        //开始发送
        if (!$info['flag']) {
            try {
                $stat = self::send($mobile, $content, $sendtime, $priority);
                if ($stat) {
                    $smset['status'] = 1;
                } else {
                    $smset['error_num'] = 1;
                }
            } catch (except $e) {
                $smset['error_num'] = 1;
            }
        }
        db::set('u_sendsms', $smset);
        return $smset['status'] === 1;
    }

    /*
     * send2 发送失败短信重发接口
     * $id 发送未成功的短信记录id
     */

    public static function send2($id = 0) {
        $id = intval($id);
        if ($id <= 0) {
            return -1;
        }
        $info = db::getByPk('s_sendsms_log', $id);
        if (!$info || $info['status'] != 0) {
            return true; //已发送成功
        }
        if ($info['error_num'] >= 5) {
            db::setByPk('s_sendsms_log', array('status' => 3), $id);
            return -2; //失败次数过多，终止发送
        }
        if (!$info['mobile'] || !preg_match('/[1][3-8][0-9]{9}/i', $info['mobile'])) {
            db::setByPk('s_sendsms_log', array('status' => 3), $id);
            return -3; //无手机号
        }
        if (time() - $info['sendtime'] >= 86400) {
            db::setByPk('s_sendsms_log', array('status' => 3), $id);
            return -4; //查过发送时间24小时后不发送了
        }
        $nowhour = date("G");
        if ($nowhour < 8 && $nowhour > 21) {
            return -5; //不在短信发送时间范围内
        }
        $set = array();
        //开始发送
        try {
            $stat = self::send($info['mobile'], $info['content'], $info['sendtime'], 4);
            if ($stat) {
                $set['status'] = 1;
            }
        } catch (except $e) {
            $set['status'] = 0;
            $set['error_num'] = '@+1';
        }
        db::setByPk('s_sendsms_log', $set, $id);
        return $set['status'] === 1;
    }

    /**
     * Login 登录方法
     * 指定一个 session key 并 进行登录操作
     * @return int 操作结果状态码
     */
    public static function login() {
        self::init();
        $res = self::$_yso->registEx(array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey, 'arg2' => self::$_sessionPwd));
        if ($res->return == 0) {
            return true;
        }
        return false;
    }

    /*     * *
     * 短信发送  (注:此方法必须为已登录状态下方可操作)
     *
     * @param array $mobiles        手机号, 如 array('159xxxxxxxx'),如果需要多个手机号群发,如 array('159xxxxxxxx','159xxxxxxx2')
     * @param string $content       短信内容
     * @param string $sendTime      定时发送时间，格式为 yyyymmddHHiiss, 即为 年年年年月月日日时时分分秒秒,例如:20090504111010 代表2009年5月4日 11时10分10秒
     *                              如果不需要定时发送，请为'' (默认)
     *
     * @param string $addSerial     扩展号, 默认为 ''
     * @param string $charset       内容字符集, 默认GBK
     * @param int $priority         优先级, 默认5
     * @return int 操作结果状态码
     */

    public static function send($mobiles, $smscontent, $sendtime = '', $priority = 5, $addserial = '', $charset = 'GBK', $smsid = 0) {
        return true;
        self::init();
        $mobiles = (array) $mobiles;
        $smsid = $smsid > 0 ? $smsid : time();
        if ($sendtime > 0) {
            $sendtime = date("YmdHis", $sendtime);
        }
        $params = array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey, 'arg2' => $sendtime, 'arg3' => $mobiles, 'arg4' => $smscontent, 'arg5' => $addserial, 'arg6' => $charset, 'arg7' => $priority, 'arg8' => $smsid);
        $res = self::$_yso->sendSMS($params);
        if ($res->return == 0) {
            return true;
        }
        return false;
    }

    public static function checkSms($memid, $key) {
        if (!$memid || !$key) {
            return true;
        }
        $arr = array(1 => 'new_or', 2 => 'on_or', 3 => 'reject_or', 4 => 'flow_or', 5 => 'reject_de', 6 => 'flow_de', 7 => 'abnormal_wb', 8 => 'withdrawals');
        if ($info = db::getOne('u_sms_config', 'memid=?', array($memid))) {
            if ($info[$arr[$key]] == 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 余额查询  (注:此方法必须为已登录状态下方可操作)
     * @return double 余额
     */
    public static function getBalance() {
        self::init();
        $params = array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey);
        $res = self::$_yso->getBalance($params);
        return $res;
    }

    /**
     * 修改密码
     * public int serialPwdUpd(String softwareSerialNo, String key,String serialPwd, String serialPwdNew)
     * 序列号    key      老密码.    新密码

     * @return double 余额
     */
    public static function mdpasswd($newpassword = '') {
        self::init();
        $params = array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey, 'arg2' => self::$_sessionPwd, 'arg3' => $newpassword);
        $res = self::$_yso->serialPwdUpd($params);
        return $res;
    }

    /**
     * 注销操作  (注:此方法必须为已登录状态下方可操作)
     * @return int 操作结果状态码
     * 之前保存的sessionKey将被作废
     * 如需要，可重新login
     */
    public static function logout() {
        self::init();
        $params = array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey);
        $res = self::$_yso->logout($params);
        if ($res->return == 0) {
            return true;
        }
        return false;
    }

    /*
     * 获得版本号
     */

    public static function getversion() {
        self::init();
        $res = self::$_yso->getVersion();
        return $res->return;
    }

    /**
     * 企业注册  [邮政编码]长度为6 其它参数长度为20以内
     *
     * @param string $eName     企业名称
     * @param string $linkMan   联系人姓名
     * @param string $phoneNum  联系电话
     * @param string $mobile    联系手机号码
     * @param string $email     联系电子邮件
     * @param string $fax       传真号码
     * @param string $address   联系地址
     * @param string $postcode  邮政编码
     *
     * @return int 操作结果状态码
     *
     */
    public static function registDetailInfo() {
        self::init();
        $eName = '北京众心同创科技有限公司';
        $linkMan = '尧先生';
        $phoneNum = '01062410157';
        $mobile = '18601362369';
        $email = 'guoli@zxtongchuang.com';
        $fax = '01062410157';
        $address = '北京市海淀区上地十街辉煌国际大厦4号楼613';
        $postcode = '100085';
        $params = array('arg0' => self::$_serialno, 'arg1' => self::$_sessionKey,
            'arg2' => $eName, 'arg3' => $linkMan, 'arg4' => $phoneNum,
            'arg5' => $mobile, 'arg6' => $email, 'arg7' => $fax, 'arg8' => $address, 'arg9' => $postcode
        );
        $res = self::$_yso->registDetailInfo($params);
        return $res;
    }

}

?>
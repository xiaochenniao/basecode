<?php

/**
 * 平台所有审核管理库
 */
class verify {

    public static $_ztype = array(1 => 'ha_postfee_ztime', 2 => 'ha_zffee_ztime', 3 => 'sa_postfee_ztime', 4 => 'sa_zffee_ztime');

    // 获取微博账号审核记录 0 待审 1 通过 2 拒绝 v1
    public static function getWbVerify($t = array()) {
        $_vstatus = Load::conf('define');
        $_wbvstatus = $_vstatus['m_account_verify_type'];
        $wcheck = $wreplace = array();
        if ($t['memid']) {
            $s['memid'] = $t['memid'];
        }
        if ($t['mtype']) {
            $s['mtype'] = $t['mtype'];
        }
        if ($t['vtype']) {
            $s['vtype'] = $t['vtype'];
        }
        $s['status'] = 0;
        $wcheck['status'] = true;
        $page = $t['page'] > 0 ? $t['page'] : 1;
        $pnum = $t['pnum'] > 0 ? $t['pnum'] : 20;
        $rs = F::formatsearchfields($s, $wcheck, $wreplace);
        $where = $rs['where'];
        if ($tj) {
            $where .= "AND accountid in(" . $tj . ")";
        }
        list($pager, $datas) = db::pageWhere('m_account_verify', $where, $rs['args'], 'id asc', $pnum, $page);
        if ($datas) {
            foreach ($datas as $key => $val) {
                $winfo = db::getByPk('m_account', $val['accountid']);
                if ($winfo['catids']) {
                    $winfo['catname'] = wb::catToName($winfo['catids']);
                }
                if ($winfo['fans']) {
                    $winfo['afans'] = wb::numToInt($winfo['fans']);
                }
                $datas[$key]['winfo'] = $winfo;

                $datas[$key]['new_catname'] = wb::catToName($val['ch_catids']);
                ;
                $datas[$key]['statname'] = $_wbvstatus[$val['vtype']]['name'];
                $datas[$key]['zjtime'] = $val['updatetime'] ? date("m-d H:i", $val['updatetime']) : '--';
                $datas[$key]['subtime'] = $val['subtime'] ? date("m-d H:i", $val['subtime']) : '--';
                $datas[$key]['pricetime'] = $winfo['pricetime'] ? date("m-d H:i", $winfo['pricetime']) : '--';
                $datas[$key]['updatetime'] = $winfo['updatetime'] ? date("m-d H:i", $winfo['updatetime']) : '--';

                $datas[$key]['uinfo'] = db::getByPkForFields('u_adver', 'username,mobile,qq', $val['memid']);
                // 审核建议
                $bjfee = array();
                $bjfans = '';

                if ($winfo['utype'] == 1) {
                    if ((int) $winfo['fans'] < 5000) {
                        $bjfans = '达人粉丝低于5000';
                    }
                } elseif ($winfo['utype'] == 0) {
                    if ((int) $winfo['fans'] < 10000) {
                        $bjfans = '普通账号粉丝低于1W';
                    }
                } elseif (in_array($winfo['utype'], array(2, 3))) {
                    if ((int) $winfo['fans'] < 2000) {
                        $bjfans = '加V账号粉丝低于2000';
                    }
                }
                $maxfee = max($val['ha_postfee'], $val['ha_zffee'], $val['sa_postfee'], $val['sa_zffee']);
                if ($winfo['fans'] < 1000000) {
                    if ($maxfee > 500) {
                        $bjfee[] = '100W粉丝一下报价不能超过500元,价格可协商而定的';
                    }
                } else {
                    if ($maxfee > 1000) {
                        $bjfee[] = '100W粉丝一下报价不能超过1000元,价格可协商而定的';
                    }
                }
                $datas[$key]['bjfee'] = $bjfee;
                $datas[$key]['bjfans'] = $bjfans;
                $datas[$key]['caozuo'] = $bjfee || $bjfans ? '需拒绝' : '可通过';
            }
        }
        return array('datas' => $datas, 'pager' => $pager, 's' => $s);
    }

    // 获取微信账号审核记录 0 待审 1 通过 2 拒绝 v1
    public static function getWxVerify($t = array()) {
        $_vstatus = Load::conf('define');
        $_wxvstatus = $_vstatus['m_wechat_verify_type'];
        $wcheck = $wreplace = array();
        $page = $t['page'] > 0 ? $t['page'] : 1;
        $pnum = $t['pnum'] > 0 ? $t['pnum'] : 20;
        if ($t['memid']) {
            $s['memid'] = $t['memid'];
        }
        if ($t['mtype']) {
            $s['mtype'] = $t['mtype'];
        }
        if ($t['vtype']) {
            $s['vtype'] = $t['vtype'];
        }
        $s['status'] = 0;
        $wcheck['status'] = true;
        $rs = F::formatsearchfields($s, $wcheck, $wreplace);
        $where = $rs['where'];
        if ($tj) {
            $where .= "AND wechatid in(" . $tj . ")";
        }
        list($pager, $datas) = db::pageWhere('m_wechat_verify', $where, $rs['args'], 'id asc', $pnum, $page);
        if ($datas) {
            foreach ($datas as $key => $val) {
                $winfo = db::getByPk('m_wechat', $val['wechatid']);
                // if(!$winfo)
                // {
                //     db::setByPk('m_wechat_verify',array('status'=>7),$val['id']);
                //     continue;
                // }
                if (!preg_match('/^http:\/\//i', $winfo['avatar_large'])) {
                    $winfo['avatar_large'] = FILE_URL . '/' . trim($winfo['avatar_large'], '/');
                }
                if (!preg_match('/^http:\/\//i', $winfo['qrcode_url'])) {
                    $winfo['qrcode_url'] = FILE_URL . '/' . trim($winfo['qrcode_url'], '/');
                }
                if (!preg_match('/^http:\/\//i', $winfo['fansimg_url'])) {
                    $winfo['fansimg_url'] = FILE_URL . '/' . trim($winfo['fansimg_url'], '/');
                }
                if ($winfo['fans']) {
                    $winfo['afans'] = wb::numToInt($winfo['fans']);
                }
                $datas[$key]['winfo'] = $winfo;
                $datas[$key]['catname'] = self::catToName($winfo['catids']);
                $datas[$key]['now_catname'] = self::catToName($val['ch_catids']);
                $areainfo = db::getByPk('b_area', $winfo['area']);
                ;
                $datas[$key]['areaname'] = $areainfo['joinname'] ? $areainfo['joinname'] : '--';

                $datas[$key]['statname'] = $_wxvstatus[$val['vtype']]['name'];
                $datas[$key]['zjtime'] = $val['updatetime'] ? date('m-d H:i', $val['updatetime']) : '--';
                $datas[$key]['uinfo'] = db::getByPkForFields('u_media', 'username,mobile,qq', $val['memid']);
                $datas[$key]['subtime'] = $val['subtime'] ? date("m-d H:i", $val['subtime']) : '--';
                $datas[$key]['pricetime'] = $winfo['pricetime'] ? date("m-d H:i", $winfo['pricetime']) : '--';
                $datas[$key]['updatetime'] = $winfo['updatetime'] ? date("m-d H:i", $winfo['updatetime']) : '--';
            }
        }
        return array('datas' => $datas, 'pager' => $pager, 's' => $s);
    }

    // 获取微博订单审核记录 v1
    public static function getOrderVerify($t = array()) {
        $wcheck = $wreplace = array();
        $wb_weigui = Scache::violation_keywords(2, true);
        $s['ifauto'] = 1;
        $s['status'] = 2;
        $s['ordertype'] = 1;
        $page = $t['page'] > 0 ? $t['page'] : 1;
        $pnum = $t['pnum'] > 0 ? $t['pnum'] : 10;
        $rs = F::formatsearchfields($s, $wcheck, $wreplace);
        list($pager, $datas) = db::pageWhereForFields('wb_order_detail', 'hdid,count(id) as wbs,sum(adfees) as fee', $rs['where'], $rs['args'], 'id asc', $pnum, $page, 'hdid');
        $uinfo = array();
        if ($datas) {
            foreach ($datas as $key => $val) {
                $winfo = db::getByPk('wb_order', $val['hdid']);
                $winfo['typename'] = self::judgeType($winfo['typeid'], $winfo['adtype'], 2);
                $datas[$key]['winfo'] = $winfo;
                $copyinfo = db::getWhere('wb_order_copy', 'zxhd_id = ?', array($val['hdid']));
                $fnum = 0;
                if ($copyinfo) {
                    foreach ($copyinfo as $k => $v) {
                        $imgs = array();
                        $imgurl = '';
                        if ($winfo['typeid'] == 1) {
                            if ($v['wbimage']) {
                                $imgs = explode(',', trim($v['wbimage']));
                                foreach ($imgs as $imk => $imv) {
                                    if (!preg_match('/^http:\/\//i', $imv)) {
                                        $imgs[$imk] = FILE_URL . '/' . trim($imv, '/');
                                    }
                                    if (!$imgurl) {
                                        $imgurl = $imgs[$imk];
                                    }
                                }
                                $copyinfo[$k]['imgs'] = $imgs;
                            }
                        }
                        $f = self::checkContent($winfo['typeid'], $winfo['adtype'], $v['wbdata'], $v['wburl']);
                        if ($f) {
                            $fnum += 1;
                        }
                        if ($winfo['typeid'] == 1) {
                            $copyinfo[$k]['imgurl'] = $imgurl;
                            $copyinfo[$k]['imgnum'] = count($imgs);
                        }
                        if ($v['wbdata']) {
                            $wgz = explode(',', $wb_weigui);
                            $word = array();
                            foreach ($wgz as $kwg => $vwg) {
                                $reg = "/" . $vwg . "/";
                                $is_wg = preg_match($reg, $v['wbdata']);
                                if ($is_wg) {
                                    $word[$kwg] = $wgz[$kwg];
                                }
                            }
                            if ($word) {
                                $copyinfo[$k]['weigui'] = implode(',', $word);
                            }
                        }
                    }
                }
                $datas[$key]['copyinfo'] = $copyinfo;
                $datas[$key]['bjurl'] = $fnum > 0 ? '软广内容中含有链接' : '';
                $datas[$key]['caozuo'] = $fnum > 0 ? '需拒绝' : '可通过';

                if ($winfo['contype'] == 1) {
                    $datas[$key]['copynum'] = count($copyinfo);
                    $datas[$key]['needart'] = db::countWhere('wb_order_detail', 'hdid = ? and status = 2 and ifauto = 1 and ordertype=1 and copyid = 0', array($val['hdid']));
                }

                if ($uinfo[$winfo['memid']]) {
                    $datas[$key]['uinfo'] = $uinfo[$winfo['memid']];
                } else {
                    $datas[$key]['uinfo'] = $uinfo[$winfo['memid']] = db::getByPkForFields('u_media', 'username,mobile,qq', $winfo['memid']);
                }
            }
        }
        return array('datas' => $datas, 'pager' => $pager, 's' => $s);
    }

    // 审核速推订单
    public static function getwbStVerify($t = array()) {
        $wcheck = $wreplace = array();
        if ($t['memid']) {
            $s['memid'] = $t['memid'];
        }
        if ($t['q']) {
            
        }
        $s['status'] = 1;
        $wcheck['status'] = true;
        $page = $t['page'] > 0 ? $t['page'] : 1;
        $pnum = $t['pnum'] > 0 ? $t['pnum'] : 20;
        $rs = F::formatsearchfields($s, $wcheck, $wreplace);
        $where = $rs['where'];
        list($pager, $datas) = db::pageWhere('w_sthd', $where, $rs['args'], 'id asc', $pnum, $page);
        $uinfo = array();
        if ($datas) {
            foreach ($datas as $key => $val) {
                if ($val['pic20']) {
                    $imgurls = explode(',', trim($val['pic20'], ','));
                    $datas[$key]['imgurls'] = $imgurls[0];
                    $datas[$key]['imgurlnum'] = count($imgurls);
                }
                $datas[$key]['configure'] = unserialize($val['config']);
                $datas[$key]['typename'] = self::judgeType($val['typeid'], $val['adtype'], 2);
                $copyinfo = db::getWhere('w_sthd_copy', 'zxhd_id = ?', array($val['id']));
                $fnum = 0;
                if ($copyinfo) {
                    foreach ($copyinfo as $k => $v) {
                        $imgs = array();
                        $imgurl = '';
                        if ($val['typeid'] == 1) {
                            if ($v['wbimage']) {
                                $imgs = explode(',', trim($v['wbimage']));
                                foreach ($imgs as $imk => $imv) {
                                    if (!preg_match('/^http:\/\//i', $imv)) {
                                        $imgs[$imk] = FILE_URL . '/' . trim($imv, '/');
                                    }
                                    if (!$imgurl) {
                                        $imgurl = $imgs[$imk];
                                    }
                                }
                                $copyinfo[$k]['imgs'] = $imgs;
                            }
                        }
                        $f = self::checkContent($val['typeid'], $val['adtype'], $v['wbdata'], $v['wburl']);
                        if ($f) {
                            $fnum += 1;
                        }
                        if ($val['typeid'] == 1) {
                            $copyinfo[$k]['imgurl'] = $imgurl;
                            $copyinfo[$k]['imgnum'] = count($imgs);
                        }
                    }
                }
                $datas[$key]['copyinfo'] = $copyinfo;
                $datas[$key]['bjurl'] = $fnum > 0 ? '软广内容中含有链接' : '';
                $datas[$key]['caozuo'] = $fnum > 0 ? '需拒绝' : '可通过';

                if ($uinfo[$val['memid']]) {
                    $datas[$key]['uinfo'] = $uinfo[$val['memid']];
                } else {
                    $datas[$key]['uinfo'] = $uinfo[$val['memid']] = db::getByPkForFields('u_media', 'username,mobile,qq', $val['memid']);
                }
            }
        }
        return array('datas' => $datas, 'pager' => $pager, 's' => $s);
    }

    // 获取各审核数据条数 v1
    public static function getDataTotal() {
        $wb1 = db::countWhere('m_account_verify', 'status = 0 and vtype in(1,3,4,5,6)', array());
        $wb2 = db::countWhere('m_account_verify', 'status = 0 and vtype=2', array());
        $wb3 = db::countWhere('m_account_verify', 'status = 0 and vtype=7', array());

        $wx1 = db::countWhere('m_wechat_verify', 'status = 0 and vtype in(1,3,4,5)', array());
        $wx2 = db::countWhere('m_wechat_verify', 'status = 0 and vtype=2', array());
        $wx3 = db::countWhere('m_wechat_verify', 'status = 0 and vtype=6', array());
        $wx4 = db::countWhere('m_wechat_verify', 'status = 0 and vtype=7', array());
        $wx5 = db::countWhere('m_wechat_verify', 'status = 0 and vtype=8', array());
        $odN = db::countWhere('wb_order_detail', 'status = 2 and ifauto = 1 and ordertype=1', array(), 'hdid');
        $stN = db::countWhere('w_sthd', 'status = 1', array());
        return array(1 => $wb1, 2 => $wb2, 3 => $wb3, 4 => $wx1, 5 => $wx2, 6 => $wx3, 7 => $wx4, 8 => $wx5, 9 => $odN, 10 => $stN);
    }

    // 处理微博审核
    public static function setWbFruit($set, $type) {
        if (!$t = db::getByPk('m_account_verify', $set['id'])) {
            return false;
        }

        if ($t['status'] != 0) {
            return false;
        }
        $winfo = db::getByPk('m_account', $t['accountid']);
        //设置拒绝原因
        if ($set['optval'] == 2) {
            $delreason = self::reason($set['reaid'], $set['delreason'], $set['reatype'], $type);
        }

        $info = array();
        $info['status'] = $set['optval'];
        $info['delreason'] = $delreason;
        $info['verifytime'] = time();
        if (db::setByPk('m_account_verify', $info, $set['id'])) {
            $tinfo = array();
            if ($info['status'] == 1) {
                //类型：2改价,4异常转正常,5普通转预约,6预约转普通,7分类更改
                $tinfo = array();
                if (in_array($t['vtype'], array(2, 5, 6))) {
                    $tinfo['ha_postfee'] = $t['ha_postfee'];
                    $tinfo['ha_zffee'] = $t['ha_zffee'];
                    $tinfo['sa_postfee'] = $t['sa_postfee'];
                    $tinfo['sa_zffee'] = $t['sa_zffee'];
                    if ($winfo['mtype'] == 2 || $t['vtype'] == 5) {
                        $tinfo['ha_postfee2'] = $t['ha_postfee2'];
                        $tinfo['ha_zffee2'] = $t['ha_zffee2'];
                        $tinfo['sa_postfee2'] = $t['sa_postfee2'];
                        $tinfo['sa_zffee2'] = $t['sa_zffee2'];
                    }
                }
                if ($t['vtype'] == 7) {
                    $tinfo['catids'] = $t['ch_catids'];
                }
                if ($t['vtype'] == 5) {
                    $tinfo['mtype'] = 2;
                }
                if ($t['vtype'] == 6) {
                    $tinfo['mtype'] = 1;
                }
                if (in_array($t['vtype'], array(1, 3, 4))) {
                    $tinfo['status'] = 1;
                }


                // if($t['ztype'] > 0 && $t['ztype'] < 5)
                // {
                // 	$tinfo[self::$_ztype[$t['ztype']]] = time();
                // }
                $v_type_check = '已通过';
            } else {
                //类型：2改价,4异常转正常,5普通转预约,6预约转普通,7分类更改

                if (in_array($t['vtype'], array(1, 3, 4))) {
                    $tinfo['status'] = 4;
                }
                $v_type_check = '未通过';
                $tinfo['reason_del'] = $delreason;
            }
            $tinfo['verify_status'] = 0;
            $tinfo['updatetime'] = time();
            if (db::setByPk('m_account', $tinfo, $t['accountid'])) {
                //  //广告主价格计算
                $set_sale_price = wb::adfee($t['accountid']);
                if (!empty($set_sale_price)) {
                    db::setByPk('m_account', $set_sale_price, $t['accountid']);
                }
                //获取拒绝原因
                if ($delreason > 0) {
                    $reinfo = db::getByPk('s_reason', $delreason);
                }
                $log = $reinfo['content'] ? "，因" . $reinfo['content'] : '';
                $messinfo['memid'] = $winfo['memid'];
                $messinfo['error_msg'] = "微博账号审核";
                $getmes = self::get_message($t['vtype'], 1);
                $usreinfo = db::getByPk('u_media', $winfo['memid']);
                $messinfo['content'] = "<" . $getmes['title'] . "提醒>亲爱的" . $usreinfo['username'] . "，您的微博号“" . $winfo['nickname'] . "”" . $log . $getmes['data'] . "审核" . $v_type_check;
                $messinfo['type'] = 1;
                $messinfo['memtype'] = 2;
                message::sendMessage($messinfo);
                // if($set['ifmob'])
                // {
                // 	$sms = array();
                // 	$sms['bowid'] = $t['id'];
                // 	$sms['wberid'] = $t['wberid'];
                // 	$sms['memid'] = $winfo['memid'];
                // 	$sms['content'] = "尊敬的用户：您好，您的微博号“".$winfo['nickname']."”审核".$optname[$set['optval']].$log;
                // 	$sms['flag'] = true;
                // 	sms::sendok($sms,1);
                // }
                return true;
            }
        }
        return false;
    }

    // 处理微信审核
    public static function setWxFruit($set, $type) {
        if (!$t = db::getByPk('m_wechat_verify', $set['id'])) {
            return false;
        }
        if ($t['status'] != 0) {
            return false;
        }
        $winfo = db::getByPk('m_wechat', $t['wechatid']);
        //设置拒绝原因
        if ($set['optval'] == 2) {
            $delreason = self::reason($set['reaid'], $set['delreason'], $set['reatype'], $type);
        }
        $info = array();
        $info['status'] = $set['optval'];
        $info['delreason'] = $delreason;
        $info['verifytime'] = time();
        if (db::setByPk('m_wechat_verify', $info, $set['id'])) {
            $tinfo = array();
            if ($info['status'] == 1) {
                //2修改价格,4普通转预约,5预约转普通,6修改阅读量,7修改粉丝,8修改分类
                if ($t['vtype'] == 6) {
                    $tinfo['single_readnum'] = $t['single_readnum'];
                    $tinfo['first_readnum'] = $t['first_readnum'];
                    $tinfo['second_readnum'] = $t['second_readnum'];
                    $tinfo['other_readnum'] = $t['other_readnum'];
                    if ($t['single_readnum'] || $t['first_readnum'] || $t['second_readnum'] || $t['other_readnum']) {
                        $tinfo['readnumstat'] = 2;
                    }
                }

                if (in_array($t['vtype'], array(2, 4, 5))) {
                    $tinfo['single_fee'] = $t['single_fee'];
                    $tinfo['first_fee'] = $t['first_fee'];
                    $tinfo['second_fee'] = $t['second_fee'];
                    $tinfo['other_fee'] = $t['other_fee'];

                    if ($winfo['mtype'] == 2 || $t['vtype'] == 4) {
                        $tinfo['single_fee2'] = $t['single_fee2'];
                        $tinfo['first_fee2'] = $t['first_fee2'];
                        $tinfo['second_fee2'] = $t['second_fee2'];
                        $tinfo['other_fee2'] = $t['other_fee2'];
                    }
                    $tinfo['pricetime'] = time();
                }

                if ($t['vtype'] == 8) {
                    $tinfo['catids'] = $t['ch_catids'];
                }
                if ($t['vtype'] == 4) {
                    $tinfo['mtype'] = 2;
                }
                if ($t['vtype'] == 5) {
                    $tinfo['mtype'] = 1;
                }
                if ($t['vtype'] == 7) {
                    $tinfo['fans'] = $t['fans'];
                    $tinfo['img_fans'] = $t['img_fans'];
                }
                if (in_array($t['vtype'], array(1, 3))) {
                    $tinfo['status'] = 1;
                }
                $v_type_check = '已通过';
            } else {
                if (in_array($t['vtype'], array(1, 3))) {
                    $tinfo['status'] = 4;
                }
                $tinfo['delreason'] = $delreason;
                $v_type_check = '未通过';
            }
            $tinfo['verify_status'] = 0;
            $tinfo['updatetime'] = time();
            if (db::setByPk('m_wechat', $tinfo, $t['wechatid'])) {
                //  //广告主价格计算
                $set_sale_price = wx::adfee($t['wechatid']);
                if ($set_sale_price) {
                    db::setByPk('m_wechat', $set_sale_price, $t['wechatid']);
                }
                //获取拒绝原因
                if ($delreason > 0) {
                    $reinfo = db::getByPk('s_reason', $delreason);
                }
                $log = $reinfo['content'] ? "，因" . $reinfo['content'] : '';
                $messinfo['memid'] = $winfo['memid'];
                $messinfo['error_msg'] = "微信账号审核";
                $getmes = self::get_message($t['vtype'], 2);
                $usreinfo = db::getByPk('u_media', $winfo['memid']);
                $messinfo['content'] = "<" . $getmes['title'] . "提醒>亲爱的" . $winfo['username'] . "，您的微信号“" . $winfo['name'] . "”" . $log . $getmes['data'] . "审核" . $v_type_check;
                $messinfo['type'] = 1;
                $messinfo['memtype'] = 2;
                message::sendMessage($messinfo);
                // if($set['ifmob'])
                // {
                // 	$sms = array();
                // 	$sms['bowid'] = $t['id'];
                // 	$sms['wberid'] = $t['weid'];
                // 	$sms['memid'] = $winfo['memid'];
                // 	$sms['content'] = "尊敬的用户：您好，您的微信号“".$winfo['name']."”审核".$optname[$set['optval']].$log;
                // 	$sms['flag'] = true;
                // 	sms::sendok($sms,1);
                // }
                return true;
            }
        }
        return false;
    }

    // 处理订单审核 注意钱的问题
    public static function setOrderFruit($set) {
        if (!$t = db::getByPk('wb_order', $set['id'])) {
            return false;
        }
        $optval = array(1 => 3, 2 => 21);
        $optname = array(1 => '已通过', 2 => '未通过');
        if (!$optval[$set['optval']]) {
            return false;
        }

        if ($set['optval'] == 1 && db::countWhere('wb_order_detail', 'hdid = ? and status = 2 and ifauto = 1 and ordertype=1 and copyid=0', array($set['id']))) {
            return false;
        }
        $info = array();
        $info['status'] = $optval[$set['optval']];
        $datas = db::getWhere('wb_order_detail', 'hdid = ? and status = 2 and ifauto = 1 and ordertype=1', array($set['id']));

        if ($datas) {
            foreach ($datas as $key => $val) {
                if ($set['optval'] == 2) {
                    $info['re_type'] = 1; //是否可选择拒单类型？
                }
                $check = db::setByPk('wb_order_detail', $info, $val['id']);
                if ($check) {
                    $loginfo = array();
                    $loginfo['zxhd_id'] = $val['hdid'];
                    $loginfo['orderid'] = $val['id'];
                    $loginfo['status'] = $info['status'];
                    $loginfo['logstr'] = $info['status'] == 3 ? "自媒体已接单" : "自媒体已拒单";
                    self::writeLogAction($loginfo);
                }
                //将小订单状态改为拒单，同时退款
                //$v = activity::setStat($val['id'],$info,true);
            }
        }
        return true;
    }

    // 处理速推订单审核 注意钱的问题
    public static function setwbStFruit($set) {
        if (!$t = db::getByPk('w_sthd', $set['id'])) {
            return false;
        }
        $optval = array(1 => 2, 2 => 3);
        $optname = array(1 => '已通过', 2 => '未通过');
        if (!$optval[$set['optval']]) {
            return false;
        }

        $info = array();
        $info['status'] = $set['optval'] == 2 ? 3 : 2;
        if (db::setWhere('w_sthd', $info, 'id = ? and status = 1', array($set['id']))) {
            $log = '速推订单:' . $t['id'] . ' 速推订单处理' . $optname[$set['optval']];
            if ($set['optval'] == 2 && $info['status'] == 3) {
                $log .= '原因：' . $set['reason'];
                $adverLog = '速推订单被拒绝，退款：' . $t['fees'];
                //bill_adver::set("add",array("taskid"=>$t['id'],'uid'=>$t['memid'],'ivalue'=>$t['fees'],'ilog'=>$adverLog,'ordertype'=>9));
            } else {
                // $config = unserialize($t['config']);
                // if($config)
                // {
                // 	$temp = array(1=>50,2=>70,5=>80);
                // 	$wbnums = 0;
                // 	$copyinfo = $copytemp = db::getWhere('w_sthd_copy','zxhd_id = ?',array($t['id']));
                // 	$starttime = $t['time_start'] > time() ? $t['time_start'] : time();
                // 	foreach($config as $tkey=>$tval)
                // 	{
                // 		$wbnums += $val['num'];
                // 	}
                // 	$wbnums = $wbnums <= 0 ? 1800 : $wbnums;
                // 	foreach($config as $key=>$val)
                // 	{
                // 		$okLen = 0;
                // 		if($val['num']>0)
                // 		{
                // 			$vLen = ceil($val['num'] * ($temp[$val['sp']]/100));
                // 			$minfo = db::getWhere('m_account','utype in(2,3) and ifauto = 1 and fee_st = ?',array($val['sp']));
                // 			shuffle($minfo);
                // 			foreach($minfo as $kk=>$rzv)
                // 			{
                // 				if($vLen <= $okLen)
                // 				{
                // 					break;
                // 				}
                // 				$ckey = array_rand($copytemp);
                // 				$set = array();
                // 				$set['memid'] = $rzv['memid'];
                // 				$set['admemid'] = $t['memid'];
                // 				$set['sthd_id'] = $t['id'];
                // 				$set['copyid'] = $copytemp[$ckey]['id'] ? $copytemp[$ckey]['id'] : $copyinfo[array_rand($copyinfo)]['id'];
                // 				$set['sendtype'] = $t['typeid'];
                // 				$set['wberid'] = $rzv['id'];
                // 				$set['feetype'] = $val['sp'];
                // 				$set['utype'] = 2;
                // 				$set['status'] = 2;
                // 				$set['time_start'] = $starttime + rand(1,$wbnums);
                // 				$set['update_time'] = time();
                // 				if($dt = db::getOne('w_sthd_order','sthd_id = ? and wberid = ?',array($t['id'],$rzv['id'])))
                // 				{
                // 					$set['id'] = $dt['id'];
                // 				}
                // 				if(db::set('w_sthd_order',$set))
                // 				{
                // 					unset($copytemp[$ckey]);unset($minfo[$kk]);$okLen += 1;
                // 				}
                // 				continue;
                // 			}
                // 			$drinfo = db::getWhere('m_account','utype = 1 and ifauto = 1 and fee_st = ?',array($val['sp']));
                // 			shuffle($drinfo);
                // 			foreach($drinfo as $kk=>$drv)
                // 			{
                // 				if($val['num'] <= $okLen)
                // 				{
                // 					break;
                // 				}
                // 				$ckey = array_rand($copytemp);
                // 				$set = array();
                // 				$set['memid'] = $drv['memid'];
                // 				$set['admemid'] = $t['memid'];
                // 				$set['sthd_id'] = $t['id'];
                // 				$set['copyid'] = $copytemp[$ckey]['id'] ? $copytemp[$ckey]['id'] : $copyinfo[array_rand($copyinfo)]['id'];
                // 				$set['sendtype'] = $t['typeid'];
                // 				$set['wberid'] = $drv['id'];
                // 				$set['feetype'] = $val['sp'];
                // 				$set['utype'] = 1;
                // 				$set['status'] = 2;
                // 				$set['time_start'] = $starttime + rand(1,$wbnums);
                // 				$set['update_time'] = time();
                // 				if($dt = db::getOne('w_sthd_order','sthd_id = ? and wberid = ?',array($t['id'],$drv['id'])))
                // 				{
                // 					$set['id'] = $dt['id'];
                // 				}
                // 				if(db::set('w_sthd_order',$set))
                // 				{
                // 					unset($copytemp[$ckey]);$okLen += 1;
                // 				}
                // 				continue;
                // 			}
                // 			if($val['num'] > $okLen)
                // 			{
                // 				foreach($minfo as $kk=>$rzv)
                // 				{
                // 					if($val['num'] <= $okLen)
                // 					{
                // 						break;
                // 					}
                // 					$ckey = array_rand($copytemp);
                // 					$set = array();
                // 					$set['memid'] = $rzv['memid'];
                // 					$set['admemid'] = $t['memid'];
                // 					$set['sthd_id'] = $t['id'];
                // 					$set['copyid'] = $copytemp[$ckey]['id'] ? $copytemp[$ckey]['id'] : $copyinfo[array_rand($copyinfo)]['id'];
                // 					$set['sendtype'] = $t['typeid'];
                // 					$set['wberid'] = $rzv['id'];
                // 					$set['feetype'] = $val['sp'];
                // 					$set['utype'] = 2;
                // 					$set['status'] = 2;
                // 					$set['time_start'] = $starttime + rand(1,$wbnums);
                // 					$set['update_time'] = time();
                // 					if($dt = db::getOne('w_sthd_order','sthd_id = ? and wberid = ?',array($t['id'],$rzv['id'])))
                // 					{
                // 						$set['id'] = $dt['id'];
                // 					}
                // 					if(db::set('w_sthd_order',$set))
                // 					{
                // 						unset($copytemp[$ckey]);$okLen += 1;
                // 					}
                // 					continue;
                // 				}
                // 			}
                // 		}
                // 	}
                //}
            }
            $msg = array();
            $msg['memid'] = $t['memid'];
            $msg['utype'] = 1;
            $msg['error_msg'] = '速推订单审核结果';
            $msg['content'] = $log;
            $msg['type'] = 1;
            message::sendMessage($msg);
        }
        return true;
    }

    // 处理抢单审核
    /*
      $set['delreason'] = r_get('delreason');
      $set['reatype'] = r_get('reatype'); // 1 保存 2 修改
      $set['reaid'] = r_get('reaid');
      $type = $set['type'] = r_int('type'); // 1 微博 2 微信 3 订单  4 抢单
     */
    public static function reason($id = 0, $content = null, $reatype = 0, $type = 0) {
        $set = array();
        if ($id > 0) {
            $reinfo = db::getByPk('s_reason', $id);
            $set['id'] = $id;
            $set['usenum'] = $reinfo['usenum'] + 1;
            $setcheck = $id;
        } else {
            $set['type'] = $reatype == 1 ? $type : 0;
            $set['content'] = $content;
            $set['usenum'] = 1;
            $setcheck = db::add('s_reason', $set);
        }
        return $setcheck;
    }

    // 判断订单类型 $f = 1 返回 字段名 $f = 2 返回订单类型名称 $f = 3 返回数组 array('e'=>'ha_zffee','c'=>'硬广转发')
    public static function judgeType($typeid, $adtype, $f = 1) {
        if ($typeid < 0 || $typeid > 2) {
            return -10;
        }
        if ($adtype < 0 || $adtype > 2) {
            return -10;
        }
        $typeName = array(1 => array('e' => 'postfee', 'c' => '直发'), 2 => array('e' => 'zffee', 'c' => '转发'));
        $adtyName = array(1 => array('e' => 'sa_', 'c' => '软广'), 2 => array('e' => 'ha_', 'c' => '硬广'));
        $t = $typeName[$typeid];
        $a = $adtyName[$adtype];
        if (in_array($f, array(1, 2))) {
            return $f == 1 ? $a['e'] . $t['e'] : $a['c'] . $t['c'];
        }
        return array('e' => $a['e'] . $t['e'], 'c' => $a['c'] . $t['c']);
    }

    // 行业转换为字
    public static function catToName($catids) {
        if ($catids) {
            $catArr = explode(',', trim($catids, ','));
            $category = Scache::category_wechat();
            $name = '';
            foreach ($catArr as $key => $catid) {
                $name .= ',' . $category[$catid]['name'];
            }
        }
        return $name ? trim($name, ',') : '';
    }

    // 判断硬广软广内容
    public static function checkContent($typeid = 1, $adtype = 1, $content = '', $wburl = '') {
        // 直发内容中不允许有链接
        if ($adtype == 1) {
            $j = 0;
            $allregex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
            preg_match_all($allregex, $content, $matchall);
            if (is_array($matchall) && !empty($matchall[0])) {
                //判断视频链接
                $match = $matchall[0];
                $len = count($match);
                for ($i = 0; $i < $len; $i++) {
                    $urlinfo = array();
                    //$urlinfo = sinaapi::getshorturl($match[$i],'array');
                    //if(!in_array($urlinfo['urls'][0]['type'],array(1,39)))
                    //{
                    //$j++;
                    //}
                }
            }
            return $j > 0 ? true : false;
        }
        return false;
    }

    ///
    public static function _rebuild_array($arr) {  //rebuild a array
        static $tmp = array();
        for ($i = 0; $i < count($arr); $i++) {
            if (is_array($arr[$i]))
                self::_rebuild_array($arr[$i]);
            else
                $tmp[] = $arr[$i];
        }
        return $tmp;
    }

    //获取站内信内容
    public static function get_message($type, $infotype) {
        $type_data = array();
        if ($infotype == 1) {
            switch ($type) {
                case '1':
                    $type_data['title'] = '上架';
                    $type_data['data'] = '上架';
                    break;
                case '2':
                    $type_data['title'] = '改价';
                    $type_data['data'] = '改价';
                    break;
                case '3':
                    $type_data['title'] = '上架';
                    $type_data['data'] = '上架';
                    break;
                case '4':
                    $type_data['title'] = '恢复';
                    $type_data['data'] = '恢复正常';
                    break;
                case '5':
                    $type_data['title'] = '派单';
                    $type_data['data'] = '普通转预约';
                    break;
                case '6':
                    $type_data['title'] = '派单';
                    $type_data['data'] = '预约转普通';
                    break;
                case '7':
                    $type_data['title'] = '分类';
                    $type_data['data'] = '修改分类';
                    break;
                default:
                    $type_data['title'] = '上架';
                    $type_data['data'] = '上架';
                    break;
            }
        } else {
            switch ($type) {
                case '1':
                    $type_data['title'] = '上架';
                    $type_data['data'] = '上架';
                    break;
                case '2':
                    $type_data['title'] = '改价';
                    $type_data['data'] = '改价';
                    break;
                case '3':
                    $type_data['title'] = '上架';
                    $type_data['data'] = '上架';
                    break;
                case '4':
                    $type_data['title'] = '派单';
                    $type_data['data'] = '普通转预约';
                    break;
                case '5':
                    $type_data['title'] = '派单';
                    $type_data['data'] = '预约转普通';
                    break;
                case '6':
                    $type_data['title'] = '阅读量';
                    $type_data['data'] = '修改阅读量';
                    break;
                case '7':
                    $type_data['title'] = '粉丝';
                    $type_data['data'] = '修改粉丝数';
                    break;
                default:
                    $type_data['title'] = '分类';
                    $type_data['data'] = '修改分类';
                    break;
            }
        }
        return $type_data;
    }

}

?>
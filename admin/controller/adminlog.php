<?php

/**
 * 管理日志
 * */
class adminlog_Controller extends Controller {

    function init() {
        $this->adminlog = Load::model('admin_log');
        $this->menu = Load::model('admin_menu');
        $this->admin = Load::model('admin');
        $this->conf = Load::conf('conf');
        $this->menus_keynames = $this->menu->getMenuKeyNames('menus_keynames');
        $this->menus_keynames['passwd'] = '修改密码';
        $this->menus_keynames['profile'] = '修改资料';
        $this->menus_keynames['index'] = '登录页面';
        $this->adminnames = $this->admin->getAdminNames();
    }

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        if (F::logininfo('usergroup') > 2) {
            $s['adminid'] = F::logininfo('id');
        } else {
            $s['adminid'] = r_int('adminid', 0);
        }
        $startdate = trim(r_get('startdate'));
        $enddate = trim(r_get('enddate'));
        $s['model'] = r_get('model');
        $s['action'] = r_get('saction') ? r_get('saction') : r_get('action');
        $wcheck = array('status' => 1);
        $wreplace = array();
        $s['startdate'] = $startdate ? $startdate : '';
        $s['enddate'] = $enddate ? $enddate : '';
        $wreplace['startdate'] = "createdate >= [V] 00:00:00";
        $wreplace['enddate'] = "createdate <= [V] 23:59:59";
        $url = $this->getPageUrl() . '/list.do';
        $page = r_int('page', 1);
        $pnum = r_int('pnum', 50);
        $rs = F::FormatSearchFields($s, $wcheck, $wreplace);
        $where = $rs['where'];
        $url .= $rs['url'] ? '?' . $rs['url'] : '';

        $rows = $this->adminlog->pageAll($page, $pnum, $url, $where, 'id desc');
        foreach ($rows as $key => $val) {
            $rows[$key]['action'] = $this->conf['admin_action'][$val['action']];
            $rows[$key]['model'] = $this->menus_keynames[$val['model']];
            $rows[$key]['dothing'] = str_replace('#TIME#', $val['createtime'], $val['dothing']);
        }
        v_set('s', $s);
        v_set('page', $page);
        v_set('pnum', $pnum);
        v_set('rows', $rows);
        v_set('adminnames', $this->adminnames);
        v_set('menus_keynames', $this->menus_keynames);
        v_set('admin_action', $this->conf['admin_action']);
        v_display('adminlog_list.tpl');
    }

    function clearAction() {
        $ids = r_get('ids');
        if (empty($ids)) {
            v_notice('没有选择要删除的对象', 1);
        }
        $this->adminlog->removeBy("id IN (" . implode(",", $ids) . ")");
        v_notice('批量删除成功', "list.do");
    }

    function removeAction() {
        $id = r_int('id');
        if (!$rowCount = $this->adminlog->remove($id)) {
            if ($rowCount === false)
                v_callback($this->adminlog->getError(), 0);
            else
                v_callback('您请求的数据不存在', 0);
        }
        v_callback('删除成功', 1);
    }

    function viewAction() {
        $id = r_int('id');
        $info = $this->adminlog->find($id);
        $info['dothing'] = str_replace('#TIME#', $info['createtime'], $info['dothing']);
        $adminlogext = Load::model('admin_log_ext');
        $infoext = $adminlogext->find($id);
        $requestvar = unserialize($infoext['requestvar']);
        ob_start();
        print_r($requestvar);
        $info['requestvar'] = ob_get_contents();
        ob_end_clean();
        v_set('item', $info);
        v_display('adminlog_view.tpl');
    }

}

?>
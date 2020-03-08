<?php

return array(
    'integral_rule' => array('itype' => array('1' => '一次性', '2' => '每次', '3' => '每时', '4' => '每天', '5' => '自定义'), 'ptype' => array('0' => '无限制', '1' => '每时', '2' => '每天', '3' => '每周', '4' => '每月', '5' => '自定义')),
    'admin_action' => array('list' => '列表', 'add' => '添加', 'edit' => '修改', 'order' => '排序', 'clear' => '清理', 'flush' => '更新缓存', 'remove' => '删除', 'check' => '审核', 'create' => '自动生成权限', 'purview' => '权限设置', 'view' => '查看详情', 'login' => '登录', 'logout' => '登出', 'excel' => '导出数据', 'import' => '导入', 'manage' => '管理', 'update' => '批量更新'),
    'admin_caches' => array('cache_file' => array('fields' => array(), 'name' => '文件缓存'), 'cache_mem' => array('fields' => array(), 'name' => 'MEM缓存')),
    'search_dates' => array("jt" => "今天", "zt" => "昨天", "bz" => "本周", "sz" => "上周", "by" => "本月", "sy" => "上月", "7" => "最近7天", "30" => "最近30天", "60" => "最近60天", "all" => "全部", "diy" => "自己选择"),
    'wboauth' => array("wb_akey" => '1711407697', "wb_skey" => '84671eb145be3fa5e47f6ad7cba63f97', 'wb_callback_url' => BASE_URL . "/oauth/wbsuccess.do"),
);
?>
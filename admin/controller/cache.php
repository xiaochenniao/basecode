<?php

/**
 * 缓存管理
 */
class cache_Controller extends Controller {

    function indexAction() {
        $this->_forward('clear');
    }

    //清除文件及数据表结构缓存
    function clearAction() {
        if (request::is_post()) {
            $cachekey = trim(r_get('cachekey'));
            $cachetype = trim(r_get('cachetype'));
            $cleartype = trim(r_get('cleartype'));
            if ($cleartype == 'all') {
                $cachetype == 'file' ? cache::del() : mem::del();
                v_notice('缓存清除成功', "clear");
            } else {
                if (!$cachekey) {
                    v_notice('请输入要清除的Key值', 1);
                }
                $cachetype == 'file' ? cache::del($cachekey) : mem::del($cachekey);
                v_notice('缓存“' . $cachekey . '”清除成功', "clear");
            }
        }
        v_display();
    }

}

?>
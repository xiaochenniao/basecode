<?php

/**
 * 后台首页引导页
 */
class index_Controller extends Controller {

    function indexAction() {
        if (F::islogin(APP_NAME)) {
            //常用菜单
            $mids = F::logininfo('diymenu');

            if ($mids) {
                $diymenus = db::getWhereForFields('sys_menu', 'menu_link as id, menu_name as name,icon_style', "id IN (" . $mids . ") AND isshow=1");

                foreach ($diymenus as $k => $menu) {
                    $diymenus[$k]['url'] = $menu['id'] . '.do';
                }
            } else {
                $diymenus = array();
            }
            $rows = sys_menu::getcache();
            //echo '<pre>';
            //print_r($diymenus);exit;
            $web = db::getByPk('sys_config', 1);
            v_set('web', $web);
            v_set('mainlist', $rows['main']);
            v_set('diymenus', json_encode($diymenus), false);
            v_set('mainblockjs', json_encode($rows['main']), false);
            v_set('submenujs', json_encode($rows['items']), false);
            v_display();
        } else {
            response::redirect('/login');
        }
    }

}

?>
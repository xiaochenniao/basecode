<?php

/*
 * 首页展示
 */

class index_Controller extends base_Controller {

    function init() {
        
    }

    /* //版本更新
      function versionAction()
      {
      //客户端版本
      $version_old = trim(r_get('version'));
      //最新版本
      $version_new = 1.6;
      $data['version_new'] = false;
      if ($version_old < $version_new)
      {
      $data['url'] = 'http://www.baidu.com';
      $data['version_new'] = true;
      v_json($data);
      }
      v_json($data);
      } */
}

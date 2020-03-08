<?php

/**
 * 文件上传操作类
 */
class upload_Controller extends Controller {

    function init() {
        
    }

    function simpleAction() {
        $f = trim(r_get('f'));
        $d = trim(r_get('d'));
        v_set('file', $f);
        v_set('dir', $d);
        v_set('type', trim(r_get('t')));
        v_set('st', r_int('st'));
        v_display('upload_simple.tpl');
    }

    function saveAction() {
        $ftype = r_get('ftype');
        $st = (int) r_get('st');
        $dir = trim(r_get('dir'));
        $uploadconfigs = array('img' => array('allowed_types' => "gif|jpg|jpeg|png|bmp", 'max_size' => 1000000, 'upload_path' => 'imgs/' . $dir),
            'audio' => array('allowed_types' => "midi|mp3|wma|wav|ra|rm", 'max_size' => 5000000, 'upload_path' => 'audio/' . $dir),
            'gimg' => array('allowed_types' => "gif|jpg|jpeg|png|bmp", 'max_size' => 500000, 'upload_path' => 'goods/' . $dir, 'small_imags' => array('s' => array(100, 100), 'm' => array(190, 190, 'max'))));
        $config = $uploadconfigs[$ftype] ? $uploadconfigs[$ftype] : $uploadconfigs['img'];
        $upload_lib = Load::lib('upload', $uploadconfigs[$ftype]);
        if ($upload_lib->run('file', $st)) {
            $filedata = $upload_lib->data();
            if (!empty($filedata)) {
                if ($filedata['is_image']) {
                    $returnfileurl = $filedata['file_small']['s'] ? $filedata['file_small']['s'] : $filedata['file_url'];
                } else {
                    $returnfileurl = $filedata['file_url'];
                }
                echo '<script>parent.uploadSuccess("' . $returnfileurl . '","' . $filedata['file_url'] . '");</script>';
            }
        } else {
            echo '<script>parent.uploadError("' . $upload_lib->display_errors() . '");</script>';
        }
    }

    function editorimgAction() {
        $err = "";
        $msg = "''";
        $uploadconfigs = array('allowed_types' => "gif|jpg|jpeg|png|bmp", 'max_size' => 500000, 'upload_path' => 'imgs', 'small_imags' => array('s' => array(800, 800, 'max')));
        $upload_lib = Load::lib('upload', $uploadconfigs);
        if ($upload_lib->run('filedata', true)) {
            $filedata = $upload_lib->data();
            $msg = "{'url':'" . FILE_URL . '/' . $filedata['file_url'] . "','localname':'" . F::jsonString($filedata['file_name']) . "','id':'1'}";
        } else {
            $err = F::jsonString($upload_lib->display_errors());
        }
        $this->ajaxmsg("{'err':'" . $err . "','msg':" . $msg . "}");
    }

    function editorflaAction() {
        $err = "";
        $msg = "''";
        $uploadconfigs = array('allowed_types' => "swf|flv", 'upload_path' => 'swfs');
        $upload_lib = Load::lib('upload', $uploadconfigs);
        if ($upload_lib->run('filedata', false)) {
            $filedata = $upload_lib->data();
            $msg = "{'url':'" . FILE_URL . '/' . $filedata['file_url'] . "','localname':'" . F::jsonString($filedata['file_name']) . "','id':'1'}";
        } else {
            $err = F::jsonString($upload_lib->display_errors());
        }
        $this->ajaxmsg("{'err':'" . $err . "','msg':" . $msg . "}");
    }

}

?>
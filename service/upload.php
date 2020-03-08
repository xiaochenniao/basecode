<?php

/**
 * 上传组件
 */
class upload {

    //生成多个上传图片框
    function multi($requesturi = '', $width = '100%', $height = '130px') {
        $width = $width ? $width : '100%';
        $height = $height ? $height : '130px';
        $src = view::url() . '/upload/multi.do' . ($requesturi ? '?' . $requesturi : '');
        $zone = '<iframe id="uploadiframe" name="uploadiframe" src="' . $src . '" frameborder="0" scrolling="no" style="width:' . $width . ';height:' . $height . ';border:0px;"></iframe>';
        return $zone;
    }

    //生成单个上传图片框
    function simple($requesturi = '', $width = '100%', $height = '130px') {
        $width = $width ? $width : '100%';
        $height = $height ? $height : '130px';
        $src = view::url() . '/upload/simple.do' . ($requesturi ? '?' . $requesturi : '');
        $zone = '<iframe id="uploadiframe" name="uploadiframe" src="' . $src . '" frameborder="0" scrolling="no" style="width:' . $width . ';height:' . $height . ';border:0px;"></iframe>';
        return $zone;
    }

}

?>
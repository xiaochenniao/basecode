<?php

/**
 * 可视化编辑器
 */
class editor {

    //生成带编辑器的文本框
    public static function create($text = '', $id = 'content', $tool = 'simple', $width = '100%', $height = 200, $upimgext = 'jpg,jpeg,gif,png,bmp', $other = '', $type = null) {
        static $xheditorjs = '';
        $baseurl = APP_NAME == 'admin' ? ADMIN_URL : BASE_URL;
        $width = is_numeric($width) ? $width . 'px' : $width;
        //Separator：分隔符 BtnBr：强制换行 Cut：剪切 Copy：复制 Paste：粘贴 Pastetext：文本粘贴 Blocktag：段落标签 Fontface：字体 FontSize：字体大小 Bold：粗体 Italic：斜体 Underline：下划线 Strikethrough：中划线 FontColor：字体颜色 BackColor：字体背景色 SelectAll：全选 Removeformat：删除文字格式 Align：对齐 List：列表 Outdent：减少缩进 Indent：增加缩进 Link：超链接 Unlink：删除链接 Img：图片 Flash：Flash动画 Media：Windows media player视频 Emot：表情 Table：表格 Source：切换源代码模式 Print：打印 Fullscreen：切换全屏模式 		$appmodel = str_replace(array('/','\\'),'',APP_MODUL_NAME);
        $uploadimgurl = $baseurl . '/upload/editorimg.do';
        $uploadlflashurl = $baseurl . '/upload/editorfla.do';
        $xheditorjs = $xheditorjs ? $xheditorjs : '<script src="/xheditor/xheditor.js" type="text/javascript"></script>';
        $editorhtml = $xheditorjs . '<textarea id="' . $id . '" name="' . $id . '" style="width: ' . $width . ';height: ' . $height . 'px">' . $text . '</textarea>';
        $editorhtml .= '<script type="text/javascript">
		$("#' . $id . '").xheditor({tools:"' . $tool . '",skin:"nostyle",upFlashUrl:"' . $uploadlflashurl . '",upFlashExt:"swf,flv",upImgUrl:"' . $uploadimgurl . '",upImgExt:"' . $upimgext . '"' . ($other ? ',' . $other : '') . '});
		</script>';
        return $editorhtml;
    }

}

?>
<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_editor($text = '',$id='content',$tool = 'simple',$width='100%',$height='200',$upimgext = 'jpg,jpeg,gif,png,bmp',$other='')
{
	return editor::create($text,$id,$tool,$width,$height,$upimgext,$other);
}
?>

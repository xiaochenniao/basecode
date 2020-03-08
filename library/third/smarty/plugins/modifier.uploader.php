<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_uploader($file = '', $uploadertype='simple', $requesturi='', $width=null,$height=null,$type=null)
{
	$fileurl = $file ? FILE_URL.'/'.$file : '';
	$requesturi .= ($requesturi ? '&' : '').'f='.$fileurl;
	return upload::$uploadertype($requesturi,$width,$height,$type);

}
?>

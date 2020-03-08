<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理页面</title>
<link href="<!--{$adminurl}-->/style/common.css" rel="stylesheet" type="text/css" />
<script src="<!--{$adminurl}-->/js/jquery.js" type="text/javascript"></script>
<script>
if(window.frameElement){
	document.onkeydown = function(e){
        var e = e ? e : window.event;
        if(e.keyCode==116) {
            window.top.main_frame.location.reload();
            if(document.all) {
                e.keyCode = 0;
                e.returnValue = false;
            } else {
                e.cancelBubble = true;
                e.preventDefault();
            }
            return false;
        }
    }
}

$.ajaxSetup({cache:false});
</script>
</head>
<body><div class="wrap">
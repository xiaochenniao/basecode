<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=7" />
        <title>后台管理</title>
        <link href="<!--{$adminurl}-->/style/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<!--{$adminurl}-->/style/common.css" rel="stylesheet" type="text/css" />
        <script src="<!--{$adminurl}-->/js/base.js" type="text/javascript"></script>
        <script src="<!--{url}-->/js/jquery.js"></script>
        <script type="text/javascript">
            function CheckAll(form) {
                var ifcheck = null;
                for (var i = 0; i < form.elements.length - 1; i++) {
                    var e = form.elements[i];
                    if (e.type == 'checkbox') {
                        if (ifcheck === null)
                            ifcheck = !e.checked;
                        e.checked = ifcheck;
                        if (typeof e.onclick == 'function')
                            e.onclick();
                    }
                }
                return ifcheck;
            }
            function getObj(domid) {
                return document.getElementById(domid);
            }
            function showaction(type, action) {
                if (getObj(type + 'type')) {
                    var obj = getObj(type + 'type').getElementsByTagName('li');
                    for (var i = 0; i < obj.length; i++) {
                        if (obj[i].id == action) {
                            getObj(obj[i].id).className = 'current1';
                            getObj(type + action).style.display = '';
                        } else {
                            getObj(obj[i].id).className = '';
                            getObj(type + obj[i].id).style.display = 'none';
                        }
                    }
                }
            }
            function _showHide(isopen) {
                var _obj = document.getElementById('admin_search');
                var v = (_obj.style.display == 'none') ? "block" : "none";
                _obj.style.display = v;
            }
            function openNewUrl(id, name, url) {
                parent.shortcut(id, name, url);
                return false;
            }
        </script>
    </head>
    <body>
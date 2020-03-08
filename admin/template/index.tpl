<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><!--{$web.seo_title}-->后台管理系统</title>
<link href="<!--{url}-->/image/favicon.ico" type="image/x-icon" rel="icon">
<script>
if(window.frameElement){
	parent.location.href = window.location.href;
}
</script>
<link href="<!--{url}-->/style/reset.css" rel="stylesheet" />
<link href="<!--{url}-->/style/style.css" rel="stylesheet" />
<link href="<!--{url}-->/style/style_icon.css" rel="stylesheet" />
<script src="<!--{url}-->/js/jquery.js"></script>
<!--[if IE]>
<style type="text/css">
#modemanage a{width:58px;}
</style>
<![endif]-->
</head>
<body onkeyup="keygo(event);">
<table width="100%" cellpadding="0" cellspacing="0" height="100%" class="tableA">
  <tbody class="tbody">
    <tr class="bgA trA">
      <th class="tac"><h1><a href="<!--{$web.weburl}-->"><img src="<!--{url}-->/image/logo.png" style="width:100px;    margin: 4px 0 0 10px;"></a></h1></th>
      <td><div class="navA_wrap">
          <ul id="B_main_block" class="navA">
            <li id="common"><a href="#common">常用</a></li>
            <!--{foreach from=$mainlist key=key item=item}-->
            <li id="<!--{$item.icon_style}-->"><a href="#<!--{$item.icon_style}-->"><!--{$item.name}--></a></li>
            <!--{/foreach}-->
          </ul>
          <div class="fr tar mr20">
            <p><a href="<!--{url}-->" class="btn-blue mr10" target="_blank">前台首页</a>
				<!--{$logininfo.usergroupname}-->:<span class="mr10"><!--{$logininfo.fullname}--></span>
				<a class="logout" href="<!--{url}-->/logout.do">注销</a></p>
          </div>
        </div></td>
    </tr>
    <tr class="trB">
        <th class="bgA"><a href="javascript:;" onclick="shortcut('diymenu','常用菜单定制','<!--{url}-->/diymenu.do');"><span style="margin-right: 5px" class="icon-pushpin"></span>常用菜单定制</a><div class="hr"></div></th>
      <td class="bgA"><div id="B_tabA" class="tabA"> <a href="javascript:;" class="tabA_pre" onClick="prev()" title="上一个">上一个</a> <a href="javascript:;" class="tabA_next" onClick="next()" title="下一个">下一个</a>
          <ul id="B_history">
          </ul>
        </div></td>
    </tr>
  </tbody>
  <tr class="trD vt">
    <th><div id="B_menunav" style="overflow:hidden">
        <div style="display: none;height:28px;">
          <h2 id="menu_title" class="h2">常用功能</h2>
        </div>
        <div class="menubar" style="overflow:hidden">
          <dl id="B_menubar">
          </dl>
        </div>
        <div id="menu_next" class="menuNext"> <a href="javascript:;" class="pre" title="上一页">上一页</a> <a href="javascript:;" class="next" title="下一页">下一页</a> </div>
      </div></th>
    <td id="B_frame"><div class="pr">
        <div class="breadCrumb">
          <div class="fr options"> <a href="#" class="refresh" title="刷新" onClick="frameRefresh()">刷新</a> <a href="javascript://" id="fullScreen" class="admin_full" title="全屏">全屏</a> <a href="#toppage;" id="toppage" class="toppage">页面设置</a> <a onclick="shortcut();" id="shortcutHandle" class="admin_map">菜单管理</a> </div>
          <div id="breadCrumb"><span>当前位置</span><em>&gt;</em><span>后台首页</span></div>
        </div>
      </div>
      <iframe id="default" src="<!--{url}-->/desktop.do" style="height:100%;width:100%;" scrolling="0" frameborder="0"></iframe></td>
  </tr>
</table>
<div id="pagesetting" class="toppage_menu" style="position:absolute;right:140px;top:130px;display:none;">
  <div class="admenu_bg" style="width:125px;">
    <h2>页面设置</h2>
    <ul style="padding:0 5px 5px;">
      <li>
        <label>
        <input type="checkbox" id="showtipsinput" name="t1" value="1">
        显示提示信息</label>
      </li>
      <li>
        <label>
        <input type="checkbox" id="showfuncinput" name="t2" value="1">
        显示搜索功能</label>
      </li>
    </ul>
  </div>
</div>
<script src="js/breeze/Cookie.js"></script>
<script>var BREEZE_BASE_PATH = 'js/breeze/';</script>
<script src="js/breeze/core/base.js"></script>
<script type="text/javascript">
USUALL = <!--{$diymenus}-->; /*常用的功能模块*/
//USUALL = [{name:'常用选项定制',url:'<!--{url}-->/diymenu.do',id:'diymenu'}].concat('-',USUALL);
MAIN_BLOCK = <!--{$mainblockjs}-->;/*主菜单区*/
SUBMENU_CONFIG = <!--{$submenujs}-->;/*子菜单区*/
var times = 0;
function frameRefresh(e){
	var id = B.$('#B_history .current').id.substr(4);
	B.$('#'+id).contentWindow.location.reload();
}
function mouseOverHandle(e){
	var target = e.target;
	while (target != 'LI'){
		target = B.parent(target);
	}
	B.addClass(e.currentTarget.className, 'hover');
}
function mouseOutHandle(e){
	var target = e.target;
	while (target != 'LI'){
		target = B.parent(target);
	}
	B.removeClass(target, 'hover');
}
function fullscreen(e){
	var scr = B.$('#full_screen_style');
	if(scr && !scr.disabled){
		scr.disabled = true;
		B.$('#fullScreen').className = 'admin_full';
		B.$('#fullScreen').title = '全屏';
		return;
	}
	if(!scr){
		scr = B.loadCSS('<!--{url}-->/style/fullscreen.css', 'full_screen_style');
	}
	scr.disabled = false;

	B.$('#fullScreen').className = 'admin_unfull';
	B.$('#fullScreen').title = '返回';
	return false;
}
function init(){
	B.require('dom', 'event', function(B){
		//绑定事件
		B.$$('#B_main_block a').forEach(function(n){
			var name = n.href.substr( n.href.indexOf('#') + 1 );
			if(name=='modemanage'){
				B.query(n).addEvent('mouseover', showModes).addEvent('mouseout', hideModes);
			}else{
				B.addEvent(n, 'click', showMenu);
			}
		});
		B.query('#admenu').addEvent('mouseover', cancelHideModes).addEvent('mouseout', hideModes);
		B.query('#admenu a').addEvent('click', showMenu);
		B.query('#fullScreen').addEvent('click', fullscreen);
		//上一页
		B.query('#menu_next a').addEvent('click', scrollMenu);
		B.query('#toppage').addEvent('click', function(e){
			B.query('#pagesetting').css('display', '').addEvent('mouseover', function(e){e.stopPropagation();});
			document.body.onmouseover = pageHide;
			e.stopPropagation();
		});
		if(document.addEventListener){
			document.addEventListener('DOMMouseScroll',scrollWheel,false);
		}else{
			window.onmousewheel=document.onmousewheel=scrollWheel;//IE/Opera/Chrome/Safari
		}
		showMenu2(B.$('#common a'));
		//加个标签
		B.query('#B_history');
		B.query('#pagesetting li').addEvent('click', function(e){
			var target = e.target, input = e.target;
			while(input.tagName != 'LI'){
				input = input.parentNode;
			}
			input = B.$('input', input);
			var ckey = input.id.substr(0, 8);
			var v = (input.checked) ? 0 : 1;
			if(target.tagName!='INPUT')
			{
				var x = !v;
				input.checked=x;
				v = v?0:1;
			}
			if(v){
				document.cookie = ckey+'='+v;
			}else{
				Cookie.del(ckey);
			}
			initTips();
			e.stopPropagation();
		});

		var span = B.createElement('<li id="tab_default" class="current" onmouseover="B.addClass(this, \'hover\')" onmouseout="B.removeClass(this,\'hover\')"><span><a href="javascript:;" hidefocus="true">后台首页</a><a href="javascript:;" class="del">关闭</a></span></li>'),
		a = B.$('a', span);
		B.data( a, 'name', 'default' );
		B.addEvent(a, 'click', shiftTag);
		B.addEvent(B.$('.del', span), 'click',  delSpan);
		B.query('#B_history .current').removeClass('current');
		B.$('#B_history').appendChild(span);
		B.$('#modemanage') && B.query('#admenu').css('left', B.offset(B.$('#modemanage')).left);
		window.onresize = resizeWin;
		B.$('#showfuncinput').checked = Cookie.get('showfunc');
		B.$('#showtipsinput').checked = !Cookie.get('showtips');
	});
}
function pageHide(){
	B.query('#pagesetting').css('display', 'none');
	document.body.onmouseover='';
}
var popCount;
function showModes(e){
	cancelHideModes(e);
	B.query('#admenu').css('display', '');
	B.query('#modemanage a').addClass('current');
}
function hideModes(e){
	popCount = setTimeout(function(){
		B.query('#admenu').css('display', 'none');
		B.query('#modemanage a').removeClass('current');
	}, 100);
	B.removeEvent(document.body, 'mouseout', hideModes);
}
function blockModes(e){
	showModes(e);
	B.removeEvent(e.target, 'mouseout', hideModes);
	B.addEvent(document.body, 'mousedown', hideModes);
}
function cancelHideModes(e){
	popCount && clearTimeout(popCount);
}
function showMenu(e){
	showMenu2(e.target);
}
function showMenu2(target){
	var href = target.href,
		name = href.substr( href.indexOf('#') + 1 ),
		dl = B.$('#B_menubar'),
		ttl = B.$('#menu_title'),
		data = {},
		title = '';
	dl.style.marginTop= 0;
	B.query('#B_main_block .current').removeClass('current');
	B.query('#admenu .current').removeClass('current');
	target.className = 'current';
	if(name == 'common'){
		title = '常用功能';
		data.items = USUALL;
	}else{
		data = SUBMENU_CONFIG[name];
		title = target.innerHTML;
	}
	ttl.innerHTML = title;
	dl.innerHTML = '';
	data.items.forEach(function(o){
		if(o=='-'){
			dl.appendChild(B.createElement('<div class="hr"></div>'));
			return;
		}
		//建立dt
		var a, dt = B.createElement('dt');
		if (o.url){
			a= B.createElement('<a href="'+o.url+'"><span  class="icon_style '+o.icon_style+'"></span>'+o.name+'</a>');
			B.query(a).addEvent('click', openWinHandle).data('name', name+'-'+o.id);
		} else {
			a = B.createElement('<a href="#'+name+'-'+o.id+'"><span  class="icon_style '+o.icon_style+'"></span>'+o.name+'<span class="second_menu"></span></a>');
			if(o.disabled){
				B.addClass(dt, 'disabled');
				dt.innerHTML = o.name;
				dl.appendChild(dt);
				return;
			}else{
				B.addEvent(a, 'click', toggleSubMenu);
				B.addClass(dt, 'expand');
			}
		}
		dt.appendChild(a);
		dl.appendChild(dt);
		if(o.items){
			var dd = B.createElement('<dd style="display:none"></dd>'), ul = B.createElement('ul');
			dd.appendChild(ul);
			//var sHtml = '<dd style="display:none"><ul>';
			o.items.forEach(function(n){
				var li = B.createElement('li');
				var lk = B.createElement('<a href="'+n.url+'">'+n.name+'</a>');
				B.data(lk, 'name', name + '-' + o.id + '-' + n.id);
				li.appendChild(lk);
				ul.appendChild(li);
			});
			dl.appendChild(dd);
			B.query('#B_menubar dd a').addEvent('click', openWinHandle);
		}
	});
	//resizeWin();
	return false;
}
function resizeWin(){
//	B.query('#menu_next').css('visibility', '');
//	var m = parseInt(B.css(B.$('#menu_next'), 'top')) || B.$('#menu_next').offsetTop;
//		n = parseInt(B.height(B.$('.menubar'))),
//		p = parseInt(B.$('#B_menubar').style.top) || 0,
//		q = Math.min(m-n-85, 0);
//	if (q<0){
//		B.query('#menu_next').css('visibility', '');
//	} else {
//		B.query('#menu_next').css('visibility', 'hidden');
//	}
//	var menu_nav = B.$('#B_menunav');
//	//alert(B.height(window)-300+'px');
//	menu_nav.style.height = B.height(window)-82+'px';
}
function showBreadList(e){
	target = e.target;
	while(target.tagName != 'SPAN'){
		target = target.parentNode;
	}
	var mid = B.attr(target, 'data-id'),
		cls = mid ? mid.split('-') : []
		main= false;
	switch(cls.length){
		case 0:
			ls = MAIN_BLOCK;
			main = true;
			break;
		case 1:
			ls = SUBMENU_CONFIG[cls[0]].items;
		 	break;
		case 2:
			ls = findItem( SUBMENU_CONFIG[cls[0]].items, cls[1] ).items;
			break;
		case 3:
			ls = findItem(findItem( SUBMENU_CONFIG[cls[0]].items, cls[1] ).items, cls[2]).items;
			break;
	}
	var sHtml = '<div class="admenu_bg"><h2 class="treename">'+target.innerHTML+'</h2><ul>';
	if(main){
		ls.forEach(function(o){
			var nid = mid ? mid+'-'+o.id : o.id,
				nam = o.name;
			o = SUBMENU_CONFIG[o.id];
			if(o.items && o.items.length){
				var r = o.items[0];
				nid += '-'+r.id;
				if(r.items && r.items.length){
					nid += '-' + r.items[0].id;
				}
			}
			sHtml += '<li><a href="#" onclick="return openWin(\''+nid+'\');">'+nam+'</a></li>';
		});
	}else{
		ls.forEach(function(o){
			var nid = mid ? mid+'-'+o.id : o.id;
			if(o.items && o.items.length){
				var r = o.items[0];
				nid += '-'+r.id;
				if(r.items && r.items.length){
					nid += '-' + r.items[0].id;
				}
			}
			sHtml += '<li><a href="#" onclick="return openWin(\''+nid+'\');">'+o.name+'</a></li>';
		});
	}
	sHtml += '</ul>';
	B.require('util.dialog', function(B){
		B.util.dialog({
			id: 'breadTip',
			data: sHtml,
			reuse: false,
			pos:['leftAlign', 'topAlign'],
			//autoHide:true
			callback: function(f){
				B.query('#breadTip').addEvent('mouseover', function(e){e.stopPropagation()});
				document.body.onmouseover = function(e){
					f.closep();
					document.body.onmouseover = '';
				};
			}
		}, target)
	});
	e.halt();
}
function toggleSubMenu(e){

    if(e.target.parentNode.tagName=='A'){
        var node = e.target.parentNode.parentNode.nextSibling;
    }else{
        var node = e.target.parentNode.nextSibling;
    }



	if (node && node.tagName == 'DD'){
		if (node.style.display=="none"){
			node.style.display = '';
			e.target.parentNode.className = 'current';
		} else {
			node.style.display = 'none';
			e.target.parentNode.className = 'expand';
		}
	}
	resizeWin();
}
function openWinHandle(e){
	var target = e.target;
	while(target.tagName != 'A'){
		target = B.parent(target);
	}
	openWin(B.data(target, 'name'));
	return false;
}
function findItem(ar, id){
	var newAr = ar.filter(function(n){
		return n.id == id;
	})
	if(newAr.length){
		return newAr[0];
	}
	return null;
}
function shortcut(mid,name,url){
	mid = mid || 'shortcut';
	name = name || '菜单管理';
	url = url || '<!--{url}-->/menu.do';
	var arBread = B.$('#breadCrumb'), frame = B.$('#'+mid);
	arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span>'+name+'</span>';
	if(frame){
		if(frame.style.display == 'none'){
			B.query('#B_frame iframe').css('display', 'none');
			frame.style.display = '';
			B.query('#B_history .current').removeClass('current');
			B.query('#tab_'+mid).addClass('current');
		}
		frame.src = url||frame.src;
		return false;
	}
	var span = B.createElement('<li id="tab_'+mid+'" class="current"  onmouseover="B.addClass(this, \'hover\')" onmouseout="B.removeClass(this,\'hover\')"><span><a href="javascript:;" hidefocus="true">'+name+'</a><a href="javascript:;" class="del">关闭</a></span></li>'),
		a = B.$('a', span);
	B.data( a, 'name', mid );
	B.addEvent(a, 'click', shiftTag);
	B.addEvent(B.$('.del', span), 'click',  delSpan);
	B.query('#B_history .current').removeClass('current');
	B.$('#B_history').appendChild(span);
	B.query('#B_frame iframe').css('display', 'none');
	var iframe = B.createElement('<iframe id="'+mid+'" src="'+url+'" style="height:100%;width:100%;" scrolling="0" frameborder="0"></iframe>');
	B.$('#B_frame').appendChild(iframe);
	//span.scrollIntoView();
	return false;
}
function openWin(mid, url, nickname){
	B.query('#breadTip').remove();
	var arBread = B.$('#breadCrumb');
	if(mid){
		//获取Name
		var cls = mid.split('-'),
			ob = SUBMENU_CONFIG,
			name,
			frame = B.$('#'+mid);
		if(cls[0] == 'common'){
			openWin(findParent(cls[1]));
			return false;
		}
		if(cls[0] == 'search'){
			arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span data-id="">搜索结果</span>';
		}

		if(cls.length > 1){
			arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span class="admenu_down" data-id="">'+findItem(MAIN_BLOCK, cls[0]).name+'</span>';
			ob = findItem(ob[cls[0]].items, cls[1]);
			arBread.innerHTML += '<em>&gt;</em><span class="admenu_down" data-id="'+cls[0]+'">'+ob.name+'</span>';
		}
		if(ob.items){
			ob = findItem(ob.items, cls[2]);
			arBread.innerHTML += '<em>&gt;</em><span class="admenu_down" data-id="'+cls[0]+'-'+cls[1]+'">'+ob.name+'</span>';
		}
		name = ob.name;
		B.query('#breadCrumb span').addEvent('click', showBreadList);
		if(frame){
			if(frame.style.display == 'none'){
				B.query('#B_frame iframe').css('display', 'none');
				frame.style.display = '';
				B.query('#B_history .current').removeClass('current');
				B.query('#tab_'+mid).addClass('current');
			}
			frame.src = url||ob.url||frame.src;
			//setTimeout(initTips,500);
			return false;
		}
	}else{
		mid = 'wrong';
		name = nickname;
		arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span data-id="">'+nickname+'</span>';
	}
	var span = B.createElement('<li id="tab_'+mid+'" class="current"  onmouseover="B.addClass(this, \'hover\')" onmouseout="B.removeClass(this,\'hover\')"><span><a href="javascript:;" hidefocus="true">'+name+'</a><a href="javascript:;" class="del">关闭</a></span></li>'),
		a = B.$('a', span);
	B.data( a, 'name', mid );
	B.addEvent(a, 'click', shiftTag);
	B.addEvent(B.$('.del', span), 'click',  delSpan);
	B.query('#B_history .current').removeClass('current');
	B.$('#B_history').appendChild(span);
	B.$('#B_history').scrollTop = span.offsetTop;
	B.query('#B_frame iframe').css('display', 'none');
	var iframe = B.createElement('<iframe id="'+mid+'" src="'+(url||ob.url)+'" style="height:100%;width:100%;" scrolling="0" frameborder="0"></iframe>');
	B.$('#B_frame').appendChild(iframe);
	//setTimeout(initTips, 500);
	return false;
}
function shiftTag(e){
	var mid = e.target.parentNode.parentNode.id.substr(4),
		frame = B.$('#'+mid),
		cls = mid.split('-'),
		ob = SUBMENU_CONFIG,
		arBread = B.$('#breadCrumb');
	if(frame.style.display == 'none'){
		B.query('#B_frame iframe').css('display', 'none');
		frame.style.display = '';
		B.query('#B_history .current').removeClass('current');
		B.query('#tab_'+mid).addClass('current');
	}
	if(mid == 'default'){
		B.$('#breadCrumb').innerHTML = '<span>当前位置</span><em>&gt;</em><span>后台首页</span>';
	}else{
		if(cls.length > 1){
			arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span class="admenu_down" data-id="">'+findItem(MAIN_BLOCK, cls[0]).name+'</span>';
			ob = findItem(ob[cls[0]].items, cls[1]);
			arBread.innerHTML += '<em>&gt;</em><span class="admenu_down" data-id="'+cls[0]+'">'+ob.name+'</span>';
		}
		if(ob.items){
			ob = findItem(ob.items, cls[2]);
			arBread.innerHTML += '<em>&gt;</em><span class="admenu_down" data-id="'+cls[0]+'-'+cls[1]+'">'+ob.name+'</span>';
		}
		B.query('#breadCrumb span').addEvent('click', showBreadList);
	}
}
function delSpan(e){
	delSpan2(e.target.parentNode.parentNode);
}
function delSpan2(li){
	//切换焦点
	var newli = B.prev(li) || B.next(li);
	if(B.hasClass(li, 'current')){
		if(!newli){
			if(li.id == 'tab_default'){
				return false;
			}
			var span = B.createElement('<li id="tab_default" class="current" onmouseover="B.addClass(this, \'hover\')" onmouseout="B.removeClass(this,\'hover\')"><span><a href="javascript:;" hidefocus="true">后台首页</a><a href="javascript:;" class="del">关闭</a></span></li>'),
			a = B.$('a', span);
			B.data( a, 'name', 'default' );
			B.addEvent(a, 'click', shiftTag);
			B.addEvent(B.$('.del', span), 'click',  delSpan);
			B.query('#B_history .current').removeClass('current');
			B.$('#B_history').appendChild(span);
			B.$('#breadCrumb').innerHTML = '<span>当前位置</span><em>&gt;</em><span>后台首页</span>';
			var iframe = B.createElement('<iframe id="default" src="<!--{url}-->/desktop.do" style="height:100%;width:100%;" scrolling="0" frameborder="0"></iframe>');
			B.$('#B_frame').appendChild(iframe);

		}else{
			var mid = newli.id.substr(4);
			if(mid == 'default'){
				B.$('#breadCrumb').innerHTML = '<span>当前位置</span><em>&gt;</em><span>后台首页</span>';
			}
			openWin(mid);
		}
	}
	B.remove(li);
	B.query('#'+B.data(B.$('a', li), 'name')).remove();
}
function next(e){
	var a, node =B.next( B.$('#B_history .current') );
	if (!node){
		return;
	}
	a = B.$('a', node);
	B.query('#B_history .current').removeClass('current');
	B.addClass(node, 'current');
	B.query('#B_frame iframe').css('display','none');
	B.$('#B_history').scrollTop = node.offsetTop;
	B.$('#'+B.data(a, 'name')).style.display = '';
}
function prev(e){
	var a, node =B.prev( B.$('#B_history .current') );
	if (!node){return;}
	a = B.$('a', node);
	B.query('#B_history .current').removeClass('current');
	B.addClass(node, 'current');
	B.$('#B_history').scrollTop = node.offsetTop;
	B.query('#B_frame iframe').css('display','none');
	B.$('#'+B.data(a, 'name')).style.display = '';
}
var adminSearchClass = {
	obj : null,
	defaultValue : "后台搜索",
	init : function(){
		 this.obj = document.getElementById('keyword');
	},
	focus : function(){
		this.init();
		if(this.obj.value == this.defaultValue){
			this.obj.value = "";
		}
		this.obj.className = "s-input";
	},
	blur : function(){
		this.init();
		if(this.obj.value == ""){

			this.obj.className = "s-input gray";
			this.obj.value = this.defaultValue;
		}
	},
	keyup : function(evt){
		var keycode = window.event ? window.event.keyCode : evt.which;
		if(keycode == 13 ){
			this.search();
		}
	},
	search : function(){
		alert("暂未开通");
		return false;
		if (times == 2){
			times = 0;
		}
		var keyword = B.$('#keyword').value;
		if (keyword.length > 1) {
			var searchFrame = B.$('#search');
			if(searchFrame){
				searchFrame.src= '$admin_file?adminjob=search&keyword='+encodeURI(keyword);
				if(searchFrame.style.display == 'none'){
					openWin('search');
				}
				return;
			}
			var span = B.createElement('<li id="tab_search" class="current" onmouseover="B.addClass(this, \'hover\')" onmouseout="B.removeClass(this,\'hover\')"><span><a href="javascript:;" hidefocus="true">搜索结果</a><a href="javascript:;" class="del">关闭</a></span></li>'),
				a = B.$('a', span),
				mid = 'search';
			B.data( a, 'name', mid );
			B.addEvent(a, 'click', shiftTag);
			B.addEvent(B.$('.del', span), 'click',  delSpan);
			B.query('#B_history .current').removeClass('current');
			B.$('#B_history').appendChild(span);
			B.query('#B_frame iframe').css('display', 'none');
			var iframe = B.createElement('<iframe id="'+mid+'" src="<!--{url}-->/search.do?keyword='+encodeURI(keyword)+'" style="height:100%;width:100%;" scrolling="0" frameborder="0"></iframe>');
			B.$('#B_frame').appendChild(iframe);
			var arBread = B.$('#breadCrumb');
			arBread.innerHTML = '<span>当前位置</span><em>&gt;</em><span data-id="">搜索结果</span>';

			return false;
		} else {
			if (times <1){
				alert('至少输入两个字节');
			}
			times++;
		}
	}
}
function findParent(id){
	var l = MAIN_BLOCK.length;
	for(var i=0; i<l; i++){
		var tmp_id_1 = MAIN_BLOCK[i].id;
		if(tmp_id_1 == id){
			return id;
		}
		var params = SUBMENU_CONFIG[MAIN_BLOCK[i].id].items,
			m = params.length;
		for(var j=0; j<m; j++){
			var tmp_id_2 = params[j].id;
			if(tmp_id_2 == id){
				return tmp_id_1+'-'+id;
			}
			//三级
			var params2 = params[j].items;
			if(params2){
				var n = params2.length;
				for(var k=0; k<n; k++){
					var tmp_id_3 = params2[k].id;
					if(tmp_id_3 == id){
						return tmp_id_1+'-'+tmp_id_2+'-'+tmp_id_3;
					}
				}
			}

		}
	}
	return '';
}
function scrollMenu(e){
	var m = parseInt(B.css(B.$('#menu_next'),'top')) || B.$('#menu_next').offsetTop;
	p = parseInt(B.$('#B_menubar').style.marginTop) || 0,
	n = parseInt(B.height(B.$('.menubar'))) - (B.UA.ie?0:p),
	q = Math.min(m-n-85, 0);
	if(e.target.className.indexOf('pre') > -1 ){
		B.query('#B_menubar').css( 'marginTop', Math.min(0, p+25) );
	}else if(e.target.className.indexOf('next')>-1){
		B.query('#B_menubar').css( 'marginTop', Math.max(q, p-25) );
	}
}
function scrollWheel(e){
	var direct = 0;
	e=e || window.event;
	if(e.wheelDelta){//IE/Opera/Chrome
		direct = -e.wheelDelta;
	}else if(e.detail){//Firefox
		direct = e.detail;
	}
	var m = parseInt(B.css(B.$('#menu_next'),'top')) || B.$('#menu_next').offsetTop;
		p = parseInt(B.$('#B_menubar').style.marginTop) || 0,
		n = parseInt(B.height(B.$('.menubar')) ) - (B.UA.ie?0:p),
		q = Math.min(m-n-85, 0);
	B.query('#B_menubar').css( 'marginTop', Math.min(0, Math.max(q, p-25*direct) ) );
}
function getElementsByClassName (className, parentElement){
	if (typeof(parentElement)=='object') {
		var elems = parentElement.getElementsByTagName("*");
	} else {
		var elems = (document.getElementById(parentElement)||document.body).getElementsByTagName("*");
	}
	var result=[];
	for (i=0; j=elems[i]; i++) {
	   if ((" "+j.className+" ").indexOf(" "+className+" ")!=-1) {
			result.push(j);
	   }
	}
	return result;
}
/*初始化信息*/
function initTips(searchshow){
	var tips = Cookie.get('showtips') ? 0 : 1;
	_showTips(tips);
	if(!searchshow){
		var desc = Cookie.get('showfunc') ? 1 : 0;
	}else{
		var desc = 1;
	}
	_showSearch(desc);
}
function _showTips(isopen){
	var iframes = B.$$('iframe');
	var infos = [];
	iframes.forEach(function(n){
		infos = infos.concat(getElementsByClassName('admin_info', n.contentWindow.document));
	});
	var v = (isopen) ? "block" : "none";
	if(infos){
		for(var i=0;i<infos.length;i++){
			infos[i].style.display = v;
		}
	}
}
function _showSearch(isopen){
	var iframes = B.$$('iframe');
	var infos = [];
	iframes.forEach(function(n){
		infos = infos.concat(getElementsByClassName('admin_search', n.contentWindow.document));
	});
	var v = (isopen) ? "block" : "none";
	if(infos){
		for(var i=0;i<infos.length;i++){
			infos[i].style.display = v;
		}
	}
}

function closeAdminTab(win){
	if(win.frameElement){
		var mid = win.frameElement.id;
		delSpan2(B.$('#tab_'+mid));
	}
}
function keygo(evt){
	var key = window.event?evt.keyCode:evt.which;
	if (key==37){//←
		prev();
	}else if (key==39){//→
		next();
	}
}
document.onkeydown = function(e){
	var e = e ? e : window.event;
	if (e.keyCode==116){//F5
		frameRefresh(e);
		return false;
	}
	return true;
}

var adminNavClass = {};
adminNavClass.initTips = initTips;

window.onload = function(){
	init();
}
adminSearchClass.init();
</script>

<script>
	$(function () {
		var menubar = $('#B_menubar');
		var history = $('#B_history');

		menubar.on('click', 'dt', function () {
			$(this).addClass('active').siblings().removeClass('active');
            $('li').removeClass('active');
		});
		menubar.on('click', 'li', function () {
			menubar.find('li').removeClass('active');
			$(this).addClass('active').siblings().removeClass('active');
            $('dt').removeClass('active');
		});

		history.on('click','li', function () {
			var str1 = $(this).find('a').eq(0).text();
			var items = menubar.children();
			var obj;

			items.each(function (i, el){
				var str2 = $(el).find('a').text();
				if( str2 === str1 ) {
					obj = el;
					return false;
				}
			});
			$(obj).addClass('active').siblings().removeClass('active');
		});
	})

</script>
</body>
</html>
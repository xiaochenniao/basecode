<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/wbox.js" type="text/javascript"></script>
<link href="<!--{$adminurl}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<div class="wrap">
  <div id="notice"></div>
  <style type="text/css">
.admin_table .td1,.admin_table .td2{padding:4px 5px 4px 15px;line-height:22px;}
</style>
  <div class="admin_info mb10">
    <h4 class="h1">后台说明</h4>
    <div class="legend">
      <ol>
        <li>操作菜单:您可以通过页面顶部的菜单进行您需要的操作。</li>
        <li>功能设置:您可以从右上角的刷新图标进行刷新当前页面，通过全屏图标将操作区域最大化，通过页面设置功能选择是否显示每个操作页面的一些功能提示。 </li>
      </ol>
    </div>
  </div>
  <h2 class="h1">
	<span class="fl mr10">您的资料</span>
	<a hidefocus="true" onclick="openNewUrl('modpasswd','修改密码','<!--{$adminurl}-->/passwd.do');" class="btn_add fl" style="margin-left:10px;"><i>修改密码</i></a>
	<a hidefocus="true" onclick="openNewUrl('apply_mibao','申请密保卡','<!--{$adminurl}-->/mibao/apply.do');" class="btn_add fl" style="margin-left:10px;"><i>设置密保卡</i></a>
	</h2>
  <div class="admin_table mb10">
    <table width="100%">
	  <tr class="tr1 vt">
		<td class="td1">用户名</td>
		<td class="td2"><!--{$logininfo.username}--></td>
	  </tr>
	  <tr class="tr1 vt">
		<td class="td1">用户组</td>
		<td class="td2"><!--{$logininfo.usergroupname}--></td>
	  </tr>
	  <tr class="tr1 vt">
		<td class="td1">描述</td>
		<td class="td2"><!--{$logininfo.description}--></td>
	  </tr>
	  <tr class="tr1 vt">
		<td class="td1">登录次数</td>
		<td class="td2"><!--{$logininfo.logintimes}--></td>
	  </tr>
	  <tr class="tr1 vt">
		<td class="td1">上次登录时间</td>
		<td class="td2"><!--{$logininfo.lastlogintime}--></td>
	  </tr>
	  <tr class="tr1 vt">
		<td class="td1">上次登录IP</td>
		<td class="td2"><!--{$logininfo.lastloginip}--></td>
	  </tr>
	</table>
  </div>
</div>
<!--{include file="footer.tpl"}-->
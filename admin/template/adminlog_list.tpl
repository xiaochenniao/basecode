<!--{include file="header.tpl"}-->
<link href="<!--{$adminurl}-->/style/datepicker.css" type="text/css" rel="stylesheet" />
<link href="<!--{$adminurl}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<script src="<!--{$adminurl}-->/js/datepicker.js" type="text/javascript"></script>
<script src="<!--{$adminurl}-->/js/wbox.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10 ta">
    <ul class="cc">
      <li class="current"><a href="#">后台管理日志</a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">后台管理日志记录了任何的表单提交记录，不记录查询操作。
    </div>
  </div>
  <form id="searchForm" name="searchForm" action="<!--{$pageurl}-->/list.do" method="post">
    <h2 class="h1" onclick="_showHide();">日志搜索</h2>
    <div id="admin_search" class="admin_search admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr1 vt">
          <td class="td1">操作页面</td>
          <td class="td2">
            <select name="model">
              <option value="" >请选择</option>
              <!--{html_options options=$menus_keynames selected=$s.model}-->
            </select></td>
          <td class="td2"></td>
        </tr>
        <tr class="tr1 vt">
          <td class="td1">操作行为</td>
          <td class="td2">
            <select name="model">
              <option value="" >请选择</option>
              <!--{html_options options=$admin_action selected=$s.action}-->
            </select>
          </td>
          <td class="td2"></td>
        </tr>
        <tr class="tr1 vt">
          <td class="td1">管理员</td>
          <td class="td2">
            <select name="model">
              <option value="" >请选择</option>
              <!--{foreach from=$adminnames key=key item=item}-->
              <option value="<!--{$key}-->" <!--{if $key==$s.adminid}-->selected<!--{/if}-->><!--{$item.fullname}--></option>
              <!--{/foreach}-->
            </select>
          </td>
          <td class="td2"></td>
        </tr>
        <tr class="tr1 vt">
          <td class="td1">操作时间</td>
          <td class="td2"><div class="input_img fl">
              <input type="text" id="from" value="<!--{$s.startdate}-->" name="startdate">
              </div>
              <span class="p_lr_10 fl">至</span>
              <div class="input_img fl">
                <input type="text" id="to" value="<!--{$s.enddate}-->" name="enddate">
              </div></td>
            <td class="td2"></td>
        </tr> 
        <tr class="tr1 vt">
          <td class="td1">每页显示条数</td>
          <td class="td2"><input type="text" name="pnum" value="<!--{$pnum|default:20}-->" class="input input_wa"></td>
          <td class="td2"></td>
        </tr>
      </table>
      <div class="mt10 tac"> <span class="mt10 mr10"><span class="btn"><span>
      <button type="submit">提 交</button>
      </span></span></span></div>
    </div>
	<div class="mb10"></div>
  </form>
  <form action="<!--{$pageurl}-->/remove.do" method="post" name="FORM">
    <h2 class="h1"><span class="fl mr10">日志列表</span><!--{include file=pagesimple.tpl}--></h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr2 vt td_bgB">
          <td width="30"><span onclick="CheckAll(document.FORM)" class="cp">全选</span></td>
          <td>管理员</td>
          <td>操作页面</td>
          <td>行为</td>
          <td>做了什么...</td>
          <td>IP</td>
          <td>操作</td>
        </tr>
        <!--{foreach from=$rows item=item}-->
        <tr class="tr1">
          <td class="td2"><input name="ids[]" type="checkbox" value="<!--{$item.id}-->" /></td>
          <td class="td2"><!--{$item.adminname}--></td>
          <td class="td2"><!--{$item.model}--></td>
          <td class="td2"><!--{$item.action}--></td>
          <td class="td2"><!--{$item.dothing}--></td>
          <td class="td2"><!--{$item.onlineip}--></td>
          <td class="td2">
            <a href="javascript:;" class="fa info-circle" title="详细" id="view_log_<!--{$item.id}-->"></a>
            <a class="fa fa-times" title="删除" href="<!--{$adminurl}-->/adminlog/remove.do?id=<!--{$item.id}-->" onClick="return confirm('确定删除?');"></a>
            <script>$('#view_log_<!--{$item.id}-->').wBox({title:'查看详情',requestType: 'iframe',target:'<!--{$adminurl}-->/adminlog/view.do?id=<!--{$item.id}-->'});</script>
          </td>
        </tr>
        <!--{foreachelse}-->
        <tr>
          <td class="p10" colspan="7"><div class="admin_tips">啊哦，没有你要的信息！</div></td>
        </tr>
        <!--{/foreach}-->
      </table>
    </div>
    <div class="cc"></div>
    <!--{include file=page.tpl}-->
    <div class="mb10 tal"> <span class="bt"><span>
      <button type="button" onclick="CheckAll(document.FORM)">全 选</button>
      </span></span> <span class="btn"><span>
      <button type="submit" name="button" onclick="return confirm('确定要删除所选相册吗？');">删除</button>
      </span></span> </div>
  </form>
  <div class="c"></div>
</div>
<script>
$(function() {
	var dates = $( "#from, #to" ).datepicker({
		defaultDate: "+1w",
		//changeMonth: true,
		numberOfMonths: 2,
		onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
});
</script>
<!--{include file="footer.tpl"}-->
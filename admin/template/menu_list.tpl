<!--{include file="header.tpl"}-->
<link href="<!--{url}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<script src="<!--{url}-->/js/jquery.js"></script>
<script src="<!--{url}-->/js/wbox.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">菜单管理</a></li>
      <li><a href="<!--{$pageurl}-->/add.do">添加菜单</a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">此为后台管理菜单管理，当前查看的为菜单列表，可以进行批量修改，排序等功能。
    </div>
  </div>
  <form action="<!--{$pageurl}-->/update.do" method="post" name="FORM" onsubmit="return comm()">
    <h2 class="h1"><span class="fl mr10">后台管理菜单设置</span><a hidefocus="true" href="<!--{$pageurl}-->/add.do" class="btn_add fl"><i>添加菜单</i></a><a hidefocus="true" href="javascript:;"  onclick="showBox(this.id,{title:'菜单图标',requestType: 'iframe',iframeWH:{width:800,height:300},target:'<!--{$pageurl}-->/getIconList.do?id=<!--{$item.id}-->'})" class="btn_add fl"><i>查看子菜单图标</i></a><a hidefocus="true" href="<!--{$pageurl}-->/flush.do" class="btn_flush fr"><i>更新缓存</i></a></h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0" id="menu_table">
        <tr class="tr2 td_bgB">
          <td width="60"><span onClick="CheckAll(document.FORM)" class="cp">启用</span></td>
          <td width="280">[顺序] 菜单名称</td>
          <td>链接地址</td>
          <td>菜单图标</td>
          <td width="120">操作</td>
        </tr>
        <!--{foreach from=$menus item=item}-->
        <tr class="tr1 vt">
          <td class="td2"><input name="info[<!--{$item.id}-->][isshow]" type="checkbox" value="1"<!--{if $item.isshow==1}--> checked<!--{/if}--> /></td>
          <td class="td2"><!--{if $item.prename}--><!--{$item.prename}--><!--{else}--><i class="expand expand_a"></i><!--{/if}-->
            <input name="info[<!--{$item.id}-->][order_id]" type="text" class="input input_wd" value="<!--{$item.order_id}-->" />
            <input name="info[<!--{$item.id}-->][menu_name]" type="text" class="input input_wa" id="me_<!--{$item.id}-->" value="<!--{$item.menu_name}-->" />
          </td>
          <td class="td2"><input name="info[<!--{$item.id}-->][menu_link]" type="text" id="url_<!--{$item.id}-->" class="input input_wa" value="<!--{$item.menu_link}-->" /></td>
          <td class="td2"><input name="info[<!--{$item.id}-->][icon_style]" type="text" id="icon_<!--{$item.id}-->" class="input input_wa" value="<!--{$item.icon_style}-->" /></td>

          <td class="td2">
            <a href="<!--{$pageurl}-->/edit.do?id=<!--{$item.id}-->" class="fa fa-pencil" title="编辑"></a>
            <a href="<!--{$pageurl}-->/remove.do?id=<!--{$item.id}-->" onClick="return confirm('确定删除?');" class="fa fa-times" title="删除"></a>
          </td>
        </tr>
        <!--{/foreach}-->
      </table>
    </div>
    <div class="tac mb10"> 
      </span></span> <span class="btn"><span>
      <button type="submit" name="Submit2">提 交</button>
      </span></span> </div>
  </form>
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<script>


  $(function () {
      $('.cp').append('<span style="margin-left: 8px;" onClick="CheckAll(document.FORM)"><input type="checkbox"></span>');
  })


function comm()
{
  var flag = false;
  $("#menu_table").find(".tr1").each(function(){
    var tdArr = $(this).children();
    var menu_name = tdArr.eq(1).find('input').eq(1).val();//获取菜单名称
    var menu_url = tdArr.eq(2).find('input').val();//获取链接地址
   // alert(menu_name);
    if(!menu_name || !menu_url){
      alert('菜单名称和链接地址必须全部填写');
      flag = true;
      return false;
    }
  });
  if(flag) return false;

}
</script>


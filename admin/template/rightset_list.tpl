<!--{include file="header.tpl"}-->
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">权限管理</a></li>
      <li><a href="<!--{$pageurl}-->/add.do">添加权限</a></li>
      <li><a href="<!--{$pageurl}-->/create.do"><span>自动生成权限</span></a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">此为后台管理权限的单个文件的权限点设置，可以进行手动增减，亦可自动生成。
    </div>
  </div>
  <form action="<!--{$pageurl}-->/update.do" method="post" name="FORM" onsubmit="return comm()">
    <h2 class="h1"><span class="fl mr10">后台管理权限设置</span><a hidefocus="true" href="<!--{$pageurl}-->/add.do" class="btn_add fl"><i>添加权限</i></a></h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0" id="power_table">
        <tr class="tr2 td_bgB">
          <td width="300">[顺序] 权限名称</td>
          <td>权限标识</td>
          <td width="120">操作</td>
        </tr>
        <!--{foreach from=$rows item=item}-->
        <tr class="tr1 vt">
          <td class="td2"><!--{if $item.prename}--><!--{$item.prename}--><!--{else}--><i class="expand expand_a"></i><!--{/if}-->
            <input name="info[<!--{$item.id}-->][order_id]" type="text" class="input input_we" value="<!--{$item.order_id}-->" />
            <input name="info[<!--{$item.id}-->][purview_name]" type="text" class="input input_wa" value="<!--{$item.purview_name}-->" />
          </td>
          <td class="td2"><input name="info[<!--{$item.id}-->][purview_link]" type="text" class="input input_wa" value="<!--{$item.purview_link}-->" /></td>
          <td class="td2">
            <a href="<!--{$pageurl}-->/edit.do?id=<!--{$item.id}-->" class="fa fa-pencil" title="编辑"></a>
            <a href="<!--{$pageurl}-->/remove.do?id=<!--{$item.id}-->" onClick="return confirm('确定删除?');" class="fa fa-times" title="删除"></a>
          </td>
        </tr>
        <!--{/foreach}-->
      </table>
    </div>
    <div class="tac mb10"> <span class="bt"><span>
      <button type="button" onClick="CheckAll(document.FORM);">全 选</button>
      </span></span> <span class="btn"><span>
      <button type="submit" name="Submit2">提 交</button>
      </span></span> </div>
  </form>
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<script>
  function comm()
  {
    var flag = false;
    $("#power_table").find(".tr1").each(function(){
      var tdArr = $(this).children();
      var power_name = tdArr.eq(0).find('input').eq(1).val();//获取权限名称
      var power_url = tdArr.eq(1).find('input').val();//获取权限标识
      if(!power_name || !power_url){
        alert('权限名称和权限标识必须全部填写');
        flag = true;
        return false;
      }
    });
    if(flag) return false;
  }
</script>
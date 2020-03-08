<!--{include file="header.tpl"}-->
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li<!--{if $pid<=0}--> class="current"<!--{/if}-->><a href="<!--{$pageurl}-->/list.do">省份列表</a></li>
      <!--{foreach from=$trees item=item}-->
      <li<!--{if $pid==$item.id}--> class="current"<!--{/if}-->><a href="<!--{$pageurl}-->/list.do?pid=<!--{$item.id}-->"><!--{$item.name}--></a></li>
      <!--{/foreach}-->
      <li><a href="<!--{$pageurl}-->/add.do?pid=<!--{$pid}-->">添加地区</a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">此为后台管理地区管理，当前查看的为地区列表，可以进行批量修改，排序等功能。
    </div>
  </div>
  <form action="<!--{$pageurl}-->/update.do" method="post" name="FORM">
    <h2 class="h1"><span class="fl mr10">地区列表</span><a hidefocus="true" href="<!--{$pageurl}-->/add.do?pid=<!--{$pid}-->" class="btn_add fl"><i>添加地区</i></a><a hidefocus="true" href="<!--{$pageurl}-->/flush.do" class="btn_flush fr"><i>更新缓存</i></a></h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr2 td_bgB">
          <td width="30"><span onClick="CheckAll(document.FORM)" class="cp">启用</span></td>
          <td width="240">[顺序] 地区名称</td>
          <td>全称</td>
          <td width="140">操作</td>
        </tr>
        <!--{foreach from=$areas item=item}-->
        <tr class="tr1 vt">
          <td class="td2"><input name="info[<!--{$item.id}-->][isshow]" type="checkbox" value="1"<!--{if $item.isshow==1}--> checked<!--{/if}--> /><input name="info[<!--{$item.id}-->][upid]" type="hidden" value="<!--{$item.upid}-->" /></td>
          <td class="td2">
            <input name="info[<!--{$item.id}-->][displayorder]" type="text" class="input input_wd" value="<!--{$item.displayorder}-->" />
            <input name="info[<!--{$item.id}-->][name]" type="text" class="input input_wa" value="<!--{$item.name}-->" />
          </td>
          <td class="td2"><!--{$item.joinname}--></td>
          <td class="td2"><!--{if $item.level<3}-->
            <a href="<!--{$pageurl}-->/list.do?pid=<!--{$item.id}-->" class="fa fa-map-marker" title="管理子地区"></a>
            <!--{else}--><a  onclick="openNewUrl('datacache-cityschool-school','学校数据','<!--{$adminurl}-->/school.do?aid=<!--{$item.id}-->');" class="fa fa-life-bouy" title="管理学校"></a><!--{/if}-->
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
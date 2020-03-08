<!--{include file="header.tpl"}-->
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
	  <li class="current"><a href="<!--{url action=list}-->"><span>定时脚本列表</span></a></li>
	  <li><a href="<!--{url action=add}-->"><span>添加定时脚本</span></a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">
      <ul>
        <li>定时脚本列表</li>
      </ul>
    </div>
  </div>
  <div style="clear:both;"></div>
  <h2 class="h1"><span class="fl mr10">定时脚本列表</span></h2>
  <div class="admin_table mb10">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr class="tr2 td_bgB">
        <td>脚本</td>
        <td>描述</td>
        <td>运行时间</td>
        <td>间隔</td>
        <td>下次运行时间</td>
        <td style="width:160px;">操作</td>
      </tr>
      <!--{foreach from=$datas item=item}-->
      <tr class="tr1 vt">
        <td class="td2"><!--{$item.script}--></td>
        <td class="td2" title="<!--{$item.descrition}-->"><!--{if $item.descrition}--><!--{$item.descrition|truncate:15}--><!--{else}-->--<!--{/if}--></td>
		<td class="td2"><!--{$item.start_time}--> --- <!--{$item.end_time}--></td>
		<td class="td2"><!--{$item.seconds}--></td>
		<td class="td2"><!--{if $item.stat == 1}--><font color="red">停止</font><!--{else}--><!--{if $item.runtime == 0}-->尚未运行<!--{else}--><!--{$item.runtime|date_format:"%Y-%m-%d %H:%M:%S"}--><!--{/if}--><!--{/if}--></td>
		<td class="td2">
          <a href="<!--{url action=run id=$item.id}-->" class="fa fa-play-circle" title="立即执行"></a>
          <!--{if $item.stat == 1}-->
          <a href="<!--{url action=stat id=$item.id stat=0}-->" class="fa fa-repeat" title="恢复"></a>
          <!--{else}-->
          <a href="<!--{url action=stat id=$item.id stat=1}-->" class="fa fa-ban" title="停止"></a>
          <!--{/if}-->
          <a href="<!--{url action=edit id=$item.id}-->" class="fa fa-pencil" title="编辑"></a>
          <a href="<!--{url action=remove id=$item.id}-->" onclick="return confirm('确定删除?')" class="fa fa-times" title="删除"></a>
		</td>
      </tr>
      <!--{foreachelse}-->
      <tr>
        <td class="p10" colspan="13"><div class="admin_tips">啊哦，没有相关定时脚本！</div></td>
      </tr>
      <!--{/foreach}-->
    </table>
  </div>
  <!--{pager pager=$pager}-->
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
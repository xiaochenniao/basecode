<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li><a href="<!--{$pageurl}-->.do">组管理</a></li>
      <li class="current"><a href="#"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->组</a></li>
    </ul>
  </div>
  <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
    <input type="hidden" name="formhash" value="<!--{formhash}-->" />
    <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
    <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->用户组</h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr1 vt">
          <td class="td1">名称<span class="s1">*</span></td>
          <td class="td2"><input name="info[group_name]" value="<!--{$info.group_name}-->" id="group_name" type="text" class="input input_wa" alt="用户组名称:空" /><div class="tip_a" id="showResult_group_name"> </div></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>
        <tr class="tr1 vt">
          <td class="td1">排序</td>
          <td class="td2"><input name="info[order_id]" value="<!--{$info.order_id|default:50}-->" type="text" class="input input_wc" /></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>
      </table>
    </div>
    <div class="tac mb10">
      <span class="btn"><span>
      <button type="submit">提 交</button>
      </span></span> </div>
  </form>
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">密码修改</a></li>
    </ul>
  </div>
  <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
    <input type="hidden" name="formhash" value="<!--{formhash}-->" />
    <input type="hidden" name="info[id]" value="<!--{$logininfo.id}-->" />
    <h2 class="h1">密码修改</h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr1 vt">
          <td class="td1">账号</td>
          <td class="td2"><!--{$logininfo.username}--><!--{if $logininfo.fullname}-->【<!--{$logininfo.fullname}-->】<!--{/if}--></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>
        <!--{if !$force}-->
        <tr class="tr1 vt">
          <td class="td1">旧密码<span class="s1">*</span></td>
          <td class="td2"><input name="info[oldpasswd]" id="oldpasswd" type="password" class="input input_wa" alt="旧密码:空/长度@5-16/英文数字" autocomplete = "off" /><div class="tip_a" id="showResult_oldpasswd"> </div></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>
        <!--{/if}-->
        <tr class="tr1 vt">
          <td class="td1">新密码<span class="s1">*</span></td>
          <td class="td2"><input name="info[password]" id="newpassword" type="password" class="input input_wa" alt="新密码:空/长度@5-16/英文数字"/><div class="tip_a" id="showResult_newpassword"></div></td>
          <td class="td2"><div class="help_a"></div></td>
        </tr>
        <tr class="tr1 vt">
          <td class="td1">确认密码<span class="s1">*</span></td>
          <td class="td2"><input name="info[newpassword1]" id="newpassword1" type="password" class="input input_wa" alt="确认密码:空/确认@newpassword" /><div class="tip_a" id="showResult_newpassword1"> </div></td>
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
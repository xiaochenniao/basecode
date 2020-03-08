<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li><a href="<!--{$pageurl}-->.do">内部账号管理</a></li>
            <li class="current"><a href="#"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->内部账号</a></li>
        </ul>
    </div>
    <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->内部账号</h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr1 vt">
                    <td class="td1">账号<span class="s1">*</span></td>
                    <td class="td2"><input name="info[username]" value="<!--{$info.username}-->" id="username" type="text" class="input input_wa"<!--{if $info.id==1000}--> readonly<!--{else}--> alt="账号:空"<!--{/if}--> /><div class="tip_a" id="showResult_username"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">密码<span class="s1">*</span></td>
                    <td class="td2"><input name="info[password]" value="<!--{$info.password}-->" id="password" type="password" class="input input_wa" alt="密码:<!--{if !$info.password}-->空/<!--{/if}-->长度@6-16/英文数字"/><div class="tip_a" id="showResult_password"></div></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">管理组<span class="s1">*</span></td>
                    <td class="td2"><select name="info[usergroup]" class="select_wa"<!--{if $info.id==1000}--> disabled="disabled"<!--{/if}-->>
                                            <!--{html_options options=$groups selected=$info.usergroup}-->
                    </select></td>
                <td class="td2"><div class="help_a"></div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">姓名<span class="s1">*</span></td>
                <td class="td2"><input name="info[fullname]" value="<!--{$info.fullname}-->" id="fullname" type="text" class="input input_wa" /><div class="tip_a" id="showResult_fullname"> </div></td>
                <td class="td2"><div class="help_a"> </div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">手机<span class="s1">*</span></td>
                <td class="td2"><input name="info[mobile]" value="<!--{$info.mobile}-->" id="mobile" type="text" class="input input_wa" /><div class="tip_a" id="showResult_mobile"> </div></td>
                <td class="td2"><div class="help_a"> </div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">QQ</td>
                <td class="td2"><input name="info[qq]" value="<!--{$info.qq}-->" id="qq" type="text" class="input input_wa" /><div class="tip_a" id="showResult_qq"> </div></td>
                <td class="td2"><div class="help_a"> </div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">邮箱</td>
                <td class="td2"><input name="info[mail]" value="<!--{$info.mail}-->" id="mail" type="text" class="input input_wa" /><div class="tip_a" id="showResult_mail"> </div></td>
                <td class="td2"><div class="help_a"> </div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">客服名称</td>
                <td class="td2"><input name="info[kfname]" value="<!--{$info.kfname}-->" id="kfname" type="text" class="input input_wa" /><div class="tip_a" id="showResult_kfname"> </div></td>
                <td class="td2"><div class="help_a"> </div></td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">职位<span class="s1">*</span></td>
                <td class="td2">
                    <label><input type='radio' name="info[job]" value="1" <!--{if $info.job == 1 || !$info.id}-->checked<!--{/if}-->>员工</label> 
                    <label><input type='radio' name="info[job]" value="2" <!--{if $info.job == 2 }-->checked<!--{/if}-->>经理</label>
                </td>
            </tr>
            <tr class="tr1 vt">
                <td class="td1">是否启用</td>
                <td class="td2"><ul class="list_A list_80 cc">
                        <li>
                            <input name="info[status]" type="checkbox" value="1"<!--{if $info.status==1}--> checked<!--{/if}--> />
                                   启用</li>
                    </ul></td>
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
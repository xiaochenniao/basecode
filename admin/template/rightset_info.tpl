<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li><a href="<!--{$pageurl}-->.do">权限管理</a></li>
            <li class="current"><a href="#"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->权限</a></li>
        </ul>
    </div>
    <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->权限</h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr1 vt">
                    <td class="td1">上级权限<span class="s1">*</span></td>
                    <td class="td2"><select name="info[parent_id]" id="parentNavId" class="select_wa">
                            <option value="0">顶级权限</option>
                            <!--{foreach from=$purviews item=purview}-->
                            <option value="<!--{$purview.id}-->" <!--{if $purview.id==$info.parent_id}-->selected<!--{/if}-->><!--{$purview.prename|replace:'<i class="lower"></i>':'&nbsp;|--&nbsp;'}--><!--{$purview.purview_name}--></option>
                            <!--{/foreach}-->
                        </select></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">权限名称<span class="s1">*</span></td>
                    <td class="td2"><input name="info[purview_name]" value="<!--{$info.purview_name}-->" id="purview_name" type="text" class="input input_wa" alt="权限名称:空" /><div class="tip_a" id="showResult_purview_name"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">权限标识<span class="s1">*</span></td>
                    <td class="td2"><input name="info[purview_link]" value="<!--{$info.purview_link}-->" id="purview_link" type="text" class="input input_wa" alt="权限标识:空" /><div class="tip_a" id="showResult_purview_link"></div></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">顺序</td>
                    <td class="td2"><input name="info[order_id]" id="order_id" value="<!--{$info.order_id|default:50}-->" type="text" class="input input_we" alt="顺序:空/数字" /><div class="tip_a" id="showResult_order_id"></div></td>
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
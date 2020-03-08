<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li><a href="<!--{$pageurl}-->/list.do">省份列表</a></li>
            <!--{foreach from=$trees item=item}-->
            <li><a href="<!--{$pageurl}-->/list.do?pid=<!--{$item.id}-->"><!--{$item.name}--></a></li>
            <!--{/foreach}-->
            <li class="current"><a href="#"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->地区</a></li>
        </ul>
    </div>
    <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->地区</h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr1 vt">
                    <td class="td1">父级<span class="s1">*</span></td>
                    <td class="td2"><select name="info[upid]" id="parentNavId" class="select_wa">
                            <!--{if $pid<=0}--><option value="0" selected="selected">省份</option>
                            <!--{else}-->
                            <!--{foreach from=$areas item=item}-->
                            <option value="<!--{$item.id}-->" <!--{if $item.id==$info.upid}-->selected<!--{/if}-->><!--{$item.joinname|default:$item.name}--></option>
                            <!--{/foreach}-->
                            <!--{/if}-->
                        </select></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">名称<span class="s1">*</span></td>
                    <td class="td2"><input name="info[name]" value="<!--{$info.name}-->" id="name" type="text" class="input input_wa" alt="地区名称:空" /><div class="tip_a" id="showResult_name"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">位置顺序</td>
                    <td class="td2"><input name="info[displayorder]" value="<!--{$info.displayorder|default:50}-->" type="text" class="input input_wa" /></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">是否启用</td>
                    <td class="td2"><ul class="list_A list_80 cc">
                            <li>
                                <input name="info[isshow]" type="checkbox" value="1"<!--{if $info.isshow==1}--> checked<!--{/if}--> />
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
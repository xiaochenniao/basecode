<!--{include file="header.tpl"}-->
<script src="<!--{url}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li><a href="<!--{url action=list}-->"><span>定时脚本列表</span></a></li>
            <!--{if $info.id}-->
            <li class="current"><a href="javascript:;"><span>修改定时脚本</span></a></li>
            <!--{else}-->
            <li class="current"><a href="javascript:;"><span>添加定时脚本</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
    <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->定时脚本</h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr1 vt">
                    <td class="td1">脚本名称<span class="s1">*</span></td>
                    <td class="td2"><input name="info[script]" value="<!--{$info.script|default:''}-->" id="script" type="text" class="input input_wb" alt="脚本名称:空" /><div class="tip_a" id="showResult_script"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">间隔秒数<span class="s1">*</span></td>
                    <td class="td2"><input name="info[seconds]" value="<!--{$info.seconds|default:300}-->" id="seconds" type="text" class="input input_we" alt="间隔秒数:空/数字"/><div class="tip_a" id="showResult_seconds"></div></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">开始小时</td>
                    <td class="td2"><input name="info[start_time]" value="<!--{$info.start_time|default:8}-->" id="start_time" type="text" class="input input_we" alt="开始小时:数字"/><div class="tip_a" id="showResult_start_time"></div></td>
                    <td class="td2"></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">结束小时</td>
                    <td class="td2"><input name="info[end_time]" value="<!--{$info.end_time|default:1}-->" id="end_time" type="text" class="input input_we" alt="结束小时:数字"/><div class="tip_a" id="showResult_end_time"></div></td>
                    <td class="td2"></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">脚本描述</td>
                    <td class="td2"><textarea name="info[descrition]" id="description" style="width: 400px;height: 80px;" alt="脚本名称:空"><!--{$info.descrition}--></textarea><div class="tip_a" id="showResult_script"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
            </table>
        </div>
        <div class="tal ml140">
            <span class="btn"><span>
                    <button type="submit">提 交</button>
                </span></span> </div>
    </form>
    <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
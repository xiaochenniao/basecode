<!--{include file="header.tpl"}-->
<div class="wrap">
    <form action="<!--{$pageurl}-->/save.do" method="post">
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->">
        <h2 class="h1"><span class="mr20">常用选项定制</span><span class="linka">注:最多定制15条</span></h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <!--{foreach from=$menulist key=k item=m}-->
                <tr class="tr1 vt">
                    <td class="td1"><!--{$m.name}--></td>
                    <td id="<!--{$m.id}-->" class="td2"><ul class="list_A list_160" style="width:100%;">
                            <!--{foreach from=$m.items key=k1 item=m1}-->
                            <li>
                                <input type="checkbox" name="diydb[]" value="<!--{$m1.id}-->" <!--{$m1.check}-->>
                                       <!--{$m1.name}--></li>
                            <!--{/foreach}-->
                        </ul></td>
                </tr>
                <!--{/foreach}-->
            </table>
        </div>
        <div class="tac mb10"><span class="btn"><span>
                    <button type="submit">提 交</button>
                </span></span> </div>
    </form>
    <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
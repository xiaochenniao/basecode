<!--{include file="header.tpl"}-->
<div class="wrap">
    <form action="<!--{$pageurl}-->/purview.do" method="post">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <input type="hidden" name="m" value="<!--{$m}-->" />
        <div class="nav3 mb10">
            <ul class="cc" id="purviewtype">
                <!--{foreach from=$purviewtops item=top name=ptop}-->
                <li id="<!--{$top.purview_link}-->" onClick="showaction('purview', '<!--{$top.purview_link}-->')"<!--{if $smarty.foreach.ptop.index==0}--> class="current"<!--{/if}-->><a href="javascript:;" hidefocus="true"><!--{$top.purview_name}--></a></li>
                <!--{/foreach}-->
            </ul>
        </div>
        <!--{foreach from=$purviews item=purview1 name="ptop2"}-->
        <div id="purview<!--{$purview1.purview_link}-->"<!--{if $smarty.foreach.ptop2.index>0}--> style="display:none;"<!--{/if}-->>
             <h3 class="h1">选择权限<input type="checkbox" id="main<!--{$purview1.purview_link}-->" onclick="checkChild(this);"/></h3>
            <div class="admin_table mb10" id="main<!--{$purview1.purview_link}-->_ul">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <!--{foreach from=$purview1.children item=purview2}-->
                    <!--{if $purview2.purview_link}-->
                    <!--{assign var="pur_link" value="$purview2.purview_link"}-->
                    <tr class="tr1 vt">
                        <td class="td1"><!--{$purview2.purview_name}--><input type="checkbox" id="sub<!--{$purview2.purview_link}-->" onclick="checkChild(this);"/></td>
                        <td id="sub<!--{$purview2.purview_link}-->_ul" class="td2"><ul class="list_A list_160" style="width:100%;">
                                <!--{foreach from=$purview2.children item=purview3}-->
                                <li>
                                    <input type="checkbox" name="info[purview][<!--{$purview2.purview_link}-->][]" value="<!--{$purview3.purview_link}-->"
                                           <!--{if $info.purview}-->
                                           <!--{foreach from=$info.purview key=key item=value}-->
                                           <!--{if $key == $purview2.purview_link}-->
                                           <!--{if $purview3.purview_link|in_array:$value}-->
                                           checked
                                           <!--{/if}-->
                                           <!--{/if}-->
                                           <!--{/foreach}-->
                                           <!--{/if}-->
                                           >
                                           <!--{$purview3.purview_name}--></li>
                                <!--{/foreach}-->
                            </ul></td>
                    </tr>
                    <!--{/if}-->
                    <!--{/foreach}-->
                </table>
            </div>
        </div>
        <!--{/foreach}-->
        <div class="tac mb10"><span id="submit" class="btn"><span>
                    <button type="submit" onFocus="blur();">提 交</button>
                </span></span></div>
    </form>
    <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/checkform.js" type="text/javascript"></script>
<link href="<!--{url}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<script src="<!--{url}-->/js/jquery.js"></script>
<script src="<!--{url}-->/js/wbox.js" type="text/javascript"></script>
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li><a href="<!--{$pageurl}-->.do">菜单管理</a></li>
            <li class="current"><a href="#"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->菜单</a></li>
        </ul>
    </div>

    <!-- todo 添加可选用图标 -->
    <div class="show-iconbox">
        <table>
            <tr>
                <td colspan="12" class="iconbox"></td>
            </tr>
            <tr>
                <td>common</td>
                <td>initiator</td>
                <td>config</td>
                <td>consumer</td>
                <td>content</td>
                <td>setcenter</td>
                <td>markoperation</td>
                <td>datacache</td>
                <td>modemanage</td>
                <td>icoImg1</td>
                <td>icoImg2</td>
                <td>icoImg3</td>
            </tr>

            <tr>
                <td colspan="12" class="iconbox"></td>
            </tr>
            <tr>
                <td>icoImg4</td>
                <td>icoImg5</td>
                <td>icoImg6</td>
                <td>icoImg7</td>
                <td>icoImg8</td>
                <td>icoImg9</td>
                <td>icoImga</td>
                <td>icoImgb</td>
                <td>icoImgc</td>
                <td>icoImgd</td>
                <td>icoImge</td>
                <td>icoImgf</td>
            </tr>
        </table>
    </div>

    <form action="<!--{$pageurl}-->/save.do" method="post" onsubmit="return fm_chk(this);">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <input type="hidden" name="info[id]" value="<!--{$info.id}-->" />
        <h2 class="h1"><!--{if $info.id}-->修改<!--{else}-->添加<!--{/if}-->菜单</h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr1 vt">
                    <td class="td1">上级菜单<span class="s1">*</span></td>
                    <td class="td2"><select name="info[parent_id]" id="parentNavId" class="select_wa">
                            <option value="0" selected="selected">顶级菜单</option>
                            <!--{foreach from=$menus item=menu}-->
                            <option value="<!--{$menu.id}-->" <!--{if $menu.id==$info.parent_id}-->selected<!--{/if}-->><!--{$menu.prename|replace:'<i class="lower"></i>':'&nbsp;|--&nbsp;'}--><!--{$menu.menu_name}--></option>
                            <!--{/foreach}-->
                        </select></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">菜单名称<span class="s1">*</span></td>
                    <td class="td2"><input name="info[menu_name]" value="<!--{$info.menu_name}-->" id="menu_name" type="text" class="input input_wa" alt="菜单名称:空" /><div class="tip_a" id="showResult_menu_name"> </div></td>
                    <td class="td2"><div class="help_a"> </div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">链接地址</td>
                    <td class="td2"><input name="info[menu_link]" value="<!--{$info.menu_link}-->" id="menu_link" type="text" class="input input_wb" alt="链接地址:空"/><div class="tip_a" id="showResult_menu_link"></div></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">菜单图标</td>
                    <td class="td2"><input name="info[icon_style]" value="<!--{$info.icon_style}-->" id="icon_style" type="text" class="input input_wb" /><div class="tip_a" id="showResult_menu_link"></div> <a style="margin-left: 20px;" hidefocus="true" href="javascript:;"  onclick="showBox(this.id,{title:'菜单图标',requestType: 'iframe',iframeWH:{width:800,height:300},target:'<!--{$pageurl}-->/getIconList.do?id=<!--{$item.id}-->'})" class="btn_add fl"><i>查看菜单图标</i></a></td>
                    <td class="td2"><div class="help_a"></div></td>
                </tr>
                <tr class="tr1 vt">
                    <td class="td1">位置顺序</td>
                    <td class="td2"><input name="info[order_id]" value="<!--{$info.order_id|default:50}-->" type="text" class="input input_wa" /></td>
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

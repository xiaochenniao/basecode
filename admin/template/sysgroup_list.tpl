<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/wbox.js" type="text/javascript"></script>
<link href="<!--{$adminurl}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<div class="wrap">
    <div class="nav3 mb10">
        <ul class="cc">
            <li class="current"><a href="#">组管理</a></li>
            <li><a href="<!--{$pageurl}-->/add.do">添加组</a></li>
        </ul>
    </div>
    <div class="admin_info mb10">
        <h3 class="h1">提示信息</h3>
        <div class="legend">此为组管理，当前查看的为组列表，可以进行批量修改，排序；亦可单独设置每个组拥有的权限。
        </div>
    </div>
    <form action="<!--{$pageurl}-->/update.do" method="post" name="FORM" onsubmit="return comm()">
        <h2 class="h1"><span class="fl mr10">用户组列表</span><a hidefocus="true" href="<!--{$pageurl}-->/add.do" class="btn_add fl"><i>添加组</i></a> <a hidefocus="true" href="<!--{$pageurl}-->/flush.do" class="btn_flush fr"><i>更新缓存</i></a></h2>
        <div class="admin_table mb10">
            <table width="100%" cellspacing="0" cellpadding="0" id="group_table">
                <tr class="tr2 td_bgB">
                    <td width="50"><span class="cp">顺序</span></td>
                    <td>组名称</td>
                    <td width="150">操作</td>
                </tr>
                <!--{foreach from=$rows item=item}-->
                <tr class="tr1 vt">
                    <td class="td2"><input name="info[<!--{$item.id}-->][order_id]" type="text" class="input input_wd" value="<!--{$item.order_id}-->" /></td>
                    <td class="td2"><input name="info[<!--{$item.id}-->][group_name]" type="text" class="input input_wc" value="<!--{$item.group_name}-->" /></td>
                    <td class="td2">
                        <a href="<!--{$pageurl}-->/edit.do?id=<!--{$item.id}-->" class="fa fa-pencil" title="编辑"></a>
                        <!--{if $item.id>1}--><a href="javascript:;" id="usergroup_<!--{$item.id}-->" onclick="showBox(this.id,{title:'设置“<!--{$item.group_name}-->”的权限',requestType: 'iframe',iframeWH:{width:780,height:420},target:'<!--{$pageurl}-->/purview.do?id=<!--{$item.id}-->'})" class="fa fa-shield" title="设置权限"></a>
                        <!--{if $item.id>1}--> <a href="<!--{$pageurl}-->/remove.do?id=<!--{$item.id}-->" onClick="return confirm('确定删除?删除后该组的用户权限也将清除');" class="fa fa-times" title="删除"></a> <!--{/if}--><!--{/if}--></td>
                </tr>
                <!--{/foreach}-->
            </table>
        </div>
        <div class="tac mb10"> <span class="btn"><span>
                    <button type="submit" name="Submit2">提 交</button>
                </span></span> </div>
    </form>
    <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<script>
    function comm()
    {
        var flag = false;
        $("#group_table").find(".tr1").each(function () {
            var tdArr = $(this).children();
            var group_name = tdArr.eq(1).find('input').val();//获取组名称
            if (!group_name) {
                alert('组名称必须填写');
                flag = true;
                return false;
            }
        });
        if (flag)
            return false;

    }
</script>

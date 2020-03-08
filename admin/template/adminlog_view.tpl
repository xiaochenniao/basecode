<!--{include file="header.tpl"}-->
<style type="text/css">
    .admin_table .td1,.admin_table .td2{padding:4px 5px 4px 15px;line-height:22px;}
</style>
<div class="wrap" style="padding:0px;">
    <div class="cc">
        <div>
            <div class="admin_table">
                <table style="width:600px">
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">操作管理员</td>
                        <td class="td2" style="width:480px">姓名：<!--{$item.adminname}-->；ID：<!--{$item.adminid}--></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">管理IP</td>
                        <td class="td2"><!--{$item.onlineip}--></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">操作URL</td>
                        <td class="td2"><!--{$item.logurl}--></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">时间</td>
                        <td class="td2"><!--{$item.createtime}--></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">具体操作</td>
                        <td class="td2"><!--{$item.dothing}--></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1" style="width:120px">详细数据流</td>
                        <td class="td2"><div style="width:100%;height:120px;overflow:auto;text-align:left;"><pre><!--{$item.requestvar}--></pre></div></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
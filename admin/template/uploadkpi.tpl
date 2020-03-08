<!--{include file="header.tpl"}-->
<div class="admin_table mb10">
    <form action="/media_wx_order/upkpi.do" method='post' enctype="multipart/form-data">
        <table width="100%" cellspacing="0" cellpadding="0" >
            <tr class="tr2 td_bgB">
            <input type="hidden"  name="h_id" value="<!--{$id}-->"/>
            <td>上传kpi:</td>
            <td><input type="file" name='icon'></td>
            </tr>


            <tr  class="tr2 td_bgB">
                <td></td>
                <td><input type="submit" value='确定'/>&nbsp;&nbsp;<input type="button" value='取消'/></td>
            </tr>
        </table>
    </form>
</div>
<div class="c"></div>

</div>
<!--{include file="footer.tpl"}-->

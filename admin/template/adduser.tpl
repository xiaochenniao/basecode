<!--{include file="header.tpl"}-->

<link rel="stylesheet" href="<!--{url}-->/style/jquery-ui.css">
<script src="<!--{url}-->/js/jquery-1.10.2.js"></script>
<script src="<!--{url}-->/js/jquery-ui.js"></script>

<div class="wrap">
    <!--{if !$inajax}-->
    <div class="admin_info mb10">
        <h3 class="h1">提示信息</h3>
        <div class="legend">此为后台订单操作记录页面。
        </div>
    </div>

    <h2 class="h1"><span class="fl mr10">订单操作记录</span></h2><!--{/if}-->
    <div class="admin_table mb10">
        <form action="/cpa_channel/add_user.do" method="post">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr class="tr2 td_bgB">
                    <td>管理员</td>
                    <td>
                        <div class="adnamebox">
                            <input type="text" name="user" id="user" value="<!--{$user.user_name}-->" class="user input"  onclick="cliuser()"  onkeyup="cliuser()"/>
                            <select size="6" id="sel" style="position: absolute;top: 38px;left: 336px;width:153px;z-index: 11;display:none" onclick="assign()">
                            </select>
                        </div>
                    </td>

                <input type="hidden" name="user_id" id="user_id"  value="">
                <input type="hidden" name="qid" id="qid"  value="<!--{$qid}-->">
                <input type="hidden" name="uid" id="uid"  value="<!--{$user.id}-->">
                </tr>
                <tr class="tr2 td_bgB">
                    <td>激活量查看权限</td>
                    <td class="td2">
                        <label><input type='radio' name='j_type' value='0' <!--{if $user.j_type == 0}-->checked="checked"<!--{/if}-->/>关闭</label>&nbsp;&nbsp;
                        <label><input type='radio' name='j_type' value='1' <!--{if $user.j_type == 1}-->checked="checked"<!--{/if}-->/>开启</label>
                    </td>
                </tr>
                <tr class="tr2 td_bgB">
                    <td>转化量查看权限</td>
                    <td class="td2">
                        <label><input type='radio' name='z_type' value='0'<!--{if $user.z_type == 0}-->checked="checked"<!--{/if}-->/>关闭</label>&nbsp;&nbsp;
                        <label><input type='radio' name='z_type' value='1' <!--{if $user.z_type == 1}-->checked="checked"<!--{/if}-->/>开启</label>
                    </td>
                </tr>
                <tr class="tr2 td_bgB">
                    <td></td>
                    <td><input type="submit" name="" id="" value="<!--{if $user.user_name}-->修改<!--{else}-->添加<!--{/if}-->"/></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="c"></div>
</div>
<script>

    $("body").on("click", function () {
        $("#sel").hide();
    })

    $(".adnamebox").on("click", function (e) {
        e.stopPropagation();
    })

    function cliuser(qid)
    {
        $.ajax({
            url: "/cpa_admin_api/username.do",
            type: "post",
            dataType: "json",
            data: {"term":$("#user").val()},
            success: function (data) {
                var client = data;
                var str = '';
                for (i = 0; i < client.length; i++)
                {
                    str += "<option value='" + client[i]['value'] + "'>" + client[i]['label'] + "</option>";
                }
                $("#sel").html(str);
                $("#sel").show();
            }
        });
    }

    function assign(abj)
    {
        var options = $("#sel option:selected");
        $('#user').val(options.text());
        $("#user_id").val(options.val());
        $("#sel").hide();
    }


    /*
     window.onload=function(){
     $(".user").autocomplete({
     minLength: 0,
     source: function( request, response ) {
     $.ajax({
     url : "/cpa_admin_api/username.do",
     type : "post",
     dataType : "json",
     data : {"term":$(".user").val()},
     
     success: function( data ) {
     response( $.map( data, function( item ) {
     return {
     label: item.label,
     value: item.value
     }
     }));
     }
     });
     },
     focus: function( event, ui ) {
     $(".user").val( ui.item.label );
     $("#user_id").val( ui.item.value );
     return false;
     },
     select: function( event, ui ) {
     $(".user").val( ui.item.label );
     $("#user_id").val( ui.item.value );
     return false;
     }
     });
     }
     */
</script>
<!--{include file="footer.tpl"}-->

<!--{include file="global/block_header.tpl"}-->
<div style="top: 25%; left: 50%; margin-left: -200px;" class="tip_wind">
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <!--{if $type == '1'}-->
            <!--{foreach from=$notice item=item}-->
            <h4><span class="s1"><!--{$item}--></span></h4>
            <!--{/foreach}-->
            <br>
            <script>
                if (history.length == 0) {
                    document.write('<p><a href="javascript:window.close();">点击这里关闭此页</a></p>');
                } else {
                    document.write('<p><a href="javascript:history.back();">点击这里返回上一页</a></p>');
                }</script>
            <!--{else}-->
            <!--{foreach from=$notice item=item}-->
            <h4 class="s3"><!--{$item}--></h4>
            <!--{/foreach}-->
            <br>
            <p id="ShowDiv"></p><br>
            <p><a href="<!--{$jumpurl}-->">如果您的浏览器没有自动跳转，请点击这里</a></p>
            <!--{/if}-->
        </dd>
    </dl>
    <div class="c"></div>
</div>
<!--{if $jumpurl}-->
<script language='javascript' type='text/javascript'>
    var secs = 3;

    for (var i = secs; i >= 0; i--) {
        window.setTimeout('doUpdate(' + i + ')', (secs - i) * 1000);
    }
    function doUpdate(num) {
        document.getElementById('ShowDiv').innerHTML = '将在' + num + '秒后自动跳转';
        if (num == 0) {
            window.location.href = '<!--{$jumpurl}-->';
        }
    }

</script>
<!--{/if}-->

<!--{include file="global/block_footer.tpl"}-->
<!--{if $page.page_total > 1}-->
<div class="cc mb10"><div class="pages">
<!--{if $page.pagefrom > 1}-->
<a href="<!--{$page.url|replace:'%7Bpage%7D':1}-->">1 ...</a>
<!--{/if}-->
<!--{section name="loop" loop=$page.page_total+1 start=$page.pagefrom max=$page.num}-->
<!--{if $page.page == $smarty.section.loop.index}-->
<b><!--{$smarty.section.loop.index}--></b>
<!--{else}-->
<a href="<!--{$page.url|replace:'%7Bpage%7D':$smarty.section.loop.index}-->"><!--{$smarty.section.loop.index}--></a>
<!--{/if}-->
<!--{/section}-->
<!--{if ($page.page+$page.num) <= $page.page_total}-->
<a href="<!--{$page.url|replace:'%7Bpage%7D':($page.page+$page.num)}-->">下<!--{$page.num}-->页</a>
<!--{/if}-->
<div class="fl">页数:<!--{$page.page}-->/<!--{$page.page_total}--> 总数:<!--{$page.total}--></div>
<span class="pagesone"><input type="text" onkeydown="javascript: if(event.keyCode==13){ location='<!--{$page.url|replace:'%7Bpage%7D':"'+this.value+'"}-->';return false;}" size="3" id="input_page_go">
<button onclick="location='<!--{$page.url|replace:'%7Bpage%7D':"'+document.getElementById('input_page_go').value+'"}-->'">Go</button></span>
</span>
</div></div>
<!--{/if}-->
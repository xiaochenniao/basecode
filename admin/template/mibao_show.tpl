<!--{include file="header.tpl"}-->
<script src="<!--{url}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li><a href="<!--{$pageurl}-->/apply.do">更换密保卡</a></li>
	  <li class="current"><a href="#">下载密保卡</a></li>
    </ul>
  </div>
  <h2 class="h1">鼠标移至图片处，点击鼠标右键，选择图片另存为可完成下载密保卡。</h2>
  <div class="admin_table mb10">
  <img src="<!--{$imgurl}-->/<!--{$codeimg}-->"/>
  </div>
  <div class="c"></div>
</div>
<iframe src="<!--{$pageurl}-->/show.do?type=down" style="display:none"></iframe>
<!--{include file="footer.tpl"}-->
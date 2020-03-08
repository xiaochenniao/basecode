<!--{include file="header.tpl"}-->
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">缓存管理</a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">此为全站缓存数据管理，可以进行批量清除操作；但Memcached动态缓存需要输入单个KEY进行清除。
    </div>
  </div>
  <form action="<!--{$pageurl}-->/clear.do" method="post" name="FORM">
	<input type="hidden" name="formhash" value="<!--{formhash}-->" />
	<h2 class="h1"><span class="fl mr10">缓存管理</span></h2>
	<div class="admin_table mb10">
	  <table width="100%" cellspacing="0" cellpadding="0">
	    <tr class="tr1 vt">
	      <td class="td1">缓存KEY值</td>
	      <td class="td2">
			<input name="cachekey" type="text" class="input input_wa" style="height:22px;"> 
		  </td>
	    </tr>
		<tr class="tr1 vt">
	      <td class="td1">缓存类型</td>
	      <td class="td2">
			<input type="radio" name="cachetype" value="mem" checked> 
			Memcached
			<input type="radio" name="cachetype" value="file"> 
			文件类
		  </td>
	    </tr>
		<tr class="tr1 vt">
	      <td class="td1">清除方式</td>
	      <td class="td2">
			<input type="radio" name="cleartype" value="key" checked> 
			按输入的Key值清除
			<input type="radio" name="cleartype" value="all"> 
			全部清除
		  </td>
	    </tr>
		<tr class="tr1 vt">
	      <td class="td1"></td>
	      <td class="td2">
			<span class="btn"><span>
		    <button type="submit" name="Submit2">提 交</button>
		    </span></span>
		  </td>
	    </tr>
	  </table>
	</div>
  </form>
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
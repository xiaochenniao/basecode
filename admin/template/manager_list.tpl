<!--{include file="header.tpl"}-->
<script src="<!--{$adminurl}-->/js/wbox.js" type="text/javascript"></script>
<link href="<!--{$adminurl}-->/style/wbox.css" type="text/css" rel="stylesheet" />
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">内部账号管理</a></li>
      <li><a href="<!--{$pageurl}-->/add.do">添加内部账号</a></li>
    </ul>
  </div>
  <div class="admin_info mb10">
    <h3 class="h1">提示信息</h3>
    <div class="legend">此为后台内部账号管理，包括内部账号，编辑，审核员等。当前查看的为内部账号列表，可以进行批量修改，启用等操作。
    </div>
  </div>
  <form action="<!--{$pageurl}-->/update.do" method="post" name="FORM">
    <h2 class="h1"><span class="fl mr10">后台内部账号列表</span><a hidefocus="true" href="<!--{$pageurl}-->/add.do" class="btn_add fl"><i>添加内部账号</i></a> <a hidefocus="true" href="<!--{$pageurl}-->/flush.do" class="btn_flush fr"><i>更新缓存</i></a></h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="tr2 td_bgB">
          <td width="30"><span onClick="CheckAll(document.FORM)" class="cp">启用</span></td>
          <td>账号</td>
          <td>姓名</td>
          <td>组别</td>
          <td>登录次数</td>
          <td>最后登录时间</td>
          <td>最后登录IP</td>
          <td>操作</td>
        </tr>
        <!--{foreach from=$managers item=item}-->
        <tr class="tr1 vt">
          <td class="td2"><input name="info[<!--{$item.id}-->][status]" type="checkbox" value="1"<!--{if $item.status==1}--> checked<!--{/if}--> <!--{if $item.id==1000}--> disabled="disabled"<!--{/if}-->/></td>
          <td class="td2"><input name="info[<!--{$item.id}-->][username]" type="text" class="input input_wc" value="<!--{$item.username}-->" <!--{if $item.id==1000}--> disabled="disabled"<!--{/if}-->/></td>
          <td class="td2"><input name="info[<!--{$item.id}-->][fullname]" type="text" class="input input_wc" value="<!--{$item.fullname}-->"<!--{if $item.id==1000 && $item.id!=$logininfo.id}--> disabled="disabled"<!--{/if}-->/></td>
          <td class="td2">
            <select name="info[<!--{$item.id}-->][usergroup]"<!--{if $item.id==1000}--> disabled="disabled"<!--{/if}-->>
                <!--{html_options options=$groups selected=$item.usergroup}-->
            </select>
          </td>
          <td class="td2"><!--{$item.logintimes}--></td>
          <td class="td2"><!--{$item.lastlogintime}--></td>
          <td class="td2"><!--{$item.lastloginip}--></td>
          <td class="td2">
              <!--{if $item.id>1000 || $item.id==$logininfo.id}-->
            <a href="<!--{$pageurl}-->/edit.do?id=<!--{$item.id}-->" class="fa fa-pencil" title="编辑"></a>
            <!--{/if}--> 
            <!--{if $item.id>1000}--> 
            <a href="javascript:;" id="manager_<!--{$item.id}-->" onclick="showBox(this.id,{title:'设置“<!--{$item.fullname}-->”的权限',requestType: 'iframe',iframeWH:{width:780,height:420},target:'/sysgroup/purview.do?uid=<!--{$item.id}-->'})" class="fa fa-shield" title="设置权限"></a>
            <!--{/if}-->
            <!--{if $item.islocked==1}--> 
            <a href="<!--{$pageurl}-->/unlock.do?id=<!--{$item.id}-->"class="fa fa-unlock-alt" title="解锁"></a>
            <!--{/if}-->
          </td>
        </tr>
        <!--{/foreach}-->
      </table>
    </div>
    <div class="tac mb10"> <span class="bt"><span>
      <button type="button" onClick="CheckAll(document.FORM);">全 选</button>
      </span></span> <span class="btn"><span>
      <button type="submit" name="Submit2">提 交</button>
      </span></span> </div>
  </form>
  <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->
<!--{include file="header.tpl"}-->
<script src="<!--{url}-->/js/checkform.js" type="text/javascript"></script>
<div class="wrap">
  <div class="nav3 mb10">
    <ul class="cc">
      <li class="current"><a href="#">申请密保卡</a></li>
    </ul>
  </div>
  <form action="<!--{$pageurl}-->/apply.do" method="post" onsubmit="return fm_chk(this);">
    <input type="hidden" name="formhash" value="<!--{formhash}-->" />
    <h2 class="h1"><!--{if $mibaocard}-->更换<!--{else}-->申请<!--{/if}-->密保卡</h2>
    <div class="admin_table mb10">
      <table width="100%" cellspacing="0" cellpadding="0">
        <!--{if $mibaocard}-->
		<tr class="tr1 vt">
          <td class="td1">卡号</td>
          <td class="td2"><!--{$mibaocard.card_num}--> <a href="<!--{$pageurl}-->/show.do" title="下载原密保卡">下载</a></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>
        <!--{/if}-->
        <tr class="tr1 vt">
          <td class="td1">手机号<span class="s1">*</span></td>
          <td class="td2"><input name="mobile" id="mobile" type="text" class="input input_wa" value="<!--{$admininfo.bdmobile}-->" style="width:90px;margin-right:10px;height:22px;line-height:22px;color:blue;font-weight:bold;text-align:center;" <!--{if $admininfo.bdmobile}-->readonly<!--{/if}--> autocomplete = "off" /> <div id="getcodebutton" style="float:left;margin-right:10px;"><span class="bt2"><span><button type="button" onclick="getValid();" id="getcodebutton2">获取验证码</button></span></span></div><div id="getcodestat" style="float:left;height:30px;line-height:30px;display:none;"></div></td>
          <td class="td2"><div class="help_a"> </div></td>
        </tr>

        <tr class="tr1 vt">
          <td class="td1">手机验证码<span class="s1">*</span></td>
          <td class="td2"><input name="validnum" id="validnum" type="text" class="input input_wa" style="width:70px;height:25px;line-height:25px;color:green;font-weight:bold;text-align:center;font-size:18px;" maxlength="6"/><div class="tip_a" id="showResult_validnum" style="height:30px;line-height:30px;">请在左侧输入手机接收到的验证码，然后点击提交按钮获取密保卡！</div></td>
          <td class="td2"><div class="help_a"></div></td>
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
<script type="text/javascript">
var allt = 300;
function getValid()
{
	var mobile = $.trim($("#mobile").val());
	var repost= /^13[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$|17[0-9]{9}$/;
	if(!repost.test(mobile))
	{
		showMsg("getcodestat",3,"手机号输入不正确！");
	}
	else
	{
		var pageurl = '<!--{$pageurl}-->';
		$("#getcodebutton").hide();
		showMsg("getcodestat",0,"正在发送短信，稍后查收！");
		$.ajax({
			type:"POST",
			url:pageurl+"/sendvalid.do",
			dataType:"json",
			data:'mobile='+mobile+'&random='+Math.random(),
			success:function(m)
			{
				if(m['msg']==1)
				{
					startclock();
					$("#getcodebutton2").attr("disabled",true);
					$("#getcodebutton2").text("（"+allt+"）秒后重新获取");
					$("#getcodebutton").show();
					showMsg("getcodestat",2,"短信验证码已发送，请注意查收！")
				}
				else
				{
					$("#getcodebutton2").text("重新获取验证码");
					showMsg("getcodestat",3,m['msg']);
				}
			}
		});
	}
}

var intsec = null;
function startclock()
{
	intsec = self.setInterval("timeEnd()",1000)
}

function timeEnd()
{
	allt--;
	$("#getcodebutton2").attr("disabled",true);
	$("#getcodebutton2").text("（"+allt+"）秒后重新获取");
	if(allt<1)
	{
		window.clearInterval(intsec);
		$("#getcodebutton2").attr("disabled",false);
		$("#getcodebutton2").text("重新获取验证码");
		$("#getcodebutton").show();
	}
}

function showMsg(_id,_s,_m)
{
	var img = '';
	if(_s=='1')
	{
		img = '<img src="/image/info_help.gif" style="float:left;"/>';
	}
	else if(_s=='2')
	{
		img = '<img src="/image/info_right.gif" style="float:left;"/>';
	}
	else if(_s=='3')
	{
		$("#getcodebutton").show();
		img = '<img src="/image/info_wrong.gif" style="float:left;"/>';
	}
	else
	{
		img = '<img src="/image/onLoad.gif" style="float:left;"/>';
	}
	$("#"+_id).show();
	$("#"+_id).html(img + '<span style="float:left;padding-left:5px;height:25px;line-height:25px;">'+_m+'</span>');
}
</script>
<!--{include file="footer.tpl"}-->
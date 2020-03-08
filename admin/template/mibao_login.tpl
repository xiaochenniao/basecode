<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微博营销平台</title>
<style type="text/css">
body { 
background-color: #fff; 
font-size: 12px; 
font-family: Verdana, Arial, Helvetica, SunSans-Regular, Sans-Serif; 
color:#564b47; 
padding:0px; 
margin:0px; 
} 
#inhalt { 
position:absolute; 
height:220px; 
width:420px; 
margin:-150px 0px 0px -210px; 
top: 50%; 
left: 50%; 
text-align: left; 
padding: 0px; 
background-color: #f5f5f5; 
border: 1px dotted #000000; 
overflow: hidden; 
} 
p, h1 { 
margin: 0px; 
padding: 10px; 
} 
h1 { 
font-size: 11px; 
text-transform:uppercase; 
text-align: right; 
color: #564b47; 
background-color: #90897a; 
} 
a { 
color: #ff66cc; 
font-size: 11px; 
background-color:transparent; 
text-decoration: none; 
}
input{font-size:12px;}
.text {width:30px;}
</style> 
</head>
<body>
<div id="inhalt">
  <p><b>您已绑定密保卡</b><br /><br /> 
为了您的账号安全，本平台对已绑定密保卡的用户进行登录安全验证.<br /> 
如果您不慎遗失您的电子密保卡，请联系管理员解决！<br /> 
<b>请妥善保管好您的电子密保卡，并不要借于他人使用，如不慎泄露由此造成的损失由您个人承担! </b>
</p> 
  <form method="post" name="mblogin" action ="<!--{url}-->/mibao/login.do">
	<input type="hidden" name="formhash" value="<!--{formhash}-->" />
	<table cellpadding="0" cellspacing="0" width="60%" align="center">
	  <tr>
		<!--{foreach from=$codes item=item}-->
        <th style="text-align:left;"><!--{$item}--></th>
        <!--{/foreach}-->
	  </tr>
	  <tr>
		<!--{foreach from=$codes key=key item=item}-->
        <td><input class="text" tabindex="<!--{$key+1}-->" id="mbvalid<!--{$key+1}-->" onkeyup="jumpforcus(<!--{$key+1}-->);" name="mbvalid[<!--{$item}-->]" maxlength="3" /></td>
        <!--{/foreach}-->
	  </tr>
	  <tr>
		<td colspan="3" align="center"><input name="submit" type="image" src="<!--{url}-->/image/login.gif" tabindex="4" style="margin-top:1.5em;width:80px;height:30px;" /></td>
	  </tr>
	</table>
  </form>
</div>
<script language="JavaScript"> 
var imax = <!--{$codes|@count|default:1}-->;
document.getElementById('mbvalid1').focus();
function jumpforcus(_i)
{
	var kv = document.getElementById('mbvalid'+_i).value;
	if(kv.length==3)
	{
		if(_i<imax)
		{
			document.getElementById('mbvalid'+(_i+1)).focus();
		}
	}
}
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微传播管理后台</title>
<style type="text/css">
  body{padding:0;margin:0px;font-size:12px;color:#555;background: #3A3C41;font-family:Verdana;}
  select{margin-left:1.5em;vertical-align:middle;border:1px solid #b4cceb;height:22px;font-size:12px;}
  #main{width:420px;margin:auto;margin-top: 80px;}
  *{padding:0;margin:0}
  input{font-size:12px;}
  #wrap{height:170px;}
  #wrapc{height:300px;background-color: #fff;border-radius: 6px;box-shadow: 0 5px 10px #1C1D1F;}
  #logo{height:88px;width:337px;background:url(<!--{url}-->/image/logo.png) center center no-repeat; -webkit-background-size: 150px; background-size: 150px; margin: 0 auto;}
  .logo{padding-top: 70px;}
  .login{padding-top:40px;}
  .login th{height:40px;line-height:40px;list-style:none;text-align:right;font-weight:normal;width:120px;font-size:16px;padding-bottom: 20px;}
  .login td{text-align:left;padding-bottom: 20px;}
  .input{font-size:16px;vertical-align:middle;height:40px;line-height: 40px;padding: 0 15px;color:#666;outline:none;border: 1px solid #ccc;border-radius: 5px;background-color: #fff;}
  .logo-icon{float:left;background:#fff;padding-right:.5em;margin-left:18px;_margin-left:8px;}
  .logo-icon .pw2{width:206px;}
  .logo-icon .pwpd2{width:206px;}
  .logo-icon .yan2{width:5em;}
  .submit-btn {width: 120px;height: 40px;font-size: 16px;line-height: 40px;color: #fff; background-color: #429DE3;outline: none;border: none;border-radius: 20px; margin-left:18px;margin-top:6px;}
  .bottom{text-align:center;margin:auto;padding-top:40px;color:#888;}
  .bottom a{color: #fff;text-decoration: none;}
  .oculus{margin-left:18px;}
  .nc-container .nc_scale span.btn_slide {color: #fff;font-weight: 700;background-color: #3A3C41;border-radius: 3px;}
  #image {height:40px;margin-left:3px;cursor:pointer;}
</style>
</head>
<body>
<div id="main">
  <div id="wrap">
    <div class="logo">
      <div id="logo"></div>
    </div>
  </div>
  <div id="wrapb"></div>
  <div id="wrapc">
    <div class="login">
      <form method="post" name="login" action ="<!--{url}-->/login.do">
        <input type="hidden" name="formhash" value="<!--{formhash}-->" />
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <th><b>登录账号</b></th>
            <td><div class="logo-icon">
                <input class="input pw2" name="admin_name" type="text" tabindex="1"/>
              </div></td>
          </tr>
          <tr>
            <th><b>登录密码</b></th>
            <td><div class="logo-icon">
                <input class="input pwpd2" type="password" name="admin_pwd" tabindex="2" />
              </div></td>
          </tr>
		  <tr>
            <th><b>验证码</b></th>
            <td>
              <div class="logo-icon"><input class="input yan2" style="width:80px" type="text" name="validate" tabindex="3" id="validate" /></div>
              <img src="/captcha.do" id="image" align="absmiddle" style="" title="点击更新验证码">
			</td>
          </tr>
          <tr>
            <th></th>
            <td><input type="submit" name="submit"  tabindex="4" class="submit-btn" value="登 录" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<div class="bottom"><a href="<!--{url}-->">微传播管理后台</a> <span style="color:#FA891B">2.0</span></div>
<script language="JavaScript">
  document.login.admin_name.focus();

  var validate = document.getElementById('validate');
  var image = document.getElementById('image');

  function updateImg() {
    if(image.style.display=='none'){
      console.log(333);
      image.src='/captcha.do?c='+new Date();
      image.style.display='inline-block';
    }
  }
  validate.onclick = updateImg;
  validate.onfocus = updateImg;

  image.onclick = function(){
    this.src='/captcha.do?c='+new Date();
  }

</script>
</body>
</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <title>图片上传</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script type="text/javascript">
            var now = new Date();
            var defaultimg = "/image/none.gif";

            function $(id) {
                return document.getElementById(id);
            }
            function startProgress()
            {
                $("progresszone").style.display = "block";
                $("progresszone_img").className = "fileimggray";
                $("optionzone").innerHTML = "正在上传...";
            }

            function uploadSuccess(_s, _b) {
                if (parent.document.getElementById("smallpic")) {
                    parent.document.getElementById("smallpic").value = _s;
                }
                if (parent.document.getElementById("bigpic")) {
                    parent.document.getElementById("bigpic").value = _b;
                }
                $("optionzone").innerHTML = '<input type="button" value="更换图片">';
                $("progresszone").style.display = "none";
                $("progresszone_img").className = "fileimg";
                $("progresszone_img").src = '<!--{$imgurl}-->/' + _s;
                $('upfileinput').outerHTML = $('upfileinput').outerHTML;
                $('upfileinput').value = '';
                successimg($('progresszone_img'));
                successMsg("上传成功...");
            }
            function uploadError(_msg) {
                $("optionzone").innerHTML = '<input type="button" value="上传图片">';
                $('upfileinput').outerHTML = $('upfileinput').outerHTML;
                $('upfileinput').value = '';
                $("progresszone").innerHTML = '失败';
                errorimg($('progresszone_img'));
            }

            function errorimg(obj) {
                obj.style.borderTop = '1px solid #FF0000'
                obj.style.borderLeft = '1px solid #FF0000'
                obj.style.borderBottom = '1px solid #FF0000'
                obj.style.borderRight = '1px solid #FF0000'
            }

            function successimg(obj) {
                obj.style.borderTop = '1px solid #AFAFAF'
                obj.style.borderLeft = '1px solid #E2E2E4'
                obj.style.borderBottom = '1px solid #E8E9ED'
                obj.style.borderRight = '1px solid #DFE1E0'
            }

        </script>
        <style>
            div.fileopt{float:left;width:100px;text-align:center;}
            div.fileopt .progresszone{position:absolute;z-index:999;margin-left:10px;margin-top:40px;width:80px;text-align:center;display:none;color:red;}
            img.fileimg{border:1px solid #AFAFAF;margin:10px;width:80px;height:80px;}
            img.fileimggray{border:1px solid #AFAFAF;margin:10px;width:80px;height:80px;opacity:0.30;filter:alpha(opacity=30);}
            img.fileimggray2{border:1px solid #AFAFAF;cursor:pointer;margin:10px;width:80px;height:80px;opacity:0.70;filter:alpha(opacity=70);}
            div.optionzone{line-height:18px;color:green;}
            div.optionzone input{height:22px;}
            .upfileinput{position:absolute;top:100px;opacity:0;filter:alpha(opacity=0);left:15px;top:103px;width:30px;}
            .c {clear:both;float:left;}
            .fl {float:left;}
        </style>
        </head>
        <body style="margin:0px;padding:0px;">
            <div style="float:left">
                <div class="fileopt"><span id="progresszone" class="progresszone"></span><img id="progresszone_img" src="<!--{$file|default:'/image/none.gif'}-->" class="<!--{if $file}-->fileimg<!--{else}-->fileimggray<!--{/if}-->"><br/>
                        <div id="optionzone" class="optionzone"><input type="button" value="<!--{if $file}-->更换<!--{else}-->上传<!--{/if}-->图片"></div>
                </div>
            </div>
            <form action="<!--{$pageurl}-->/save.do" method="post" id="uploadForm" name="uploadForm" enctype="multipart/form-data" target="forumdata">
                <input type="hidden" name="st" value="<!--{$st|default:'0'}-->"/>
                <input type="hidden" name="ftype" value="<!--{$type|default:'img'}-->"/><input type="hidden" name="dir" value="<!--{$dir}-->"/><input type="hidden" name="ifsave" value="0"/>
                <input name="file" type="file" class="upfileinput" id="upfileinput" onchange="startProgress();
                        $('uploadForm').submit();" size="1" hidefocus>
            </form>
            <iframe id="forumdata" name="forumdata" style="display:none;"></iframe>
        </body>
</html>
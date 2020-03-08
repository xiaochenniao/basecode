<script type="text/javascript">
var searchshow = typeof(showsearch)=='undefined' ? false : true;
if(parent.adminNavClass){
	parent.adminNavClass.initTips(searchshow);
}
function checkChild(obj)
{
	var oid = obj.id+'_ul';
	var checked = obj.checked;
	var checkbox = document.getElementById(oid).getElementsByTagName("input");
	for(i=0;i<checkbox.length;i++){
		checkbox[i].checked = checked;
	}
}
function Option(opturl,confirm_msg){
	if(confirm_msg){
		if(confirm(confirm_msg)){
			window.frames['iframe_target'].location.href=opturl;
		}
	}else{
		window.frames['iframe_target'].location.href=opturl;
	}
	
}
function DeleteIt(deleteurl,confirm_msg){
	if(!confirm_msg)confirm_msg = "您确定要删除吗？";
	if(confirm(confirm_msg)){
		window.frames['iframe_target'].location.href=deleteurl;
	}
}
document.onkeydown = function(e){
	var e = e ? e : window.event;
	if (e.keyCode==116){//F5
		parent.frameRefresh(e);
		return false;
	}
	/*
	else if (e.keyCode==37){//←
		parent.prev(e);
	}else if (e.keyCode==39){//→
		parent.next(e);
	}
	*/
	else
	{
		return true;
	}
}
</script>
<iframe id="iframe_target" name="iframe_target" style="display:none;"></iframe>
</body>
</html>
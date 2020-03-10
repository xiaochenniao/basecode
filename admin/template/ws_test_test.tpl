<!--{include file="header.tpl"}-->
<div class="wrap">
    <form action="<!--{$pageurl}-->/save.do" method="post">
        <div class="nav3 mb10">
            <ul id="basictype" class="cc">
                <li id="state" onclick="showaction('basic', 'state')" class="current"><a href="javascript:;" hidefocus="true">站点状态</a></li>
                <li id="info" onclick="showaction('basic', 'info')" ><a href="javascript:;" hidefocus="true">站点信息</a></li>
                <li id="seo" onclick="showaction('basic', 'seo')" ><a href="javascript:;" hidefocus="true">SEO信息</a></li>
            </ul>
        </div>
        <div id="basicstate">
            <h2 class="h1">站点状态设置</h2>
            <div class="admin_table mb10">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr class="tr1 vt">
                        <td class="td1">收号状态</td>
                        <td class="td2"><ul class="list_A list_80">
                                <li>
                                    <input type="radio" value="1" name="info[zhtj]" <!--{if $info.zhtj==1}--> checked<!--{/if}-->>
                                           开启</li>
                                <li>
                                    <input type="radio" value="0" name="info[zhtj]" <!--{if $info.zhtj==0}--> checked<!--{/if}-->>
                                           关闭</li>
                            </ul></td>
                        <td class="td2"><div class="help_a">开启后，允许前台用户提交账号</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">账号单价</td>
                        <td class="td2"><input id="zhfee" name="info[zhfee]" type="text" class="input input_wa" value="<!--{$info.zhfee}-->"/><div class="tip_a" id="showResult_webname"></div></td>
                        <td class="td2"><div class="help_a"> 填写收号系统里每个账号的单价 </div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">解锁绑定-开关</td>
                        <td id="bbsifopen" class="td2"><ul class="list_A list_80">
                                <li>
                                    <input type="radio" value="1" name="info[ifopen][jb]" <!--{if $info.ifopen.jb==1}--> checked<!--{/if}-->>
                                           开</li>
                                <li>
                                    <input type="radio" value="0" name="info[ifopen][jb]" <!--{if $info.ifopen.jb==0}--> checked<!--{/if}-->>
                                           关</li>
                            </ul></td>
                        <td class="td2"><div class="help_a">控制解锁绑定项目的开关</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">解锁项目-开关</td>
                        <td id="bbsifopen" class="td2"><ul class="list_A list_80">
                                <li>
                                    <input type="radio" value="1" name="info[ifopen][js]" <!--{if $info.ifopen.js==1}--> checked<!--{/if}-->>
                                           开</li>
                                <li>
                                    <input type="radio" value="0" name="info[ifopen][js]" <!--{if $info.ifopen.js==0}--> checked<!--{/if}-->>
                                           关</li>
                            </ul></td>
                        <td class="td2"><div class="help_a">控制解锁项目的开关</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">账号绑定-开关</td>
                        <td id="bbsifopen" class="td2"><ul class="list_A list_80">
                                <li>
                                    <input type="radio" value="1" name="info[ifopen][bd]" <!--{if $info.ifopen.bd==1}--> checked<!--{/if}-->>
                                           开</li>
                                <li>
                                    <input type="radio" value="0" name="info[ifopen][bd]" <!--{if $info.ifopen.bd==0}--> checked<!--{/if}-->>
                                           关</li>
                            </ul></td>
                        <td class="td2"><div class="help_a">控制账号绑定项目的开关</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">跳转提示</td>
                        <td class="td2"><ul class="list_A list_80">
                                <li>
                                    <input type="radio" value="1" name="info[ifjump]" <!--{if $info.ifjump==1}--> checked<!--{/if}-->>
                                           开启</li>
                                <li>
                                    <input type="radio" value="0" name="info[ifjump]" <!--{if $info.ifjump==0}--> checked<!--{/if}-->>
                                           关闭</li>
                            </ul></td>
                        <td class="td2"><div class="help_a">开启后，前台操作都将进行提示</div></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="basicinfo" style="display:none;">
            <h2 class="h1">站点信息设置</h2>
            <div class="admin_table mb10">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr class="tr1 vt">
                        <td class="td1">网站名称</td>
                        <td class="td2"><input id="webname" name="info[webname]" type="text" class="input input_wa" value="<!--{$info.webname}-->"/><div class="tip_a" id="showResult_webname"></div></td>
                        <td class="td2"><div class="help_a"> 填写站点名称 </div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">站点地址</td>
                        <td class="td2"><input id="weburl" name="info[weburl]" type="text" class="input input_wa" value="<!--{$info.weburl}-->"/><div class="tip_a" id="showResult_weburl"></div></td>
                        <td class="td2"><div class="help_a">填写您站点的完整网址。不要以斜杠 (“/”) 结尾</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">后台地址</td>
                        <td class="td2"><input id="adminurl" name="info[adminurl]" type="text" class="input input_wa" value="<!--{$info.adminurl}-->"/><div class="tip_a" id="showResult_domain"></div></td>
                        <td class="td2"><div class="help_a">填写您后台的完整网址。不要以斜杠 (“/”) 结尾</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">上传文件地址</td>
                        <td class="td2"><input id="imgurl" name="info[imgurl]" type="text" class="input input_wa" value="<!--{$info.imgurl}-->"/></td>
                        <td class="td2"><div class="help_a"></div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">程序版本</td>
                        <td class="td2"><input name="info[version]" value="<!--{$info.version}-->" type="text" class="input input_wa" /></td>
                        <td class="td2"><div class="help_a">主要是用于css,js等的版本</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">ICP 备案信息</td>
                        <td class="td2"><input name="info[icp]" value="<!--{$info.icp}-->" type="text" class="input input_wa" /></td>
                        <td class="td2"><div class="help_a">填写 ICP 备案的信息，例如: 京ICP备xxxxxxxx号</div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">第三方统计代码</td>
                        <td class="td2"><textarea name="info[statscode]" class="textarea"><!--{$info.statscode}--></textarea></td>
                        <td class="td2"><div class="help_a">在第三方网站上注册并获得统计代码，并将统计代码粘贴在下面文本框中即可</div></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="basicseo" style="display:none;">
            <h2 class="h1">搜索引擎优化设置</h2>
            <div class="admin_table mb10">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr class="tr1 vt">
                        <td class="td1">SEO标题</td>
                        <td class="td2"><input id="seo_title" name="info[seo_title]" type="text" class="input input_wa" value="<!--{$info.seo_title}-->"/><div class="tip_a" id="showResult_seo_title"></div></td>
                        <td class="td2"><div class="help_a"> 填写SEO标题 </div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">SEO关键词</td>
                        <td class="td2"><input id="seo_keyword" name="info[seo_keyword]" type="text" class="input input_wa" value="<!--{$info.seo_keyword}-->"/><div class="tip_a" id="showResult_seo_keyword"></div></td>
                        <td class="td2"><div class="help_a"></div></td>
                    </tr>
                    <tr class="tr1 vt">
                        <td class="td1">SEO描述</td>
                        <td class="td2"><textarea name="info[seo_description]" class="textarea"><!--{$info.seo_description}--></textarea></td>
                        <td class="td2"><div class="help_a"></div></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="tac mb10"><span id="submit" class="btn"><span>
                    <button  onclick="sendMsg();">提 交</button>
                </span></span></div>
    </form>
                        <button  onclick="sendMsg();">提 交1</button>
    <div class="c"></div>
</div>
<!--{include file="footer.tpl"}-->

<script type="text/javascript">

    var wsServer = 'ws://192.168.124.9:9502';
    var websocket = new WebSocket(wsServer);
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
    };

    websocket.onclose = function (evt) {
        console.log("Disconnected");
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
    };
   

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };
    
    function sendMsg() {
        
        websocket.send('发送消息'); //发送消息
    }

</script>
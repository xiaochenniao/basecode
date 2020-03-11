<!--{include file="header.tpl"}-->
<div class="wrap">

    <div class="nav3 mb10">
        <ul id="basictype" class="cc">
            <li id="state" onclick="showaction('basic', 'state')" class="current"><a href="javascript:;" hidefocus="true">wbsocket_test</a></li>
            
        </ul>
    </div>


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
console.log(123);
// 服务端主动推送消息时会触发这里的onmessage
ws = new WebSocket("ws://msy.meishangyun.com:8282");
ws.onopen = function(){
    console.log('open');
    ws.send('hello');
};
ws.onmessage = function(e){
    var data =  JSON.parse(e.data);
    var type = data.type || '';
    switch(type){
        case 'init':
            // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
            // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
            var postData = {client_id: data.client_id};
            var url = module + 'CustomerService/bindUid';
            $.ajax({
                url: url,
                data: postData,
                type: 'post',
                beforeSend: function(xhr){
                    $('.loading').show();
                },
                error:function(xhr){
                    $('.loading').hide();
                    dialog.error('AJAX错误');
                },
                success: function(data){
                    $('.loading').hide();
                    if(data.status==0){
                        dialog.error(data.info);
                    }else if(data.code==1 && data.data=='no_login'){
                        loginDialog();
                    }else{
                        console.log(data);
                    }
                }
            });
            break;
        case 'msg':
            console.log(data);
            break;
        default :
            // 当mvc框架调用GatewayClient发消息时直接alert出来
            console.log('default');
            console.log(data);
    }
};
ws.onclose = function(e){
    console.log(e);
};
ws.onerror = function () {
    console.log('error');
};
console.log(123);
// 服务端主动推送消息时会触发这里的onmessage
ws = new WebSocket("ws://msy.meishangyun.com:8282");
ws.onopen = function(e){
    console.log('open');
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
                success: function(msg){
                    $('.loading').hide();
                    if(msg.status==0){
                        dialog.error(msg.info);
                    }else if(msg.code==1 && msg.data=='no_login'){
                        loginDialog();
                    }else{
                        console.log(data);
                        //绑定成功
                        // var sendMsg = {
                        //     'type':'msg',
                        //     'client_id':msg.info.client_id,
                        //     'user_id':msg.info.user_id,
                        //     'client_msg':'Hello World!'
                        // };
                        // sendMsg = JSON.stringify(sendMsg);
                        // ws.send(sendMsg);
                    }
                }
            });
            break;
        case 'msg':
            console.log(data);
            break;
        case 'end':
            console.log(data);
            break;
        default :
            console.log('default');
    }
};
ws.onerror = function (e) {
    console.log('error');
};
ws.onclose = function(e){
    console.log('close');
};
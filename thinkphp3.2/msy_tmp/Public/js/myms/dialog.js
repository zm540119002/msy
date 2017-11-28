var dialog = {
    // 错误弹出层
    error: function(message) {
        layer.open({
            content: message?message:'出错啦！'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        // layer.msg(message?message:'出错啦！',{icon: 2,time: 3});
    },

    //成功弹出层
    success : function(message,url) {
        layer.open({
            content : message?message:'成功',
            time : 1,
            end : function(){
                if(url){
                    location.href=url;
                }
            }
        });
    },

    // 确认弹出层
    confirm : function(message, callback) {
        //询问框
        layer.confirm(message, {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.isFunction(callback) && callback;
        }, function(){
        });
    },

    //消息框
    msg:function (message,option,callback) {
        var _option ={};
        if(message.status==0){
            _option ={icon: 2,time: 3};
        }else if(message.status==1){
            _option ={icon: 1,time: 1};
        }
        $.extend(_option,option);
        layer.msg(message.info,_option,callback);
    }
};
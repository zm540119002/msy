var dialog = {
    // 错误弹出层
    error: function(message) {
        layer.open({
            content: message?message:'出错啦！'
            ,icon: 2
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
    },

    //成功弹出层
    success : function(message,url) {
       
        layer.open({
            content : message?message:'成功',
            time : 1,
            skin: 'msg',
            end : function(){
                if(url){
                    location.href=url;
                }
            }
        });
    }
};
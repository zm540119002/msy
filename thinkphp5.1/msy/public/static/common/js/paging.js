//下拉获取分页列表-公共回调函数
function pullDownListCallBack(){
}

//下拉获取分页列表
var currentPage = 1;//记录当前页
var requestEnd = false;//请求结束标记
function pullDownList(config,postData) {
    var _postData = $.extend({},postData);
    _postData.page = currentPage ? currentPage : 1;
    _postData.pageSize = _postData.pageSize?_postData.pageSize:4;
    //请求结束标志
    if(requestEnd){
        dialog.error('没有更多啦');
        loadTrigger = true;
        return false;
    }
    config.url = config.url?config.url:action;
    config.callBack = config.callBack?config.callBack:pullDownListCallBack;
    $.ajax({
        url: url,
        data: _postData,
        type: 'get',
        dataType:'json',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function (xhr) {
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            config.callBack(data);
        }
    });
}


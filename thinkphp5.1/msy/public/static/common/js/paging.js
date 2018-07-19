//下拉获取分页列表-公共回调函数
function getPagingListCallBack(config,postData,data){
    config.container.html(data);
}

//下拉获取分页列表
// var currentPage = 1;//记录当前页
// var requestEnd = false;//请求结束标记
function getPagingList(config,postData) {
    //容器
    config.container = config.container?config.container:$("#list");
    //提交路径
    config.url = config.url?config.url:action;
    //type为true时为下拉分页,默认为普通分页
    config.type = config.type?config.type:false;
    //回调函数名
    config.callBack = config.callBack?config.callBack:getPagingListCallBack;
    //要提交的数据
    postData = postData?postData:$('#form1').serializeObject();
    postData.page = postData.currentPage ? postData.currentPage : config.currentPage;
    postData.pageSize = postData.pageSize ? postData.pageSize:4;
    //请求结束标志
    if(config.type && config.requestEnd){
        dialog.error('没有更多啦');
        loadTrigger = true;
        return false;
    }
    $.ajax({
        url: config.url,
        data: postData,
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
            $('.loading').hide();
            config.callBack(config,postData,data);
        }
    });
}

//下拉加载更多
var loadTrigger = false;//加载触发器
$(window).on('scroll',function(){
    if(loadTrigger && $(document).scrollTop()+$(window).height()>=$(document).height()){
        loadTrigger = false;
        getPagingList(config,postData);
    }
});
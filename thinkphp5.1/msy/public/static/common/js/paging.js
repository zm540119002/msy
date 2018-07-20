//获取分页列表-公共回调函数
function getPagingListCallBack(config,data){
    config.container.html(data);
}

/**获取分页列表
 * @param config.currentPage 必须配置
 * @param config.loadTrigger 必须配置
 * @param config.requestEnd  必须配置
 * @param config   必须是全局变量
 * @param postData 必须是全局变量
 */
function getPagingList(config,postData) {
    //容器
    config.container = config.container?config.container:$("#list");
    //提交路径
    config.url = config.url?config.url:action;
    //type为true时为分页,默认为普通分页
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
        config.loadTrigger = true;
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
            config.callBack(config,data);
            if(config.type){
                if($($.parseHTML(data)).length<postData.pageSize){
                    config.requestEnd = true;
                }
                config.currentPage ++;
                config.loadTrigger = true;
            }
        }
    });
}

//窗口滚动条-加载更多
$(window).on('scroll',function(){
    if(config.loadTrigger && $(document).scrollTop()+$(window).height()>=$(document).height()){
        config.loadTrigger = false;
        getPagingList(config,postData);
    }
});

$('.classify-label-content ').on('scroll',function(){
    var listHeight=document.getElementById('list').scrollHeight;
    if(config.loadTrigger && $('.classify-label-content ').scrollTop()+$('.classify-label-content ').height()>=listHeight){
        config.loadTrigger = false;
        config.activityStatus = $('.category-tab li.current a').data('activity-status');
        getPagingList(config,postData);
    }
});


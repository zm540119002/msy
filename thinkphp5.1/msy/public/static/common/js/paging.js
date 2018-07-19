//获取分页列表-公共回调函数
function getPagingListCallBack(config,postData,data){
    config.container.html(data);
}

//获取分页列表
/**
 *
 * @param config 必须是全局变量
 *例子
 * var config = {
        requestEnd:false,//固定项不可修改，必须填写
		loadTrigger:false,//固定项不可修改，必须填写
		currentPage:1,//固定项不可修改，必须填写
		url:module+'goods/getList', 必填填写项，
		type:true,//可选项 true:下拉分页 false:带页数分页
		callBack:callBack //可选项 成功回调函数
	};
 * @param postData 必须是全局变量
 * @returns {boolean}
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
            config.callBack(config,postData,data);
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
//
$('.databaseLayer .scroll-list-content').on('scroll',function(){
    var listHeight=$('.databaseLayer #listLayer').get(0).scrollHeight;
    if(config_2.loadTrigger && $('.databaseLayer .scroll-list-content').scrollTop()+$('.databaseLayer .scroll-list-content').height()>=listHeight){
        config_2.loadTrigger = false;
        getPagingList(config_2,postData_2);
    }
});
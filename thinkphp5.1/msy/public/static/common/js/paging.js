//获取分页列表-公共回调函数
function getPagingListCallBack(config,data){
    $('.loading').hide();
    if(config.currentPage == 1){
        config.container.find('li').remove();
        config.container.append(data);
    }else{
        config.container.find('li:last').after(data);
    }
}

/**获取分页列表
 * @param config  下拉分页配置 必须是全局变量
 *例子
 * var config = {
        requestEnd:false,//如type为true  就是固定项不可修改，必须填写  type：为false或不传 不需要填写
		loadTrigger:false,//如type为true  就是固定项不可修改，必须填写  type：为false或不传 不需要填写
		currentPage:1,//如type为true  就是固定项不可修改，必须填写  type：为false或不传 不需要填写
		url:module+'goods/getList', 非必填填写项，默认为当前方法
		callBack:callBack //可选项 成功回调函数
	};
 * @param postData 提交数据 必须是全局变量
 */
function getPagingList(config,postData) {
    //容器
    config.container = config.container?config.container:$("#list");
    //提交路径
    config.url = config.url?config.url:action;
    //回调函数名
    config.callBack = config.callBack?config.callBack:getPagingListCallBack;
    //要提交的数据
    postData = postData?postData:$('#form1').serializeObject();
    postData.page = postData.currentPage ? postData.currentPage : config.currentPage;
    postData.pageSize = postData.pageSize ? postData.pageSize:4;
    //请求结束标志
    if(config.requestEnd){
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
            if($($.parseHTML(data)).length<postData.pageSize){
                config.requestEnd = true;
            }
            config.currentPage ++;
            config.loadTrigger = true;
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

//禁用移动按钮
function disableBtn(){
    var listUl = $('#list');
    listUl.find('li').find('.move-btn').removeProp('disabled');
    listUl.find('li:first').find('.up-btn').prop('disabled','disabled').addClass('disabled');
    listUl.find('li:last').find('.down-btn').prop('disabled','disabled').addClass('disabled');
}

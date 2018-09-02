/**
 * Created by Administrator on 2018/4/25.
 */
//上拉加载更多
var loadTrigger = false;//加载触发器
function getMore(url,config) {
    $('.scroller-container').on('scroll',function(){
        var listHeight=$('.scroller-container').get(0).scrollHeight;
        if(loadTrigger &&  $('.scroller-container').scrollTop()+ $('.scroller-container').height()>=listHeight){
            loadTrigger = false;
            getPage(url,config);
        }
    });
}

//弹窗上拉加载更多
var loadTriggerLayer = false;//加载触发器
function getMoreLayer(url,config) {
    $('.databaseLayer .scroll-list-content').on('scroll',function(){
        var listHeight=$('.databaseLayer #listLayer').get(0).scrollHeight;
        if(loadTriggerLayer && $('.databaseLayer .scroll-list-content').scrollTop()+$('.databaseLayer .scroll-list-content').height()>=listHeight){
            loadTriggerLayer = false;
            getPageLayer(url,config);
        }
    });
}

//获取分页列表-商品页回调函数
function goodsPullDownPagingCallBack(){
    $('.loading').hide();
    if(currentPage == 1){
        $('#list li').remove();
        $('#list').append(data);
    }else{
        $('#list li:last').after(data);
    }
    if($($.parseHTML(data)).length<_postData.pageSize){
        requestEnd = true;
    }
    currentPage ++;
    loadTrigger = true;
    disableBtn();
}

//获取列表
var currentPage = 1;//记录当前页
var requestEnd = false;//请求结束标记
function getPage(url,config) {
    var postData = $.extend({},config);
    postData.page = currentPage ? currentPage : 1;
    postData.pageSize = postData.pageSize?postData.pageSize:4;
    //请求结束标志
    if(requestEnd){
        dialog.error('没有更多啦');
        loadTrigger = true;
        return false;
    }
    $.ajax({
        url: url,
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
            if(currentPage == 1){
                $('#list li').remove();
                $('#list').append(data);
            }else{
                $('#list li:last').after(data);
            }
            if($($.parseHTML(data)).length<postData.pageSize){
                requestEnd = true;
            }
            currentPage ++;
            loadTrigger = true;
            disableBtn();
        }
    });
}

var currentPageLayer = 1;//记录当前页
var requestEndLayer = false;//请求结束标记
function getPageLayer(url,config) {
    var postData = $.extend({},config);
    postData.page = currentPageLayer ? currentPageLayer : 1;
    postData.pageSize = postData.pageSize?postData.pageSize:4;
    //请求结束标志
    if(requestEndLayer){
        errorTipc('没有更多啦');
        loadTriggerLayer  = true;
        return false;
    }
    $.ajax({
        url: url,
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
            if(currentPageLayer == 1){
                $('.databaseLayer #listLayer li').remove();
                $('.databaseLayer #listLayer').append(data);
            }else{
                $('.databaseLayer #listLayer li:last').after(data);
            }
            if($($.parseHTML(data)).length<postData.pageSize){
                requestEndLayer = true;
            }
            currentPageLayer ++;
            loadTriggerLayer = true;
        }
    });
}

//禁用移动按钮
function disableBtn(){
    var listUl = $('#list');
    listUl.find('li').find('.move-btn').removeProp('disabled');
    listUl.find('li:first').find('.up-btn').prop('disabled','disabled').addClass('disabled');
    listUl.find('li:last').find('.down-btn').prop('disabled','disabled').addClass('disabled');
}
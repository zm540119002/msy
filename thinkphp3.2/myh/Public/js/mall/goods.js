var currentPage = 1;//记录当前页
var requestEnd = false;//请求结束标记
//获取商品-列表
function getGoodsList(config) {
    var postData = $.extend({},config);
    postData.p = currentPage?currentPage:1;
    postData.pageSize = postData.pageSize?postData.pageSize:2;
    postData.templateType = postData.templateType?postData.templateType:'list';
    //请求结束标志
    if(requestEnd){
        dialog.error('没有更多啦');
        loadTrigger = true;
        return false;
    }
    $.ajax({
        url: MODULE + '/Goods/goodsList',
        data: postData,
        type: 'get',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function (xhr) {
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(currentPage == 1){ 
                $('ul.goodsListContent').append(data);
            }else{
                $('ul.goodsListContent li:last').after(data);
            }
            if($(data).length<postData.pageSize){
                requestEnd = true;
            }
            currentPage ++;
            loadTrigger = true;
        }
    });
}

//上拉加载更多
var loadTrigger = false;//加载触发器
$(window).on('scroll',function(){
    if(loadTrigger && $(document).scrollTop()+$(window).height()>=$(document).height()){
        loadTrigger = false;
        getGoodsList(config);
    }
});




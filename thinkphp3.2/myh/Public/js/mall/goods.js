var currentPage = 1;//记录当前页
var requestEnd = false;
//获取分类商品-图形形式-列表
function getGoodsList(config) {
    var postData = $.extend({},config);
    postData.p = currentPage?currentPage:1;
    postData.pageSize = postData.pageSize?postData.pageSize:2;
    postData.templateType = postData.templateType?postData.templateType:'list';
    //请求结束标志
    if(requestEnd){
        dialog.error('没有更多啦');
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
                $('ul.goodsListContent').empty().append(data);
            }else{
                $('ul.goodsListContent li:last').after(data);
            }
            if($(data).length<postData.pageSize){
                requestEnd = true;
            }
            currentPage ++;
        }
    });
}
//上拉加载更多
// $(window).on('scroll',function(){
//     if($(document).scrollTop()+$(window).height()>=$(document).height()){
//         getGoodsList(config);
//     }
// });
var loadMoreWorking = false;
var hasMore = true;
var lastTop = 0;
document.addEventListener('scroll', function (e) {
    var clientHeight = document.documentElement.clientHeight;
    var scrollHeight = document.documentElement.scrollHeight;
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    console.log(scrollTop);
    console.log(scrollHeight);
    console.log(clientHeight);
    if (lastTop < scrollTop && scrollTop > scrollHeight - clientHeight - 5
        || scrollTop == scrollHeight - clientHeight
    ) {
        hasMore && getGoodsList(config);
    }

    lastTop = scrollTop;
});

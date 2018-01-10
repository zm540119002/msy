var currentPage = 1;//记录当前页
var requestEnd = false;
//获取分类商品-图形形式-列表
function getGoodsList(config) {
    console.log(config);
    console.log(currentPage);
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
                $('ul.goodsListContent').append(data);
                console.log('第一次');
            }else{
                $('ul.goodsListContent li:last').after(data);
                console.log('第二次');
            }
            isMore=true;
            if($(data).length<postData.pageSize){
                requestEnd = true;
            }
            currentPage ++;
           
        }
    });
}
//上拉加载更多
var isMore=false;
$(window).on('scroll',function(){
    if(isMore && $(document).scrollTop()+$(window).height()>=$(document).height()){
         console.log('滚动开始');
         isMore=false;
        getGoodsList(config);
    }
});

var currentPage = 1;//记录当前页
var requestEnd = false;
var finished=true;
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
            finished=true;
            
            $('.loading').hide();
            if(currentPage == 1){
                $('ul.goodsListContent').empty().append(data);
            }else{
                $('ul.goodsListContent li:last').after(data);
               
            }
            console.log($(data).length);
            if($(data).length<postData.pageSize){
                requestEnd = true;
                finished=true;
            }
            currentPage ++;
        }
    });
     
}
 var config = {
    pageSize:4,
    buyType:2,
    templateType:'photo'
};
//初始化
getGoodsList(config);
//上拉加载更多

// function loadGoods(config){
$(window).on('scroll',function(){
    console.log(finished);
    if(finished && $(document).scrollTop()+$(window).height()>=$(document).height()){
        finished=false;
        getGoodsList(config); 
    }
});
// }
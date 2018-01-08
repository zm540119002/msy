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
//         // alert(1);
//         getGoodsList(config);
//     }
// });

var ticking = false; // rAF 触发锁
 
function onScroll(){
  if(!ticking) {
    requestAnimationFrame(realFunc);
    ticking = true;
  }
}
 
function realFunc(){
	// do something...
 if($(document).scrollTop()+$(window).height()>=$(document).height()){
        // alert(1);
        getGoodsList(config);
    }
	console.log("Success");
	ticking = false;
}
// 滚动事件监听
window.addEventListener('scroll', onScroll, false);

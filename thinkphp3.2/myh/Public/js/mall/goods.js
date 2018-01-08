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
//         alert(1);
//         // getGoodsList(config);
//     }
// });

var last_known_scroll_position = 0;
var ticking = false;

function doSomething(scroll_pos) {
  // do something with the scroll position
  console.log(scroll_pos+$(window).height());
  console.log($(document).height());
  if(scroll_pos+$(window).height()>=$(document).height()){
    getGoodsList(config);
  }
}

window.addEventListener('scroll', function(e) {
    console.log(window.scrollY);
  last_known_scroll_position = window.scrollY;
  if (!ticking) {
        window.requestAnimationFrame(function() {
       doSomething(last_known_scroll_position);
        ticking = false;
        });
  }
  ticking = true;
});
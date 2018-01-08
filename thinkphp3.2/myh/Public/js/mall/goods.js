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

// 简单的节流函数
function throttle(func, wait, mustRun) {
	var timeout,
		startTime = new Date();
 
	return function() {
		var context = this,
			args = arguments,
			curTime = new Date();
 
		clearTimeout(timeout);
		// 如果达到了规定的触发时间间隔，触发 handler
		if(curTime - startTime >= mustRun){
			func.apply(context,args);
			startTime = curTime;
		// 没达到触发间隔，重新设定定时器
		}else{
			timeout = setTimeout(func, wait);
		}
	};
};
// 实际想绑定在 scroll 事件上的 handler
function realFunc(){
	console.log("Success");
     if($(document).scrollTop()+$(window).height()>=$(document).height()){
        // alert(1);
        getGoodsList(config);
    }
}
// 采用了节流函数
window.addEventListener('scroll',throttle(realFunc,500,1000));

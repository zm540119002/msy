/**
 * Created by Administrator on 2018/4/25.
 */
$(function () {
    var config = {
        pageSize:4,
        pageType:'manage',
    };
    var url = module+'goods/getList';
    getPage(url,config);
    //上下架
    $('body').on('click','.shelf',function(){
        var shelfStatus=$(this).data('shelf');
        var _this = $(this);
        var storeType=$('.channel_nav a.current').data('type');
        var goodsId = $(this).parents('li').data('id');
        var postData ={};
        postData.goodsId = goodsId;
        postData.shelf_status = shelfStatus;
        postData.storeType = storeType;
        var url = module+'goods/setShelf';
        $.ajax({
            url: url,
            data: postData,
            type: 'post',
            dataType:'json',
            beforeSend: function(){
            },
            error:function (xhr) {
                dialog.error('AJAX错误');
            },
            success: function(data){
                if(data.status == 1){
                    if(shelfStatus==2){
                        _this.removeClass('shelf').addClass('apply-shelf').text('已申请');
                    }else{
                        _this.data('shelf',2);
                        _this.addClass('up-shelf').text('上架');
                    }
                }
            }
        });
    })
});

// //上拉加载更多
// var loadTrigger = false;//加载触发器
// $(window).on('scroll',function(){
//     if(loadTrigger && $(document).scrollTop()+$(window).height()>=$(document).height()){
//         loadTrigger = false;
//         var config = {
//             pageSize:4,
//             pageType:'manage',
//         };
//         var url = module+'goods/getList';
//         getPage(url,config);
//     }
// });
//
// //获取列表
// var currentPage = 1;//记录当前页
// var requestEnd = false;//请求结束标记
// function getPage(url,config) {
//     var postData = $.extend({},config);
//     postData.page = currentPage ? currentPage : 1;
//     postData.pageSize = postData.pageSize?postData.pageSize:4;
//     //请求结束标志
//     if(requestEnd){
//         dialog.error('没有更多啦');
//         loadTrigger = true;
//         return false;
//     }
//     $.ajax({
//         url: url,
//         data: postData,
//         type: 'get',
//         dataType:'json',
//         beforeSend: function(){
//             $('.loading').show();
//         },
//         error:function (xhr) {
//             $('.loading').hide();
//             dialog.error('AJAX错误');
//         },
//         success: function(data){
//             $('.loading').hide();
//             if(currentPage == 1){
//                 $('#list li').remove();
//                 $('#list').append(data);
//             }else{
//                 $('#list li:last').after(data);
//             }
//             if($($.parseHTML(data)).length<postData.pageSize){
//                 requestEnd = true;
//             }
//             currentPage ++;
//             loadTrigger = true;
//         }
//     });
// }

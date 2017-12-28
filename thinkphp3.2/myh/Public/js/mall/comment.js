var currentPage = 1;//记录当前页
var requestEnd = false;
//获取分类商品-图形形式-列表
function getCommentList(config) {
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
        url: MODULE + '/Comment/commentList',
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
            console.log(data);
            if(currentPage == 1){

            }else{
              
            }
            if($(data).length<postData.pageSize){
                requestEnd = true;
            }
            currentPage ++;
        }
    });
}
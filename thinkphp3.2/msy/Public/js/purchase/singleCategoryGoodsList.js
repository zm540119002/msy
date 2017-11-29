//获取单分类下商品-列表形式
var currentPage = 1;//记录当前页
var requestEnd = false;//请求结束标志
function getSingleCategoryList(config) {
    var url = MODULE + '/Goods/goodsList';
    var postData = {};
    postData.p = currentPage?currentPage:1;
    postData.pageSize = config.pageSize?config.pageSize:2;
    postData.category_id_1 = config.category_id_1;
    postData.category_id_2 = config.category_id_2;
    postData.template_type = 'list';
    //判断请求是否结束
    if(requestEnd){
        dialog.error('没有更多啦');
        return false;
    }
    $.ajax({
        url: url,
        data: postData,
        type: 'get',
        beforeSend: function(){
            $('#content').append($('#loading').html());
        },
        success: function(data){
            $('.loading').remove();
            if($(data).length<postData.pageSize){
                requestEnd = true;
            }
            if(currentPage == 1){
                $('#content').empty().html(data);
            }else{
                $('#content').append(data);
            }
            currentPage++;
        }
    });
}
//获取分类商品-图形形式-页面
function getPage(config) {
    var postData = {};
    postData.p = config.currentPage ? config.currentPage : 1;
    postData.pageSize = 2;
    postData.category_id_1 = config.category_id_1;
    $.ajax({
        url: config.url,
        data: postData,
        type: 'get',
        beforeSend: function(){
            $('#content').append($('#loading').html());
        },
        success: function(data){
            $('#content').html(data);
        }
    });
}

//获取分类商品-图形形式-列表
function getPhotoList(obj,url) {
    var postData = {};
    postData.p = obj.data('page')+1;
    postData.category_id_2 = obj.data('category_id_2');
    postData.pageSize = 2;
    postData.template_type = 'photo';
    //请求结束标志
    if(obj.data('request_end')){
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
            obj.parents('.category_1_goods').find('ul').append(data);
            if($(data).length<postData.pageSize){
                obj.data('request_end',1)
            }
            obj.parents('.category_1_goods').find('.view_more').data('page',postData.p);
        }
    });
}
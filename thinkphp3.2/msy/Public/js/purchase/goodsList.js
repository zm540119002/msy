//获取多分类下商品-列表形式
function getMultiCategoryList(obj,opt_type) {
    var url = CONTROLLER+'/goodsList';
    var postData = {};
    postData.p = obj.data('current_page');
    postData.category_id_2 = obj.data('category_id_2');
    postData.pageSize = 2;
    postData.template_type = 'list';
    //判断请求是否结束
    if(obj.data('request_end')){
        dialog.error('没有更多啦');
        return false;
    }
    opt_type = opt_type?opt_type:'switch';
    $.ajax({
        url: url,
        data: postData,
        type: 'get',
        beforeSend: function(){
            $('#content').append($('#loading').html());
        },
        success: function(data){
            $('.loading').remove();
            if(postData.p==1){
                obj.data('current_page',postData.p+1);
            }
            if(opt_type == 'pull_up'){
                if(postData.p!=1){
                    obj.data('current_page',postData.p+1);
                }
                if($(data).length<postData.pageSize){
                    obj.data('request_end',1);
                }
                $('#content').append(data);
            }else{
                if($(data).length == 0){
                    $('#content').empty();
                }else{
                    $('#content').html(data);
                }
            }
        }
    });
}
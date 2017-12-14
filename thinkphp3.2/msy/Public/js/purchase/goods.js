//获取多分类下商品-列表形式
function getMultiCategoryGoodsList(obj,opt_type) {
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

//获取单分类下商品-列表形式
var currentPage = 1;//记录当前页
var requestEnd = false;//请求结束标志
function getSingleCategoryGoodsList(config) {
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
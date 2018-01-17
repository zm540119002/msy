$(function () {
    //购物车.加减
    $('body').on('click','.gplus,.greduce',function(){
        replaceOneGoodsToCart($(this));
    });
    //购买数量.失去焦点
    $('body').on('blur','.gshopping_count',function(){
        replaceOneGoodsToCart($(this));
    });
    //购物车删除
    $('body').on('click','.detele_cart',function(){
        var _li = $(this).parents('li');
        var postData = {};
        var foreign_ids=[];
        foreign_ids.push(_li.data('id'));
        postData.foreign_ids = foreign_ids;
        delCart(postData);
    });
    $('body').on('click','.detele_carts',function(){
        var postData = {};
        var foreign_ids = [];
        $.each($('.purchase_package_list li'),function(){
            var _this=$(this);
            var foreign_id = _this.data('id');
            foreign_ids.push(foreign_id);
        });
        postData.foreign_ids = foreign_ids;
        delCart(postData);
    });
});
//从购物车里替换单个商品信息
function replaceOneGoodsToCart(obj) {
    var _li = obj.parents('li');
    var postData = {};
    postData.foreign_id = _li.data('id');
    postData.num = _li.find('.gshopping_count').val();
    var url = CONTROLLER + '/replaceOneGoodsToCart';
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(data.status==0){
                dialog.error(data.info);
            }else {
                dialog.success(data.info);
            }
        }
    });
}

function delCart(postData) {
    var url = CONTROLLER + '/delCart';
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(data.status==0){
                dialog.error(data.info);
            }else {
                dialog.success(data.info);
            }
        }
    });

}



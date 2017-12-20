$(function () {
    //加减
    $('body').on('click','.gplus,.greduce',function(){
        replaceOneGoodsToCart($(this));
    });
    //失去焦点
    $('body').on('blur','.gshopping_count',function(){
        replaceOneGoodsToCart($(this));
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
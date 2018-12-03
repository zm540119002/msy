$(function(){        
    //加
    $('body').on('click','.gplus',function(){
        //单个商品数量自加
        goodsNumPlus($(this));
        //计算商品列表总价
        calculateTotalPrice();
        
    });
    //减
    $('body').on('click','.greduce',function(){
        //单个商品数量自减
        goodsNumReduce($(this));
        //计算商品列表总价
        calculateTotalPrice();
    });

});
//单个商品数量自加
function goodsNumPlus(obj) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    _li.find('.gshopping_count').val(++num);
}

//单个商品数量自减
function goodsNumReduce(obj) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    if(num<2){
        return false;
    }
    _li.find('.gshopping_count').val(--num);
}   

//计算商品列表总价
function calculateTotalPrice(){
    var _thisLis = $('ul.goods_list').find('li');
    if(!$('footer').find('price').length){
        return false;
    }
    var isInt = true;
    var amount = 0;
    $.each(_thisLis,function(){
        var _thisLi = $(this);
        var num = _thisLi.find('.gshopping_count').val();
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        amount += _thisLi.find('price').text() * num;
    });
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
    $('footer').find('price').html(amount.toFixed(2));
}
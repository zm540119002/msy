$(function () {
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

    //失去焦点
    $('body').on('blur','.gshopping_count',function(){
        //计算商品列表总价
        calculateTotalPrice();
    });

    //进入采购(验证机构认证)
    $('body').on('click','.go_to_purchase',function(){
        var url = MODULE + '/Common/checkCompanyAuthorise?returnUrl=' + encodeURIComponent(location.href);
        location.href = url;
    });

    //立即结算
    $('body').on('click','.buy_now',function(){
        var postData = assemblyData();
        var url = MODULE + '/Order/generate';
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
                if(data.status == 0){
                    if(data.url){
                        location.href = data.url;
                    }else{
                        dialog.error(data.info);
                    }
                }else if(data.status == 1){
                    if(data.info=='isAjax'){
                        loginDialog();
                    }else{
                        location.href = MODULE + '/Order/settlement/orderId/' + data.id;
                    }
                }
            }
        });
    });

    //加入购物车
    $('body').on('click','.add_cart',function(){
        var postData = assemblyData();
        if(!postData.goodsList.length){
            dialog.error('请选择商品');
            return false;
        }
        var url = MODULE + '/Cart/addGoodsToCart';
        $.ajax({
            url: url,
            data: postData,
            type: 'post',
            beforeSend: function(){$('.loading').show();},
            error:function(){$('.loading').hide();dialog.error('AJAX错误');},
            success: function(data){
                $('.loading').hide();
                if(data.status==0){
                    dialog.error(data.info);
                }else {
                    dialog.success(data.info);
                }
            }
        });
    });
});

//组装数据
function assemblyData() {
    var lis = $('ul.goods_list').find('li');
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    $.each(lis,function(){
        var _this = $(this);
        var num = _this.find('.gshopping_count').val();
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        if(parseInt(num)){
            var tmp = {};
            tmp.foreign_id = _this.data('id');
            tmp.num = num;
            postData.goodsList.push(tmp);
        }
    });
    if(!isInt){
        dialog.error('请输入正整数');
        return false;
    }
    return postData;
}

//计算商品列表总价
function calculateTotalPrice(){
    var _thisLi = $('ul.goods_list').find('li');
    var num = _thisLi.find('.gshopping_count').val();
    if(!isPosIntNumberOrZero(num)){
        dialog.error('请输入正整数');
        return false;
    }
    var amount = 0.00;
    amount += _thisLi.find('span.purchase_price').find('price').text() * num;
    $('footer').find('price').html(amount.toFixed(2));
}

//单个商品数量自减
function goodsNumReduce(obj) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    if(num<1){
        return false;
    }
    _li.find('.gshopping_count').val(--num);
}

//单个商品数量自加
function goodsNumPlus(obj) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    _li.find('.gshopping_count').val(++num);
}
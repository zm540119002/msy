$(function(){
    //初始化总价
    TotalPrice();
    //购物车全选
    $('.allCheck').on('click',function(){
        if($(this).is(':checked')){
            $.each($('.shopping_cart_list li'),function(){
                $('.single_select').prop('checked',true);
            });
            TotalPrice();
        }else{
            $.each($('.shopping_cart_list li'),function(){
                $('.single_select').prop('checked',false);
            });
            TotalPrice();
        }
    });
    //选择商品
    $('.single_select').on('click',function(){
        var goods=$(this).parents('.shopping_cart_list').find('.single_select');
        var goodsSelect=$(this).parents('.shopping_cart_list').find('.single_select:checked');
        if (goods.length == goodsSelect.length) { //如果店铺被选中的数量等于所有店铺的数量
            $(".allCheck").prop('checked', true); //全选按钮被选中
            TotalPrice();
        } else {
            $(".allCheck").prop('checked', false); //else全选按钮不被选中
            TotalPrice();
        }
    });
    //购物车更改商品数量
    $('.gshopping_count').on('blur',function(){
        var _li =  $(this).parents('li');
        var singleChecked = _li.find('.single_select').is(':checked');
        if(singleChecked){//如果选择了，更改总价
            TotalPrice();
        }
        var postData = assemblyCartData(_li);
        var num =$(this).val();
        postData.num = num;
        replaceOneGoodsToCart(postData);
    });
    //购物车商品数量加减
    // var shopNum = $('.shopping_count').val();
    $('body').on('click','.plus',function(){
        var _li =  $(this).parents('li');
        var num =$(this).siblings('.gshopping_count').val();
        num++;
        $(this).siblings('.gshopping_count').val(num);
        var singleChecked = _li.find('.single_select').is(':checked');
        if(singleChecked){//如果选择了，更改总价
            TotalPrice();
        }
        var postData = assemblyCartData(_li);
        postData.num = num;
        replaceOneGoodsToCart(postData);
    });

    $('body').on('click','.reduce',function(){
        var _li =  $(this).parents('li');
        var num =$(this).siblings('.gshopping_count').val();
        num--;
        if(num < 1){
            return false;
        }
        $(this).siblings('.gshopping_count').val(num);
        var singleChecked = _li.find('.single_select').is(':checked');
        if(singleChecked){//如果选择了，更改总价
            TotalPrice();
        }
        var postData = assemblyCartData(_li);
        postData.num = num;
        replaceOneGoodsToCart(postData);
    });

    //购物车单个删除
    $('body').on('click','.edit_delete',function(){
        var _li =  $(this).parents('li');
        var data = assemblyCartData(_li);
        var tmp = [];
        tmp.push(data);
        var postData = {
           goodsList:tmp
        };
        var type = 'single';
        delCart(postData,type,$(this));
    });
    //选择删除购物车
    // $('body').on('click','.edit_delete',function(){
    //     var postData = {};
    //     var foreign_ids = [];
    //     $.each($('.purchase_package_list li'),function(){
    //         var _this=$(this);
    //         if(_this.find('.sigle_checkbox').is(':checked')){
    //             var foreign_id = _this.data('id');
    //             foreign_ids.push(foreign_id);
    //         }
    //     });
    //     postData.foreign_ids = foreign_ids;
    //     var type = 'more';
    //     delCart(postData,type,$('.purchase_package_list li'));
    // });
})

//选择或当个删除购物车
function delCart(postData,type,obj) {
    var url = CONTROLLER + '/delCart';
    layer.open({
        content:'是否删除？',
        btn:['确定','取消'],
        yes:function(index){
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
                        if(type == 'single'){
                            obj.parents('li').remove();
                        }
                        if(type == 'more'){
                            $.each(obj,function(){
                                var _this=$(this);
                                var cartId = _this.data('id');
                                for(var i=0;i<postData.foreign_ids.length;i++){
                                    if(cartId == postData.foreign_ids[i]){
                                        _this.remove();
                                    }
                                }
                            });
                        }
                        if( $('.purchase_package_list li').length == 0){
                            $('.select_checkbox_box').hide();
                            $('#no_data').show();
                        }else{
                            $('.select_checkbox_box').show();
                        }
                        dialog.success(data.info);
                    }
                }
            });
            layer.close(index);
        }
    });
}
//加减购物车单个的商品数量
function replaceOneGoodsToCart(data) {
    var url = MODULE + '/Cart/replaceOneGoodsToCart';
    $.post(url,data, function (msg) {

    })
}
//计算总价
function TotalPrice(){
    var allPrice=0;
    $.each($('.shopping_cart_list li'),function(){
        var _this=$(this);
        var singleChecked = _this.find('.single_select').is(':checked');
        if(singleChecked){
            var singleNum = parseInt(_this.find('.gshopping_count').val());
            var goodsPrice=_this.find('.shop_product_price').text().replace(/[^\d.]/g,"");
            var total=singleNum*goodsPrice;
            allPrice +=total;
        }
    });
    allPrice = parseFloat(allPrice).toFixed(2);
    $('.goods_total_price price').text(allPrice);
}

//组装购物车数据
function assemblyCartData(li) {
    var isInt = true;
    var id = li.data('id');
    var goods_type = li.data('goods_type');
    if(id){
        var postData = {};
        postData.foreign_id = id;
        postData.goods_type = goods_type;
    }
    if(postData && postData.length == 0){
        dialog.error('请选择商品');
        return false;
    }
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
    if(!postData){
        return false;
    }
    if (postData.goodsList && postData.goodsList.length == 0) {
        dialog.error('请选择商品');
        return false;
    }
    if (!isInt) {
        dialog.error('购买数量为正整数');
        return false;
    }
    return postData;
}

$(function(){
    //加入购物车
    $('body').on('click','.shopping_cart',function() {
        var _this = $(this);
        var _thisLi = _this.parents('li');
        var goodsId = _thisLi.data('goodsid');
        var projectId = _thisLi.data('projectid');
        var buyType = _thisLi.data('buytype');
        var html='';
        $.post(MODULE + '/Cart/addCart', {goodsId:goodsId,projectId:projectId,buyType:buyType}, function (msg) {
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if (msg.status == 1) {
                html+='<li data-foreignid='+msg.info.info.id+' data-buytype='+msg.info.info.buy_type+'>';
                html+='<span class="shop_product_name">'+msg.info.info.name+'</span>';
                html+='<span class="shop_product_price">'+msg.info.info.real_price+'</span>';
                html+='<div class="shop_product_num">';
                html+='<a href="javascript:void(0);" class="reduce">-</a>';
                html+='<input type="text" value='+msg.info.info.num+' class="shopping_count">';
                html+='<a href="javascript:void(0);" class="plus">+</a>';
                html+='</div>';
                html+='</li>';
                if(!$('.cart').find('li').length){
                    $('.cart ul').append(html);
                }else{
                    var sign = 1;
                    $('.cart').find('li').each(function(){
                        var buyType = $(this).data('buytype');
                        var id = $(this).data('foreignid');
                        var num = parseInt($(this).find('.shopping_count').val());
                        if(buyType == msg.info.info.buy_type && id == msg.info.info.id){
                            $(this).find('.shopping_count').val(parseInt(msg.info.info.num)+num);
                            sign = 0;
                        }
                    });
                    if(sign){
                        $('.cart').find('li:last').after(html);
                    }
                }
                $('.commodity_quantity').text(msg.info.count);
                $('.total_price').text('￥' + msg.info.total);
                $('.cartIds').val( msg.info.cartIds);
            }
        })

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

    //编辑商品
    $('.edit_goods').on('click',function(){
        $(this).hide();
        $(this).siblings('.edit_goods_type').show();
    });
    $('.edit_finish').on('click',function(){
        $(this).parents('.edit_goods_type').hide();
        $(this).parents('.edit_goods_type').siblings('.edit_goods').show();
    });

    $('.shopping_count').on('input propertychange',function(){
        if(!$(this).val()){
            return false;
        }
        TotalPrice();

    });

    //更改商品数量
    $('.shopping_count').on('blur',function(){
        var _this = $(this);
        var _thisLi = _this.parents('li');
        var foreignId = _thisLi.data('foreignid');
        var buyType = _thisLi.data('buytype');
        var goodsNum = _this.val();

        $.post(MODULE + '/Cart/addCart', {foreignId: foreignId,goodsNum:goodsNum,buyType:buyType}, function (msg) {
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            $('.commodity_quantity').text(msg.info.count);
            $('.total_price').text('￥' + msg.info.total);
            $('.cartIds').val( msg.info.cartIds);
        })
    });

    //删除某条记录
    $('.edit_delete').on('click',function(){
        var _this=$(this);
        var _thisLi = _this.parents('li');
        layer.open({
            content:'确定要删除此购物商品？',
            btn:['确定','取消'],
            yes:function(index){
                var foreignId = _thisLi.data('foreignid');
                var buyType = _thisLi.data('buytype');
                var cartId = _thisLi.data('cartid');
                $.post(MODULE + '/Cart/delCart', {cartId: cartId,foreignId:foreignId,buyType:buyType}, function (msg) {
                    if(msg.status == 1){
                        _thisLi.remove();
                        dialog.success(msg.info);
                    }
                    if(msg.status == 0){
                        dialog.error(msg.info);
                    }
                });
                layer.close(index);
            }
        })

    });
    //登录清空购物车
    $('.empty_cart').on('click',function(){
        layer.open({
            content:'确定要清空购物车？',
            btn:['确定','取消'],
            yes:function(index){
                var cartIds = [];
                $.each($('.shopping_cart_list li'),function(){
                    var _this=$(this);
                    var cartId = _this.data('cartid');
                    cartIds.push(cartId);
                });
                $.post(MODULE + '/Cart/delCart', {cartIds: cartIds}, function (msg) {
                    if(msg.status == 1){
                        dialog.success(msg.info,'myCart');
                    }
                    if(msg.status == 0){
                        dialog.error(msg.info);
                    }
                });
                layer.close(index);
            }
        })
    });

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

    //商品数量加减

    var shopNum = $('.shopping_count').val();
    $('body').on('click','.plus',function(){
        shopNum = $(this).siblings('.shopping_count').val();
        shopNum++;
        $(this).siblings('.shopping_count').val(shopNum);
        TotalPrice();
        var foreignId = $(this).parents('li').data('foreignid');
        var buyType = $(this).parents('li').data('buytype');
        $.post(MODULE + '/Cart/addCart', {foreignId: foreignId,goodsNum:shopNum,buyType:buyType}, function (msg) {
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                $('.commodity_quantity').text(msg.info.count);
                $('.total_price').text('￥' + msg.info.total);
                $('.cartIds').val( msg.info.cartIds);
            }
        })

    });
    $('body').on('click','.reduce',function(){
        shopNum = $(this).siblings('.shopping_count').val();
        var foreignId = $(this).parents('li').data('foreignid');
        var buyType = $(this).parents('li').data('buytype');
        shopNum--;
        $(this).siblings('.shopping_count').val(shopNum);
        TotalPrice();
        if(shopNum==0){
            $(this).parents('li').remove();
            $.post(MODULE + '/Cart/delCart', {foreignId:foreignId,buyType:buyType}, function (msg) {
                if(msg.status == 1){
                    $(this).parents('li').remove();
                }
                if(msg.status == 0){
                    dialog.error(msg.info);
                }
            });
        }else{
            $.post(MODULE + '/Cart/addCart', {foreignId: foreignId,goodsNum:shopNum,buyType:buyType}, function (msg) {
                if(msg.status == 0){
                    dialog.error(msg.info);
                }
                if(msg.status == 1){
                    $('.commodity_quantity').text(msg.info.count);
                    $('.total_price').text('￥' + msg.info.total);
                    $('.cartIds').val( msg.info.cartIds);
                }
            })
        }

    });


    //计算总价
    function TotalPrice(){
        var allPrice=0;
        $.each($('.shopping_cart_list li'),function(){
            var _this=$(this);
            var goodsNum=parseInt(_this.find('.shopping_count').val());
            var goodsPrice=_this.find('.shop_product_price').text().replace(/[^\d.]/g,"");
            var total=goodsNum*goodsPrice;
            allPrice +=total;

        });
        allPrice = parseFloat(allPrice).toFixed(2);
        $('.total_price').text('￥'+allPrice);
    }

    //去结算
    $('body').on('click','.allPay',function () {
       var cartIds = $(this).parents().find('.cartIds').val();
        window.location.href = MODULE+'/Order/addOrder/cartIds/'+cartIds;
    })

    //去结算
    $('body').on('click','.cartPay',function () {
        var cartIds = [];
        $.each($('.shopping_cart_list li'),function(){
            var _this=$(this);
            if(_this.find('.single_select').is(":checked")){
                cartIds.push(_this.data('cartid'));
            }
        });
        if(cartIds.length==0){
            layer.open({
                content : '你还没有选中购买的商品！',
                icon:2,
                btn : ['知道了'],
                title : '提示',
            });
            return false;
        }
        window.location.href = MODULE+'/Order/addOrder/cartIds/'+cartIds;
    });
    //显示购物车
    // $('body').on('click','.shopping_cart',function(){
    //     $('.shopping_cart_nav').show().css({display:'flex'});
    // });


   
})


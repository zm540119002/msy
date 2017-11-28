$(function(){
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

    //购物车更改商品数量
    $('.cart_count').on('blur',function(){
        var _this = $(this);
        var _thisLi = _this.parents('li');
        var foreignId = _thisLi.data('foreignid');
        var type = _thisLi.data('type');
        var num = _this.val();

        $.post(MODULE + '/Cart/addCart', {foreignId:foreignId,num:num,type:type}, function (msg) {
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
                var cartId = _thisLi.data('cartid');
                var type = _thisLi.data('type');
                $.post(MODULE + '/Cart/delCart', {cartId: cartId,foreignId:foreignId,type:type}, function (msg) {
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
                        $('.shopping_cart_list >ul>li').remove();
                        $('.mask').hide();
                        TotalPrice();
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

    //购物车商品数量加减
    // var shopNum = $('.shopping_count').val();
    $('body').on('click','.plus',function(){
        shopNum = $(this).siblings('.shopping_count').val();
        shopNum++;
        $(this).siblings('.shopping_count').val(shopNum);
        //TotalPrice();
        var foreignId = $(this).parents('li').data('foreignid');
        var type = $(this).parents('li').data('type');
        $.post(MODULE + '/Cart/addCart', {foreignId:foreignId,num:shopNum,type:type}, function (msg) {
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
        if(shopNum==1){
            return false;
        }
        shopNum--;
        $(this).siblings('.shopping_count').val(shopNum);

        //TotalPrice();
        var foreignId = $(this).parents('li').data('foreignid');
        var type = $(this).parents('li').data('type');
        $.post(MODULE + '/Cart/addCart', {foreignId:foreignId,num:shopNum,type:type}, function (msg) {
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

    //产品详情的加减
    $('body').on('click','.gplus',function(){
         shopNum = parseInt($(this).siblings('.gshopping_count').val());
        var foreignId = $(this).parents('li').data('foreignid');
        var buyType = $(this).parents('li').data('buytype');
        var type = $(this).parents('li').data('type');
        var cartId =$(this).parents('li').data('cartid');
        shopNum++;
        $(this).siblings('.gshopping_count').val(shopNum);
        allAmount(shopNum);
    });

    $('body').on('click','.greduce',function(){
        shopNum = parseInt($(this).siblings('.gshopping_count').val());
        var foreignId = $(this).parents('li').data('foreignid');
        var buyType = $(this).parents('li').data('buytype');
        var type = $(this).parents('li').data('type');
        var cartId =$(this).parents('li').data('cartid');
        shopNum--;
        if(shopNum<1){
            return false;
        }
        $(this).siblings('.gshopping_count').val(shopNum);
        allAmount(shopNum);
    });

    function allAmount(singleNum){
        var allAmount=0;
        // var singleNum=parseInt(_this.find('.shopping_count').val());
        var goodsPrice=parseFloat($('.purchase_gs_r price').text());
        allAmount=singleNum*goodsPrice;
        allAmount=parseFloat(allAmount).toFixed(2)
        $('.goods_total_price price').text(allAmount);
        return allAmount;
    }
    //计算总价
    function TotalPrice(){
        var allPrice=0;
        var goodsNum=0;

        $.each($('.shopping_cart_list li'),function(){
            var _this=$(this);
            var singleNum=parseInt(_this.find('.shopping_count').val());
            var goodsPrice=_this.find('.shop_product_price').text().replace(/[^\d.]/g,"");
            var total=singleNum*goodsPrice;
                goodsNum +=singleNum;
                allPrice +=total;
        });
        allPrice = parseFloat(allPrice).toFixed(2);
        $('.total_price').text('￥'+allPrice);
        $('.commodity_quantity').text(goodsNum);
        if(!goodsNum){
            $('html,body').css({"overflow":"auto"});
            $('.express-area-box').css({bottom:'-100%',display:'none'});
            $('.mask').hide();
            $(".shop_cart").addClass("disabled");
        }
    }

    //去结算
    $('body').on('click','.allPay',function () {
        var cartIds = '';
        $.each($('.shopping_cart_list li'),function(){
            cartIds += $(this).data('cartid') + ',';
        });
        window.location.href = MODULE+'/Order/addOrder/cartIds/'+cartIds;

    });

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
    $('body').on('click','.shopping_cart',function(){
        $('.shopping_cart_nav').show().css({display:'flex'});
    });
   //购物车弹窗详情
    function getPurchaseDetails(id,type,buyType,position) {
        if(type === 'goods'){
            var url = MODULE+'/Goods/getGoodsInfo';
        }
        $.ajax({
            url: url,
            data: {id:id},
            type: 'post',
            beforeSend: function(){
            },
            error:function(){
                dialog.error('AJAX错误');
            },
            success: function(data){
                if(position === 'list'){
                    $('.common_contents').after(data);
                }
                if(position === 'info'){
                   $('.msh_product_picture').after(data);
                }
            }
        });
        if(!$('.mask').data('show')){
            $('html,body').css({"height":"100%","overflow":"hidden"});//动态阻止body页面滚动
//                $('.mask').show().css({position:'fixed'});
            $('.mask').data('show',1);

            if(buyType==1){
                $('.weituangou_cart_nav').css({display:'flex',zIndex:21});
            }else{
                $('.group_cart_nav:first').css({display:'flex',zIndex:21});
            }
            $('.signShopping_nav').css({display:'flex',zIndex:22})
//                $('.express-area-box').css({bottom:0+'px',display:'block',position:'fixed'});
            if($('.shoppingCart_form ul').height()>420){
                $('.shoppingCart_form ul').css({"max-height":4.5+'rem'});
            }
        }else{
            $('html,body').css({"overflow":"auto"});
            $('.mask').hide();
            $('.express-area-box').css({bottom:'-100%',display:'none'});
            $('.mask').data('show',0);
            $('.group_cart_nav').hide();
        }
    }


    //列表购物车弹窗
    $('body').on('click','.shopping_cart',function(){
        var _li = $(this).parents('li');
        var _this=$(this);
        //先清空
        $('.gshopping_count').val(1);
        $('.goods_total_price price').text( _li.find('.price').text().replace(/[^\d.]/g,""));
        var type = _li.data('type');
        var id = _li.data('goodsid');
        var buyType = _this.data('weituan');
        var position = 'list';
        getPurchaseDetails(id,type,buyType,position);
    });
    //详情购物车弹窗
    $('body').on('click','.info_shopping_cart',function(){
        var type = $('.type').val();
        var id = $('.id').val();
        var buyType = 2;
        var position = 'info';
        getPurchaseDetails(id,type,buyType,position);
    });

    $('body').on('click','.mask,.closeBtn',function(){
        $('html,body').css({"overflow":"auto"});
        $('.mask,signShopping_nav,.group_cart_nav').hide();
        $('.goodsInfo_footer_nav').show();
        $('.express-area-box').css({bottom:'-100%',display:'none'});
        $('.mask').data('show',0);
    });

    //加入购物车
    $('body').on('click','.add_cart',function(){
        var type = $('.goods_list li').data('layer-type');
        var postData = {};
        var url =  MODULE + '/Cart/addCart';
        if(type === 'goods'){
            postData.goodsId = $('.goods_list li').data('layer-id');
        }
        if(type === 'project'){
            postData.projectId = $('.goods_list li').data('layer-id');
        }
        postData.num = $('.gshopping_count').val();
        console.log(postData);
        $.ajax({
            url: url,
            data: postData,
            type: 'post',
            beforeSend: function(){
            },
            error:function(){
                dialog.error('AJAX错误');
            },
            success: function(data){
                if(data.status==0){
                    dialog.error(data.info);
                }else {
                    dialog.error(data.info);
                }
            },
            complete:function(){
                $('.group_cart_nav,.mask').hide();
                $('.goodsInfo_footer_nav').show();
                $('.express-area-box').css({bottom:'-100%',display:'none'});
                $('html,body').css({"overflow":"auto"});
            }
        });
    });

    //立即购买
    $('body').on('click','.buy_now',function () {
        var type = $('.goods_list li').data('layer-type');
        var url =  MODULE + '/Order/addOrder/';
        if(type === 'goods'){
            var goodsId = $('.goods_list li').data('layer-id');
            var num = $('.gshopping_count').val();
            url += 'goodsId/'+goodsId+'/num/'+num ;
        }
        if(type === 'project'){
            var projectId = $('.goods_list li').data('layer-id');
            var num = $('.gshopping_count').val();
            url += 'projectId/'+projectId+'/num/'+num ;
        }
        location.href = url;
    });

})



$(function () {

    // 弹出支付方式
    $('body').on('click','.confirm_order',function(){
        var settlementMethod=$('.settlementMethod').html();
        layer.open({
            type: 1
            ,anim: 'up'
            ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 50%; padding:10px 0; border:none;',
            className:'settlementmethod bankTransferLayer',
            content: settlementMethod
        });
    });

    // 选择支付方式
    $('body').on('click','.settlementmethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });

    // 提交订单
    $('body').on('click','.settlement_btn',function () {
        _this = $(this);
        var consignee=$('.consigneeInfo input[name="layer_consignee"]').val();
        var mobile=$('.consigneeInfo input[name="layer_mobile"]').val();
        var province=$('.consigneeInfo input[name="province"]').val();
        var city=$('.consigneeInfo input[name="city"]').val();
        var area=$('.consigneeInfo input[name="area"]').val();
        var detail_address=$('.consigneeInfo input[name="layer_detail_address"]').val();
        var orderId  = $('.order_id').val();
        var orderSn  = $('.order_sn').val();
        var payCode = $('.pay_code').val();
        var addressId= $('.address_id').val();
        var orderArr =[];
        $.each($('.goods_order_item li'),function () {
            _this = $(this);
            var order_detail_id = _this.data('order_detail_id');
            var brand_id = _this.find('.brand_name').data('id');
            var brand_name = _this.find('.brand_name').text();
            orderArr.push({
                id:order_detail_id,
                brand_id:brand_id,
                brand_name:brand_name,
            });
        })
        if(!addressId){
            dialog.error('请选择收货地址');
            return false;
        }
        var postData ={
            order_id:orderId,
            order_sn:orderSn,
            pay_code:payCode,
            consignee:consignee,
            mobile:mobile,
            province:province,
            city:city,
            area:area,
            detail_address:detail_address,
            orderDetail:orderArr
        };
        _this.addClass("nodisabled");//防止重复提交
        var url = module + 'Order/confirmOrder';
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
                _this.removeClass("nodisabled");//删除防止重复提交
                $('.loading').hide();
                if(data.status){
                    location.href = data.info;

                }else{
                    dialog.error('结算提交失败!');
                }
            }
        });
    });

    //再次购买
    $('body').on('click','.purchase_again',function () {
        //再次购买跳转到提交订单位置
        var _This=$(this);
        var oLis=$(this).parents('li').find('.order_item');
        var postData={};
        var goodsList=[];
        $.each(oLis,function () {
            var _this=$(this);
            var goods_id=_this.data('id');
            var buy_type=_this.data('buy_type');
            var brand_id=_this.data('brand_id');
            var brand_name=_this.data('brand_name');
            var num=_this.find('span.num').text();
            goodsList.push({
                goods_id:goods_id,
                buy_type:buy_type,
                num:num,
                brand_id:brand_id,
                brand_name:brand_name,
            });
        });
        if($.isEmptyArray(goodsList)){
            dialog.error('数据错误');
            return false
        }
        postData.goodsList=goodsList;
        _This.addClass("disabled");//防止重复提交
        generateOrder(postData,_This);
    });

});


$(window).load(function() {
    var current_time = $('.current_time').val();//时间戳类型
    var current_time1 = $('.current_time1').val();//日期类型
    $.each($(".order_info_list"),function(k,val){
        var _this =$(this);
        var group_buy_overdue_time = _this.data('group_buy_overdue_time');
        var order_overdue_time =  _this.data('order_overdue_time');//时间戳类型
        var order_overdue_time1 =  _this.data('order_overdue_time1');//日期类型
        var goods_id =  _this.data('goods_id');
        var group_buy_id =  _this.data('group_buy_id');
        var logistics_status = _this.data('logistics_status');
        var orderid = _this.data('orderid');
        if(group_buy_overdue_time){
            //团购订单是否过期
            if((group_buy_overdue_time - current_time)>0){//
                _this.find('.invite_group_buy').on('click',function(){
                    location.href = MODULE + '/Goods/goodsDetail/goodsId/'
                        + goods_id+'/groupBuyId/'+ group_buy_id+'/shareType/groupBuy';
                });
            }else{//已过期
                _this.find('.invite_group_buy').text('本次团购已结束').addClass('group_buy_end');
                _this.find('.invite_group_buy').on('click',function(){
                    var url = MODULE + '/Goods/goodsDetail/goodsId/' + goods_id;
                    dialog.confirm('此次团购已结束，是否重新开团',url);
                });
            }
        }
        //待支付订单是否过期
        if(logistics_status == 1){
            //倒计时
            var countdownShow = _this.find('.count_down_box');
            if((order_overdue_time - current_time) > 0 && countdownShow.data('key')==1){
                var countdownShowId=(countdownShow.attr('id')).toString();
                addTimer(countdownShowId,order_overdue_time1,current_time1);
            }
        }
    });

    //去支付
    $('body').on('click','.order_pay_btn',function(){
        //计算商品列表总价
        var orderid = $(this).parents('.order_info_list').data('orderid');
        location.href = MODULE + '/Order/settlement/orderId/' + orderid;
    });
    //点击已取消按钮
    $('body').on('click','.order_cancle',function(){
        //计算商品列表总价
        var goods_id =  $(this).parents('.order_info_list').data('goods_id');
        if(goods_id){
            var url = MODULE + '/Goods/goodsDetail/goodsId/' + goods_id;
            dialog.confirm('重新开团',url)
        }else{
            dialog.error('普通订单可能存在多个商品，请重新购买！')
        }

    });
    //点击去分享按钮
    $('body').on('click','.invite_group_buy',function(){
        //计算商品列表总价
        var goods_id =  $(this).parents('.order_info_list').data('goods_id');
        var group_buy_id =  $(this).parents('.order_info_list').data('group_buy_id');
        location.href = MODULE + '/Goods/goodsDetail/goodsId/'
            + goods_id+'/groupBuyId/'+ group_buy_id+'/shareType/groupBuy';

    });
    //点击本次团购已结束按钮
    $('body').on('click','.group_buy_end',function(){
        var goods_id =  $(this).parents('.order_info_list').data('goods_id');
        var url = MODULE + '/Goods/goodsDetail/goodsId/' + goods_id;
        dialog.confirm('此次团购已结束，是否重新开团',url);
    });


})

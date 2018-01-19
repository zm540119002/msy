$(window).load(function() {
    var current_time = $('.current_time').val();
    var current_time1 = $('.current_time1').val();
    console.log(current_time1)
    $.each($(".order_info_list"),function(k,val){
        var _this =$(this);
        var group_buy_overdue_time = _this.data('group_buy_overdue_time');
        var order_overdue_time =  _this.data('order_overdue_time');
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
                    dialog.confirm('此次团购已结束，是否重新开团',url)
                });
            }
        }
        //待支付订单是否过期
        if(logistics_status == 1){
            if((order_overdue_time - current_time)>0){
                _this.find('.order_pay_btn').on('click',function(){
                    location.href = MODULE + '/Order/settlement/orderId/' + orderid;
                });
            }else{//已过期
                _this.find('.order_pay_btn').text('已取消');
                _this.find('.order_pay_btn').on('click',function(){
                    var url = MODULE + '/Goods/goodsDetail/goodsId/' + goods_id;
                    dialog.confirm('重新购买',url)
                });
            }
        }
    });
    //活动倒计时
    $.each($(".count_down_box"),function(k,val){

        var _this =$(this);
        var thisId=(_this.attr('id')).toString();
        var obj=_this.find('.count_down_box');
        var order_overdue_time =  _this.parents('.order_info_list').data('order_overdue_time');
        var order_overdue_time1 =  _this.parents('.order_info_list').data('order_overdue_time1');
        var group_buy_id =  _this.parents('.order_info_list').data('group_buy_id');
        var logistics_status = _this.parents('.order_info_list').data('logistics_status');
        if(logistics_status == 1) {
            addTimer(thisId,order_overdue_time1,current_time1);
        }
    });

})

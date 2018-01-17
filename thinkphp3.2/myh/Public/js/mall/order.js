$(window).load(function() {
    var current_time = "{$current_time}";
    $(".order_list").each(function(){
        var _this =$(this);
        var group_buy_overdue_time = _this.data('group_buy_overdue_time');
        var order_overdue_time =  _this.data('order_overdue_time');
        var goods_id =  _this.data('goods_id');
        var group_buy_id =  _this.data('group_buy_id');
        if(group_buy_overdue_time){
            if((group_buy_overdue_time - current_time)>0){
                _this.find('invite_group_buy').on('click',function(){
                    location.href = MODULE + '/Goods/goodsDetail/goodsId/'
                        + goods_id+'/groupBuyId/'+ group_buy_id+'/shareType/groupBuy';
                });
            }else{
                _this.find('invite_group_buy').text('本次团购已结束').addClass('group_buy_end');
                _this.find('invite_group_buy').on('click',function(){
                    var url = MODULE + '/Goods/goodsDetail/goodsId/' + goods_id;
                    dialog.confirm('此次团购已结束，是否重新开团',url)
                });
            }
        }
    });
});

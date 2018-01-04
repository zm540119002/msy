$(function () {
    //计算商品列表总价
    calculateTotalPrice();
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

    //购买数量.失去焦点
    $('body').on('blur','.gshopping_count',function(){
        //计算商品列表总价
        calculateTotalPrice();
    });

    //立即结算/立即购买
    $('body').on('click','.buy_now,.clearing_now',function(){
        var postData = assemblyData();
        if(!postData){
            return false;
        }
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
                        location.href = MODULE + '/Order/orderDetail/orderId/' + data.id;
                    }
                }
            }
        });
    });

    //加入购物车
    $('body').on('click','.add_cart',function(){
        var postData = assemblyData();
        if(!postData){
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

    //确认订单
    $('body').on('click','.determine_order',function(){
        var consigneeName=$('.consignee_name').text();
        var consigneePhone=$('.consignee_phone').text();
        var consigneeAddress=$('.consignee_address').text();
        var content='';
        if(!consigneeName || !isMobilePhone(consigneePhone) ||!consigneeAddress){
            content="请选择收货人地址";
        }
        var orderId = $('section.orderInfo').data('id');
        if(!orderId){
            content="请确定订单是否正确";
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var postData = {};
        postData.orderId = orderId;
        var url = MODULE + '/Order/confirmOrder';
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
                    location.href = MODULE + '/Order/settlement/orderId/' + data.id;
                }
            }
        });
    });

    //我的二维码
    $('body').on('click','.QR_codes',function(){
        var url = location.href;
        $.ajax({
            url: MODULE + '/Referrer/myQRCodesWithGoods',
            data: {url:url},
            type: 'post',
            beforeSend: function(){
                $('.loading').show();
            },
            error:function (xhr) {
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
                        loginDialog(flushPage);
                    }else{
                        $('.mask,.express-code-box').fadeIn();
                        $('.twitter_code_img img').attr('src','/Uploads/'+data.url);
                    }
                }
            }
        });
    });


    //关闭删除二维码
    $("#areaMask,.closeBtn").click(function() {
        var imgUrl = $('.twitter_code_img img').attr('src');
        $.ajax({
            url: MODULE + '/Referrer/delMyQRCodesWithGoods',
            data: {imgUrl:imgUrl},
            type: 'post',
            beforeSend: function(){
                $('.loading').show();
            },
            error:function (xhr) {
                $('.loading').hide();
                dialog.error('AJAX错误');
            },
            success: function(data){
                $('.loading').hide();
                if(data.status == 1){
                    clockArea();
                }
                if(data.status == 0){
                    dialog.error(data.info)
                }
            }
        });
    });

    //一键分享转发 微信分享提示图
    $('body').on('click','.forward',function(){
        $.ajax({
            url: MODULE + '/CommonAuthUser/checkLogin',
            data:{},
            type:'post',
            beforeSend:function(xhr){
                $('.loading').show();
            },
            error:function(xhr){
                $('.loading').hide();
                dialog.error('AJAX错误');
            },
            success:function(data){
                $('.loading').hide();
                if(data.status == 0){
                    if(data.url){
                        location.href = data.url;
                    }else{
                        dialog.error(data.info);
                    }
                }else if(data.status == 1){
                    if(data.info=='isAjax'){
                        loginDialog(flushPage);
                    }else{
                        $('.mcover').show();
                    }
                }
            }
        });
    });
    //关闭微信分享提示图
    $('.weixinShare_btn').on('click',function(){
        $('.mcover').hide();
    });

    //发起微团购并支付
    $('body').on('click','.initiate_group_buy',function(){
        var postData = assemblyData();
        if(!postData){
            return false;
        }
        var url = MODULE + '/Order/generate';
        postData.returnUrl = location.href;
        $.ajax({
            url: url,
            data: postData,
            type: 'post',
            beforeSend:function(xhr){
                $('.loading').show();
            },
            error:function(xhr){
                $('.loading').hide();
                dialog.error('AJAX错误');
            },
            success: function(data){
                $('.loading').hide();
                if(data.status==0){
                    dialog.error(data.info);
                }else{
                    location.href = MODULE + '/Order/orderDetail/orderId/' + data.id;
                }
            }
        });
    });
});
//关闭二维码
function clockArea() {
    $("#areaMask,.express-code-box").fadeOut();
}

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
        var goodsId = _this.data('id');
        if(parseInt(num) && goodsId){
            var tmp = {};
            tmp.foreign_id = goodsId;
            tmp.num = num;
            postData.goodsList.push(tmp);
        }
    });
    if(postData.goodsList && postData.goodsList.length == 0){
        dialog.error('请选择商品');
        return false;
    }
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
    return postData;
}

//计算商品列表总价
function calculateTotalPrice(){
    var _thisLis = $('ul.goods_list').find('li');
    var isInt = true;
    var amount = 0;
    $.each(_thisLis,function(){
        var _this = $(this);
        var num = _this.find('.gshopping_count').val();
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        amount += _this.find('price').text() * num;
    });
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
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
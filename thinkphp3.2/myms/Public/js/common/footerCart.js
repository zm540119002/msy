$(function () {
    //列表形式加入购物车
    $('body').on('click','.add_cart',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        if(!postData){
            return false;
        }
        addCart(postData)
    });

    //立即结算/立即购买/发起微团
    $('body').on('click','.buy_now,.clearing_now',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        if(!postData){
            return false;
        }
        generateOrder(postData,buyNowCallBack);
    });

    var group_buy_end = $('.groupBuyEnd').val();
    if(group_buy_end){ //重新开团
        dialog.confirm('此团购已结束，是否重新开团')
    }
    //发起微团购并支付
    $('body').on('click','.initiate_group_buy',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        if(!postData){
            return false;
        }
        postData.groupBuyId = $('.groupBuyId').val();
        var group_buy_end = $('.groupBuyEnd').val();
        if(group_buy_end){ //重新开团
            delete(postData["groupBuyId"]);
        }
        generateOrder(postData,groupBuyCallBack);
    });

    //一键分享转发 微信分享提示图
    $('body').on('click','.forward',function(){
        $.ajax({
            url: MODULE + '/CommonAuthUser/checkLogin',
            type:'post',
            beforeSend: function(){
                $('.loading').show();
            },
            error:function(){
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
    //关闭微信分享
    $('.weixinShare_btn').on('click',function(){
        $('.mcover').hide();
    })

    //列表购物车弹窗
    $('body').on('click','.shopping_cart',function(){
        var _li = $(this).parents('li');
        var goods_type = _li.data('goods_type');
        var id = _li.data('id');
        var position = 'list';
        getPurchaseDetails(id,goods_type,position);
    });

    //关闭详情购物车弹窗
    $('body').on('click','.mask,.closeBtn',function(){
        $('.express-area-box,.mask').remove();
        $('html,body').css({"overflow":"auto"});
    });

    //底层弹窗的加
    $('body').on('click','.gplus',function(){
        shopNum = parseInt($(this).siblings('.gshopping_count').val());
        shopNum++;
        $(this).siblings('.gshopping_count').val(shopNum);
        allAmount(shopNum);
    });
    //底层弹窗的减
    $('body').on('click','.greduce',function(){
        shopNum = parseInt($(this).siblings('.gshopping_count').val());
        shopNum--;
        if(shopNum<1){
            return false;
        }
        $(this).siblings('.gshopping_count').val(shopNum);
        allAmount(shopNum);
    });
    //确认订单
    $('body').on('click','.determine_order',function(){
        var addressInfo = {};
        var postData = {};
        var addressId = $('.addressid').val();
        if(!addressId){
            var content='';
            var consignee=$('.recipient_name').val();
            var mobile=$('.recipient_mobile').val();
            var area_address=$('.district_address').val();
            var address=$('.recipient_detail_address').val();
            if(!consignee){
                content='请填写收货人姓名';
            }else if(!register.phoneCheck(mobile)){
                content='请填写正确的手机号码';
            }else if(!area_address){
                content='请选择地区';
            }else if(!address){
                content='请填写详细地址';
            }
            if(content){
                dialog.error(content);
                return false;
            }
            addressInfo.consignee = consignee;
            addressInfo.mobile = mobile;
            addressInfo.area_address = area_address;
            addressInfo.address = address;
            postData.addressInfo = addressInfo;
        }else{
            postData.addressId = addressId;
        }
        var orderId = $('.orderid').val();
        postData.orderId = orderId;
        var url = MODULE + '/Order/confirmOrder';
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
                }
                if(data.status==1){
                    location.href = MODULE + '/Order/settlement/orderId/' + data.id;
                }
            }
        });
    });

});
//计算总价
function allAmount(singleNum){
    var allAmount=0;
    var goodsPrice=parseFloat($('.concessional_rate price').text());
    allAmount=singleNum*goodsPrice;
    allAmount=parseFloat(allAmount).toFixed(2)
    $('.goods_total_price price').text(allAmount);
    return allAmount;
}
//购物车弹窗详情
function getPurchaseDetails(id,type,position) {
    if(type == 1){
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
                var realPrice=$('.real_price price').text();
                allAmount(1);
            }
            if(position === 'info'){
                $('.msh_product_picture').after(data);
                $('.express-area-box').css({zIndex:22});
                $('.express-area-box .group_cart_nav').css({display:'flex',zIndex:22});
            }
        }
    });
    if(!$('.mask').data('show')){
        $('html,body').css({"height":"100%","overflow":"hidden"});//动态阻止body页面滚动
        $('.mask').data('show',1);
        $('.signShopping_nav').css({display:'flex',zIndex:22});
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
//添加购物车
function addCart(data) {
    var url =  MODULE + '/Cart/addCart';
    $.ajax({
        url: url,
        data: data,
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
        }
    });
}
//组装数据
function assemblyData(lis) {
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    $.each(lis,function(){
        var _this = $(this);
        var singleChecked = _this.find('.single_select').is(':checked');
        if(singleChecked){
            var num = _this.find('.gshopping_count').val();
            if(!isPosIntNumber(num)){
                isInt = false;
                return false;
            }
            var id = _this.data('id');
            var goods_type = _this.data('goods_type');
            if(parseInt(num) && id){
                var tmp = {};
                tmp.foreign_id = id;
                tmp.num = num;
                tmp.goods_type = goods_type;
                postData.goodsList.push(tmp);
            }
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
//生成订单
function generateOrder(postData,callBack) {
    postData.url = postData.url?postData.url:MODULE + '/Order/generate';
    $.ajax({
        url: postData.url,
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
                if(data.joined){
                    layer.open({
                        content : data.info?data.info:'成功',
                        btn:['确定','取消'],
                        end : function(){

                        },
                        yes:function(index){
                            delete(postData["groupBuyId"]);
                            generateOrder(postData,groupBuyCallBack);
                            layer.close(index)
                        }
                    });
                }else{
                    dialog.error(data.info);
                }
            }else if(data.status == 1){
                if(data.info=='isAjax'){
                    loginDialog(callBack);
                }else{
                    location.href = MODULE + '/Order/orderDetail/orderId/' + data.orderId;
                }
            }
        }
    });
}
//立即购买回调
function buyNowCallBack() {
    $('.buy_now,.clearing_now').click();
}
//立即购买回调
function groupBuyCallBack() {
    $('.initiate_group_buy').click();
}


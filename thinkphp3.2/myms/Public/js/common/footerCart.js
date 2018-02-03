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
    $('body').on('click','.buy_now,.clearing_now,.initiate_group_buy',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        if(!postData){
            return false;
        }
        generateOrder(postData,buyNowCallBack);
    });
    //确认订单，跳转到结算页面
    $('body').on('click','.determine_order',function () {
        var area_address = $('#area_address').val();
        if(area_address){
            var content='';
            var consignee=$('.recipient_name').val();
            var mobile=$('.recipient_mobile').val();
            var district=$('.area_address').text();
            var  address=$('.recipient_detail_address').val();
            if(!consignee){
                content='请填写收件人姓名';
            }else if(!register.phoneCheck(mobile)){
                content='请填写正确的手机号码';
            }else if(district=='省 > 市 > 区/县'){
                content='请选择地区';
            }else if(!address){
                content='请填写详细地址';
            }
            if(content){
                layer.open({
                    content:content,
                    time: 2,
                });
                return false;
            }
        }
        var addressId = $('#addressId').val();
        var cartIds   = $('#cartIds').val();
        var invoice_title  = $('#invoice_title').val();
        var goodsId = $('#goodsId').val();
        var projectId = $('.projectId').val();
        var goodsNum = $(this).parents().find('.shopping_count').val();
        var groupBuySn = $('.groupBuySn').val();
        //提交订单，跳转到支付页面
        $.post(CONTROLLER + '/addOrder',
            {
                addressId:addressId,cartIds:cartIds,invoice_title:invoice_title,
                consignee:consignee,mobile:mobile,district:district,address:address,
                goodsId:goodsId,projectId:projectId,goodsNum:goodsNum,groupBuySn:groupBuySn
            },
            function (msg) {
                if(msg.status == 0){
                    dialog.error(msg.info);
                }
                if(msg.status == 1){
                    var url = MODULE + '/Order/settlement/orderId/'+msg.info;
                    dialog.success('提交订单成功',url)
                }
            })
    });
    
    //微信分享提示图
    $('body').on('click','.forward_weChat',function(){
        $('.mcover').show();
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
                //$('.goods_total_price price').text(realPrice);
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
        // ,
        // complete:function(){
        //     $('.group_cart_nav,.mask').hide();
        //     $('.goodsInfo_footer_nav').show();
        //     $('.express-area-box').css({bottom:'-100%',display:'none'});
        //     $('html,body').css({"overflow":"auto"});
        // }
    });
}
//组装数据
function assemblyData(lis) {
    // if(!$('footer').find('price').length){
    //     return false;
    // }
    alert(1);
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    $.each(lis,function(){
        var _this = $(this);
        var singleChecked=$('.single_select').is(':checked');
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
                    //dialog.confirm(data.info,data.url);
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


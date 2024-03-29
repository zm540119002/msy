/**
 * 没有登录购物车 操作
 * @param postData
 */
cart = {
    //向购物车中添加商品
    addCart: function (postData) {
        //获取存储购物车商品信息
        var cartListOld = localStorage.cartList;
        if (cartListOld == null || cartListOld == "") {
            //第一次加入商品
            var cartList = JSON.stringify(postData);
            localStorage.setItem('cartList',cartList);
            var goodsList = postData.goodsList;
        }else {
            var jsonstr = JSON.parse(cartListOld);
            var goodsList = jsonstr.goodsList;
            var addGoodsList = postData.goodsList;
            //查找购物车中是否有该商品
            $.each(addGoodsList,function(i,addGoods){
                var find = false;
                $.each(goodsList,function(j,goods){
                    if(addGoods.goods_id == goods.goods_id){
                        //找到修改数量
                        find = true;
                        goodsList[j].num = parseInt(addGoods.num) + parseInt(goods.num);
                        return;
                    }
                });
                if(!find){
                    //没有该商品就直接加进去
                    goodsList.unshift({
                        "goods_id": addGoods.goods_id,
                        "num": addGoods.num,
                    });
                }
            });
            var cartList = {
                goodsList:goodsList
            };
            //保存购物车
            localStorage.setItem('cartList',JSON.stringify(cartList));
        }
        //计算总数
        var total_num = 0;
        $.each(goodsList,function(i,goods){
            total_num += parseInt(goods['num']);
        });
        dialog.success('成功');
        $('footer').find('.add_num').text(total_num).addClass('current');
    },
    //修改商品数量
    editCartNum:function (goods) {
        //获取存储购物车商品信息
        var cartListOld = localStorage.cartList;
        var jsonstr = JSON.parse(cartListOld);
        var goodsList = jsonstr.goodsList;

        $.each(goodsList,function(j,oldgoods){
            if(goods.goods_id == oldgoods.goods_id){
                //找到修改数量
                goodsList[j].num = parseInt(goods.num);
                return;
            }
        });
        var cartList = {
            goodsList:goodsList
        };
        //保存购物车
        //localStorage.removeItem("cartList");//删除变量名为key的存储变量
        localStorage.setItem('cartList',JSON.stringify(cartList));
    },
    //删除商品
    delCart:function (goods) {
        //获取存储购物车商品信息
        var cartListOld = localStorage.cartList;
        var jsonstr = JSON.parse(cartListOld);
        var goodsList = jsonstr.goodsList;
        $.each(goods.goods_ids,function(i,goods_id){
            $.each(goodsList,function(j,oldgoods){
                if(goods_id == oldgoods.goods_id){
                    goodsList = goodsList.filter(function (element, index, self) {
                        return index<j||index>j
                    });
                }
            });
        });
        var cartList = {
            goodsList:goodsList
        };
        //保存购物车
        localStorage.setItem('cartList',JSON.stringify(cartList));
        //删除对应的商品列
        dialog.success('成功');
        $.each(goods.goods_ids,function(i,goods_id){
            $.each($('.cart_goods_list li'),function(j,oldgoods){
                var id = $(this).data('id');
                if(goods_id == id){
                    $(this).remove();
                }
            });
        });
    },
    //统计购物车的数量
    getGoodsTotal:function () {
        //获取存储购物车商品信息
        var cartListOld = localStorage.cartList;

        var total_num = 0;
        if(cartListOld){
            var jsonstr = JSON.parse(cartListOld);
            var goodsList = jsonstr.goodsList;
            //计算总数
            $.each(goodsList,function(i,goods){
                total_num += parseInt(goods['num']);
            });
        }

        return total_num;
    }
    
};



$(function () {
    //加
    $('body').on('click','.gplus',function(){
        var incrementObj={};
        incrementObj.order_quantity=$(this).siblings('.minimum_order_quantity').val();
        incrementObj.increase_quantity=$(this).siblings('.increase_quantity').val();
        //单个商品数量自加
        goodsNumPlus($(this),incrementObj);
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //减
    $('body').on('click','.greduce',function(){
        var incrementObj={};
        incrementObj.order_quantity=$(this).siblings('.minimum_order_quantity').val();
        incrementObj.increase_quantity=$(this).siblings('.increase_quantity').val();
        //单个商品数量自减
        goodsNumReduce($(this),incrementObj);
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //购买数量.失去焦点
    $('body').on('blur','.gshopping_count',function(){
        var buyNum=parseInt($(this).val());
        var orderNum=parseInt($(this).siblings('.minimum_order_quantity').val());
        if(buyNum<orderNum){
             dialog.error('起订数量不能少于'+orderNum);
             $(this).val(orderNum);
             return false;
        }
        if(buyNum>orderNum){
            dialog.error('购买限额为'+orderNum);
            $(this).val(orderNum);
            return false;
        }
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //购物车加
    $('body').on('click','.cart_gplus',function(){
        //购物车单个商品数量自加
        cartGoodsNumPlus($(this));
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算商品列表总价
        calculateCartTotalPrice($(this));
        //修改数据库数量
        var _this = $(this);
        var postData = {};
        var id = _this.parents('li').data('cart_id');
        var goods_id = _this.parents('li').data('id');
        var num = _this.siblings('.cart_gshopping_count').val();
        postData.id = id;
        postData.num = num;
        postData.goods_id = goods_id;

        if(user_id){
            editCartNum(postData,_this);
        }else{
            cart.editCartNum(postData,_this);
        }
    });
    //购物车减
    $('body').on('click','.cart_greduce',function(){
        //购物车单个商品数量自减
        cartGoodsNumReduce($(this));
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算购物车商品列表总价
        calculateCartTotalPrice($(this));
        //修改数据库数量
        var _this = $(this);
        var postData = {};
        var id = _this.parents('li').data('cart_id');
        var goods_id = _this.parents('li').data('id');
        var num = _this.siblings('.cart_gshopping_count').val();
        postData.id = id;
        postData.num = num;
        postData.goods_id = goods_id;
        if(user_id){
            editCartNum(postData,_this);
        }else{
            cart.editCartNum(postData,_this);
        }

    });
    //购物车购买数量.失去焦点
    $('body').on('blur','.cart_gshopping_count',function(){
        var buyNum=parseInt($(this).val());
        if(buyNum<1){
             $(this).val(1);
             return false;
        }
        $(this).parents('li').find('.sign_checkitem').prop("checked",true);
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算购物车商品列表总价
        calculateCartTotalPrice($(this));
    });
    //购物车删除
    $('body').on('click','.detele_cart',function(){
        var _li = $(this).parents('li');
        var postData = {};
        var cart_ids=[];
        var goods_ids=[];
        cart_ids.push(_li.data('cart_id'));
        goods_ids.push(_li.data('id'));
        if(user_id){
            postData.cart_ids = cart_ids;
            delCart(postData, 'single',$(this));
        }else{
            postData.goods_ids = goods_ids;
            cart.delCart(postData);
        }
    });
    //购物车全选总价
    $('body').on('click','footer .checkall,.cpy_checkitem,.sign_checkitem',function(){
        //计算购物车商品列表总价
        calculateCartTotalPrice();
    });

    //加入购物车
    $('body').on('click','.add_cart,.add_purchase_cart',function(){
        var _this = $(this);
        var lis = null;
        lis = $('ul.goods_list').find('li[data-buy_type="1"]');
        var postData = assemblyData(lis);
        if(!postData){
            return false;
        }
        if (user_id){ //登录
            postData._this = _this;
            postData.lis = lis;
            addCart(postData);
        } else{//没有登录
            cart.addCart(postData);

        }
    });
    //购物车列表页
    $('body').on('click','.add_cart_icon',function(){
        location.href = module + 'Index/cartManage';
    });
    //去结算 生成订单
    $('body').on('click','.settlement',function(){
        var postData = {};
        var goodsList = [];
        var oLis=$('.cart_goods_list li');
        $.each(oLis,function () {
            var signcheck=$(this).find('.sign_checkitem');
            if(signcheck.prop('checked')){
                var goods_id=$(this).data('id');
                var num=$(this).find('.cart_gshopping_count').val();
                goodsList.push({
                    goods_id:goods_id,
                    num:num
                });
            }
        });
        postData.goodsList = goodsList;
        if($.isEmptyArray(goodsList)){
            dialog.error('请选择要结算的商品');
            return false
        }
        var _this = $(this);
        _this.addClass("nodisabled");//防止重复提交
        generateOrder(postData,_this);
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
            var brand_id=_this.data('brand_id');
            var brand_name=_this.data('brand_name');
            var num=_this.find('span.num').text();
            goodsList.push({
                goods_id:goods_id,
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

    $('body').on('click','.pay',function () {
        var orderSn =  $('#order_sn').val();
        location.href = module + 'Order/toPay/order_sn/' + orderSn;
    });

    // 选择支付方式
    $('body').on('click','.settlementmethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });

    // 结算订单处理 增加支付方式
    $('body').on('click','.settlement_btn',function () {
        var pay_code = $('input[name=pay_code]').val();
        var sn = $('input[name=order_sn]').val();
        if(!pay_code){
            dialog.error('请选择支付方式');
            return false;
        }

        var postData = {};
        postData.pay_code = pay_code;
        postData.sn       = sn;

        $.post(url,postData,function(data){
            if(data.status){
                location.href = data.info;

            }else{
                dialog.error('结算提交失败!');
            }
        });

    });

    //一键分享转发 微信分享提示图
    $('body').on('click','.share',function(){
        $('.mcover').show();
    });
    //关闭微信分享提示图
    $('body').on('click','.weixinShare_btn',function(){
        $('.mcover').hide();
    });

});

// 支付方式弹窗
function paymentMethod(){
    var settlementMethod=$('.settlementMethod').html();
    layer.open({
        type: 1
        ,anim: 'up'
        ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 50%; padding:10px 0; border:none;',
        className:'settlementmethod bankTransferLayer',
        content: settlementMethod
    });
}

//生成订单
function generateOrder(postData,obj) {
    var url = module + 'Order/generate';
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
            obj.removeClass("nodisabled");//防止重复提交
            $('.loading').hide();

            if(data.status){

                location.href = data.data.url;
            }else{
                dialog.error(data.info);
            }

        }
    });
}

//组装数据
function assemblyData(lis) {
    if(!$('footer').find('price').length){
        return false;
    }
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    $.each(lis,function(){
        var _this = $(this);
        var num = _this.find('.gshopping_count').val();
        //alert(num);
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        var goodsId = _this.data('id');
        //alert(goodsId);
        if(parseInt(num) && goodsId){
            var tmp = {};
            tmp.goods_id = goodsId;
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
function calculateTotalPrice(obj){
   
    var buyType=obj.data('type');
    
    if(!$('footer').find('price').length){
        return false;
    }
    var isInt = true;
    var amount = 0;
    if(buyType=='sample'){
        var _thisLis = $('.goodsInfoLayer ul.goods_list').find('li');
        $.each(_thisLis,function(index,val){
            var _thisLi = $(this);
            var num = _thisLi.find('.gshopping_count').val();
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
            amount += _thisLi.find('.sample_price').text() * num;
        });
        $('.goodsInfoLayer footer').find('price').html(amount.toFixed(2));
    }else{
        var _thisLis = $('.list.goods_list').find('li');
        $.each(_thisLis,function(index,val){
            var _thisLi = $(this);
            var num = _thisLi.find('.gshopping_count').val();
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
            amount += _thisLi.find('price').text() * num;
        });
        $('footer').find('price').html(amount.toFixed(2));
    }
    
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
   
}
//计算购物车商品列表总价
function calculateCartTotalPrice(obj){
    if(!$('footer').find('price').length){
        return false;
    }
    var isInt = true;
    var totalNum=0;
    var amount = 0;
    var _thisLis = $('.cart_goods_list').find('li');
    $.each(_thisLis,function(index,val){
        var _thisLi = $(this);
        if(_thisLi.find('.sign_checkitem').is(':checked')){
            var num = _thisLi.find('.cart_gshopping_count').val();
            totalNum+=parseInt(num);
            amount += _thisLi.find('price').text() * num;
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
        }
    });
    $('footer').find('price').html(amount.toFixed(2));
    $('footer').find('.total_num').text('('+totalNum+')'+'件');
}
//单个商品数量自减
function goodsNumReduce(obj,opt) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    num=parseInt(num);
    var orderQuantity=parseInt(opt.order_quantity);
    if(num<=orderQuantity){
        return false;
    }
    num=num-parseInt(opt.increase_quantity);
    _li.find('.gshopping_count').val(num);

}

//单个商品数量自加
function goodsNumPlus(obj,opt) {
    var _li = obj.parents('li');
    var orderQuantity=parseInt(opt.order_quantity);
    var num = _li.find('.gshopping_count').val();
    num=parseInt(num);
    if(num==0){
        _li.find('.gshopping_count').val(opt.order_quantity);
    }else{
        num=num+parseInt(opt.increase_quantity);
        _li.find('.gshopping_count').val(num);
    }

}
//购物车中单个商品数量自减
function cartGoodsNumReduce(obj) {
    var _item = obj.parents('li');
    var num = _item.find('.cart_gshopping_count').val();
    num=parseInt(num);
    // var orderQuantity=parseInt(opt.order_quantity);
    if(num<2){
        // _item.find('.sign_checkitem').prop("checked",false);
        return false;
    }
    //num=num-parseInt(opt.increase_quantity);
    _item.find('.cart_gshopping_count').val(--num);
    _item.find('.sign_checkitem').prop("checked",true);
}

//购物车中单个商品数量自加
function cartGoodsNumPlus(obj) {
    var _item = obj.parents('li');
        _item.find('.sign_checkitem').prop("checked",true);
    var num = _item.find('.cart_gshopping_count').val();
    num=parseInt(num);
    //num=num+parseInt(opt.increase_quantity);
    _item.find('.cart_gshopping_count').val(++num);
}

//修改购物车商品数量
function editCartNum(postData,obj) {
    var url = module + 'Cart/editCartNum';
    obj.addClass("nodisabled");//防止重复提交
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
            obj.removeClass("nodisabled");//防止重复提交
            $('.loading').hide();
            
        }
    });
}

//选择或当个删除购物车
function delCart(postData,type,obj) {
    var url = module + 'Cart/del';
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
                                var cartId = _this.data('cart_id');
                                for(var i=0;i<postData.cart_ids.length;i++){
                                    if(cartId == postData.cart_ids[i]){
                                        _this.remove();
                                    }
                                }
                            });
                        }
                        if(!$('.cart_goods_list li').length){
                            var no_cart_data=$('.no_cart_data').html();
                            $('.cart_goods_list').append(no_cart_data);
                        }
                        calculateCartTotalPrice();
                        dialog.success(data.info);
                    }
                }
            });
            layer.close(index);
        }
    });
}

/**
 * 登录状态加入购物车
 * @param postData
 */
function addCart(postData) {
    var url = module + 'Cart/addCart';
    var _this=postData._this;
    var lis=postData.lis;
    _this.addClass("nodisabled");//防止重复提交
    delete postData._this;
    delete postData.lis;
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
            _this.removeClass("nodisabled");//防止重复提交
            if(data.status==0){
                dialog.error(data.info);
            } else{
                dialog.success(data.info);
                var num = 0;
                $.each(lis,function(index,val){
                    num += parseInt($(this).find('.gshopping_count').val());

                });
                console.log(num);
                total_num = parseInt(total_num) + parseInt(num);
                $('footer').find('.add_num').text(total_num).addClass('current');
            }
        }
    });
}


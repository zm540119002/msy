<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title><?php echo (C("WEB_NAME")); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="/Public/css/common/public.css">
	<link rel="stylesheet" href="/Public/css/common/weui.css">
	
    <link rel="stylesheet" type="text/css" href="/Public/css/home/mall.css">

	<script type="text/javascript" src="/Public/js/common/jquery-1.9.1.min.js"></script>
</head>

<body>
<!-- 基础内容模块，子页面可覆盖 -->

    <!-- 美悦会商城首页 -->
    <div id="slider">
        <div class='swipe-wrap'>
            <div><img src="/Public/img/common/banner/home-banner.jpg" /></div>
            <div><img src="/Public/img/common/banner/home-banner1.jpg" /></div>
            <div><img src="/Public/img/common/banner/home-banner2.jpg" /></div>
            <div><img src="/Public/img/common/banner/home-banner3.jpg" /></div>
        </div>
        <ul class="position">
            <li class="on"></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <nav class="mall_function_menu navigation_module f24">
        <a href="">
            <span class="mall_home"></span>
            商城首页
        </a>
        <a href="<?php echo U('groupBuyIndex');?>">
            <span class="wt_purchase"></span>
            微团购
        </a>
        <a href="<?php echo U('Index/referrerIndex');?>">
            <span class="twitter_share"></span>
            推客分享
        </a>
        <a href="<?php echo U('Cart/index');?>">
            <span class="buy_cart"></span>
            购物车
        </a>
        <a href="<?php echo U('PersonalCenter/index');?>">
            <span class="person_center"></span>
            个人中心
        </a>
    </nav>
    <section class="f24">
        <ul class="business_product_module goodsListContent">
            <?php if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </section>
    <!-- 美悦会商城首页-end -->

<!-- 返回顶部-->

    <div class="right_sidebar">
    <a href="<?php echo U('Index/index');?>" class="f24">首页</a>
    <a href="javascript:void(0);" class="f24 backTop">顶部</a>
</div>

<!-- 加载更多-->

    <div class="loading">
    <img src="/Public/img/common/loading.gif" alt=""><span class="f24">正在加载...</span>
</div>


	<section id="userLoginForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>已注册用户通行证登陆</p>
        </div>
        <ul class="loginNav">
            <li class="current">账号密码登录</li>
            <li>短信验证码登陆</li>
        </ul>
        <form class="loginTab" id="formLogin">
            <div class="userLogin login_wrap active">
                <div class="login_item">
                    <span class="username_txt">账号</span>
                    <input class="username user_name" type="text" placeholder="6-16位数字、字母或下划线" name="name">
                </div>
                <div class="login_item">
                    <span class="password_txt">密码</span>
                    <input class="username password" type="password" placeholder="6-16数字或字母" name="password">
                </div>
            </div>
            <div class="smsLogin login_wrap hide">
                <div class="login_item">
                    <span class="username_txt">手机号</span>
                    <input class="username user_phone" type="tel" placeholder="请输入手机号" name="mobile_phone">
                    <a href="javascript:void(0);" class="mesg_code">获取验证码</a>
                </div>
                <div class="login_item">
                    <span class="username_txt txt3">验证码</span>
                    <input type="text" class="tel_code username input_txt3" placeholder="请输入收到的验证码" name="captcha">
                </div>
            </div>
            <input type="hidden" name="captcha_type" value="login" />
        </form>
        <div class="error_tipc" ></div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="register_dialog">注册</a>
            <a href="javascript:void(0);" class="forget_dialog">忘记美尚通行证密码?重置密码</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userLoginForm=$('#userLoginForm').html();
    //登录触发
    var loginLayer = null;
    function loginDialog(func){
        loadTrigger=false;
        loginLayer = layer.open({
            className:'loginLayer',
            content:userLoginForm,
            // shadeClose:false,
            btn:'登录',
            success:function(){
                window.scrollTo(0,0);
                tab_down('.loginNav li','.loginTab .login_wrap','click');
                $('.layui-m-layershade').on('touchmove',function(e){
                    event.preventDefault(); 
                });
               isRolling($('.loginLayer'));
            },
            end:function(){
                loadTrigger=true;
            },
            yes:function(index){
                var $layer = $('.loginLayer').find('.active');
                var _index =$('.loginLayer').find('.login_wrap.active').index();
                var content='';
                //验证
                switch(_index){
                    case 0:
                        var userName=$layer.find('.user_name').val();
                        var password=$layer.find('.password').val();
                        if(!checkAccount(userName)){
                            content='请输入正确用户名';
                        }else if(!register.pswCheck(password)){
                            content = "请输入6-16数字或字母的密码";
                        }
                        break;
                    case 1:
                        var userPhone=$layer.find('.user_phone').val();
                        var verifiCode=$layer.find('.tel_code').val();
                        if(!register.phoneCheck(userPhone)){
                            content='请输入正确手机号';
                        }else if(!register.vfyCheck(verifiCode)){
                            content = "请输入正确的验证码";
                        }
                        break;
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/login");?>';
                var postData = $('.loginLayer').find('#formLogin').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            loadTrigger=true;
                            layer.close(index);
                        }
                    }
                });
            }
        });
    };
    
    $(function(){
        //注册
        $('body').on('click','.register_dialog',function(){
            platformNotesDialog();
            //registerDialog();
        });

        //忘记密码
        $('body').on('click','.forget_dialog',function(){
            forgetPasswordDialog();
        });
        
    });
</script>

	<section id="userRegisterForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>新用户通行证注册</p>
        </div>
        <form class="registerTab" id="formRegister">
            
            <input type="hidden" name="captcha_type" value="register" />
        </form>
        <div class="error_tipc" ></div>
        <div class="guide_friendly_tipc">
            <p class="register_explain">说明：美尚通行证适用于集团旗下美尚云、美尚会、美创会等平台</p>
        </div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="register_back">返回登录</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userRegisterForm=$('#userRegisterForm').html();
    //注册触发
    var registerLayer = null;
    function registerDialog(func){
        registerLayer = layer.open({
            className:'registerLayer',
            content:userRegisterForm,
            btn:['确定'],
            success:function(){
                $('.registerLayer .mesg_code').text('获取验证码');
                $('input[name="notice"]').attr('checked',true);
            },
            yes:function(index){
                var $layer=$('.registerLayer').find('.registerTab');
                //验证
                var userName=$layer.find('.user_name').val();
                var password=$layer.find('.password').val();
                var newPassword=$layer.find('.cofirm_password').val();
                var userPhone=$layer.find('.user_phone').val();
                var verifiCode=$layer.find('.tel_code').val();
                var content='';
                if(!checkAccount(userName)){
                    content='请输入正确用户名';
                }else if(!register.pswCheck(password)){
                    content = "请输入正确的密码";
                }else if(password!=newPassword){
                    content = "两次密码输入不一致";
                }else if(!register.phoneCheck(userPhone)){
                    content = "请输入正确的手机号码";
                }else if(!register.vfyCheck(verifiCode)){
                    content = "请输入正确的验证码";
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/register");?>';
                var postData = $('.registerLayer').find('#formRegister').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            layer.close(index);
                        }
                    }
                });
            }
        });
    }
    $(function(){
        //返回登录
        $('body').on('click','.register_back',function(){
            loginDialog();
        })
    })
</script>

	<section id="userForgetPasswdForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>重置通行证登陆密码</p>
        </div>
        <form class="registerTab" id="formReset">
            
            <input type="hidden" name="captcha_type" value="reset" />
        </form>
        <div class="error_tipc" ></div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="forgetPasswd_back">返回登录</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userForgetPasswdForm=$('#userForgetPasswdForm').html();
    //注册触发
    var forgetPasswdLayer = null;
    function forgetPasswordDialog(func){
        forgetPasswdLayer = layer.open({
            className:'forgetPasswdLayer',
            content:userForgetPasswdForm,
            btn:['确定'],
            success:function(){
                $('.forgetPasswdLayer .mesg_code').text('获取验证码');
            },
            yes:function(index){
                var $layer=$('.forgetPasswdLayer').find('.forgetPasswd_wrap');
                //验证
                var userName=$layer.find('.user_name').val();
                var password=$layer.find('.password').val();
                var newPassword=$layer.find('.cofirm_password').val();
                var userPhone=$layer.find('.user_phone').val();
                var verifiCode=$layer.find('.tel_code').val();
                var content='';
                if(!checkAccount(userName)){
                    content='请输入正确用户名';
                }else if(!register.pswCheck(password)){
                    content = "请输入正确的密码";
                }else if(password!=newPassword){
                    content = "两次密码输入不一致";
                }else if(!register.phoneCheck(userPhone)){
                    content = "请输入正确的手机号码";
                }else if(!register.vfyCheck(verifiCode)){
                    content = "请输入正确的验证码";
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/forget_password");?>';
                var postData = $('.forgetPasswdLayer').find('#formReset').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            layer.close(index);
                        }
                    }
                });
            }
        });
    }
    $(function(){
        //返回登录
        $('body').on('click','.forgetPasswd_back',function(){
            loginDialog();
        })
    })
</script>

	<section id="platformNotes" style="display:none;">
    <div class="platformNoteText">
        <p>111111111111111111</p>
        <p>222222222222222222</p>
        <p>333333333333333333</p>
        <p>444444444444444444</p>
        <div class="agreem_marquee">
            <input type="checkbox" class="agreement_consent" id="agreementConsent" >
            <label for="agreementConsent">我已阅读并同意该协议</label>
        </div>
    </div>
</section>
<script type="text/javascript">
    var platformNotes=$('#platformNotes').html();
    var notesDialogLayer=null;
    function platformNotesDialog(){
        notesDialogLayer=layer.open({
            title:['《美尚平台使用须知》','border-bottom:1px solid #d9d9d9;'],
            className:'notesLayer',
            content:platformNotes,
            btn:['下一步'],
            success:function(){},
            yes:function(index){
                if($('.agreement_consent').is(':checked')){
                    registerDialog();
                    layer.close(index);
                }
            }
        });
    }
</script>

<!-- 基础页脚模块，子页面可覆盖 -->

    <section id="areaLayer" class="express-area-box cart" style="display:none;">
    <div class="purchase_goods_module f24 goodsInfo">
        <ul class="goods_list"></ul>
    </div>
    <script type="text/javascript" src="/Public/js/common/footerCart.js"></script>
<footer class="f24 group_cart_nav group_cart_toggle">
    <?php if(is_array($unlockingFooterCartSingle)): $i = 0; $__LIST__ = $unlockingFooterCartSingle;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo["share"])): ?><!--分享-->
            <div class="group_btn25 bfff">
                <a href="javascript:void(0);" class="share_column share"><span class="s_i"></span><?php echo ($vo["share"]); ?></a>
            </div><?php endif; ?>
        <?php if(!empty($vo["shopping_basket"])): ?><!--购物篮-->
            <div class="group_btn25 bfff">
                <a href="javascript:void(0);" class="share_column shopping_cart_column shopping_basket">
                    <span class="s_c"><i>5</i></span></a>
            </div><?php endif; ?>
        <?php if(!empty($vo["amount"])): ?><!--总价-->
            <div class="group_btn50 bfff">
                <span class="goods_total_price amount">￥<price>0.00</price></span>
            </div><?php endif; ?>
        <?php if(!empty($vo["add_cart"])): ?><!--加入购物车-->
            <a href="javascript:void(0);" class="bff6482 group_btn50 add_cart"><?php echo ($vo["add_cart"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["clearing_now"])): ?><!--立即结算-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 clearing_now"><i class="collect"></i><?php echo ($vo["clearing_now"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["buy_now"])): ?><!--立即购买-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 buy_now"><i class="collect"></i><?php echo ($vo["buy_now"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["determine_order"])): ?><!--确定订单-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 determine_order"><i class="collect"></i><?php echo ($vo["determine_order"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["pay_now"])): ?><!--立即支付-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 pay_now"><i class="collect"></i><?php echo ($vo["pay_now"]); ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
</footer>
</section>
<div id="areaMask" class="mask" data-show="0"></div>
<script type="text/javascript">
    $(function () {
        //购物车点击
        $('body').on('click','.shopping_cart',function(){
            if(!$('.mask').data('show')){
                $('html,body').css({"height":"100%","overflow":"hidden"});//动态阻止body页面滚动
                $('.mask').show().css({position:'fixed'});
                $('.mask,.goodsInfo').on('touchmove',function(e){
                    console.log(e);
                    e.preventDefault(); 
                });
                $('.mask').data('show',1);
                $('.group_cart_toggle').css({display:'flex',zIndex:21});
                $('.express-area-box').css({bottom:0+'px',display:'block',position:'fixed',zIndex:19});
                if($('.shoppingCart_form ul').height()>420){
                    $('.shoppingCart_form ul').css({"max-height":4.5+'rem'});
                }
                
            }else{
                $('html,body').css({"overflow":"auto"});
                $('.mask').hide();
                $('.express-area-box').css({bottom:'-100%',display:'none'});
                $('.mask').data('show',0);
                $('.group_cart_toggle').hide();
            }
            //先清空
            //$('.gshopping_count').val(0);
            //$('.goods_total_price').find('price').text(0);
            var _li = $(this).parents('li');
            var url = MODULE + '/Goods/goodsInfo';
            var postData = {};
            postData.goodsId = _li.data('id');
            $.ajax({
                url: url,
                data: postData,
                type: 'get',
                beforeSend: function(){
                },
                error:function(){
                    $('.loading').hide();
                    dialog.error('AJAX错误');
                },
                success: function(data){
                    $('.loading').hide();
                    if(data){
                        $('ul.goods_list').html(data);
                        calculateTotalPrice();
                    }
                }
            });
        });
        //弹框关闭
        $('body').on('click','.closeBtn',function(){
            $('html,body').css({"overflow":"auto"});
            $('.mask').hide();
            $('.group_cart_toggle').hide();
            $('.express-area-box').css({bottom:'-100%',display:'none'});
            $('.mask').data('show',0);
        });
    });
</script>


<!--js基本插件-->
<script type="text/javascript" src="/Public/js/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="/Public/js/common/public.js"></script>
<script type="text/javascript" src="/Public/js/common/common.js"></script>
<script type="text/javascript" src="/Public/js/common/dialog.js"></script>
<script type="text/javascript" src="/Public/js/common/loginDialog.js"></script>

<script type="text/javascript">
    var MODULE = '/index.php/Mall';
    var ACTION = '/index.php/Mall/Index/index';
    var CONTROLLER = '/index.php/Mall/Index';
    var APP = '/index.php';
    var PUBLIC = '/Public';
    var ROOT = '';
    var UPLOAD = ROOT+'/Uploads/';
</script>


    <script type="text/javascript" src="/Public/js/common/swipe.js"></script>
    <script type="text/javascript" src="/Public/js/mall/goods.js"></script>
    <script type="text/javascript">
        var config = {
            pageSize:4,
            buyType:1,
            templateType:'photo'
        };
        $(function(){
            //初始化
            getGoodsList(config);
             //滑动轮播
            var elem = document.getElementById('slider');
                swipe(elem);
        });
    </script>

</body></html>
<?php /*a:6:{s:63:"D:\web\thinkphp5.1\mch\application/index/view\order\manage.html";i:1551065534;s:18:"template/base.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;s:25:"template/footer_menu.html";i:1551083825;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN"><head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('custom.title')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://mch.new.com/static/common/css/base.css">
	<link rel="stylesheet" href="http://mch.new.com/static/common/css/public.css">
	

	<script type="text/javascript" src="http://mch.new.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
		var domain = '<?php echo request()->domain(); ?>'+'/'+'index.php/';
		var module = domain + '<?php echo request()->module(); ?>/';
		var controller = module + '<?php echo request()->controller(); ?>/';
		var action = controller + '<?php echo request()->action(); ?>';
		var uploads = '<?php echo request()->domain(); ?>'+'/uploads/';
		var send_sms_url = '<?php echo url("ucenter/UserCenter/sendSms"); ?>';
		var walletPayCallBackParameter = {};
		var walletPayCallBack = function(parameter){
		};
	</script>
	<!--前置自定义js-->
	
</head><body>
<!-- 内容模块 -->

<!--订单状态标签-->
<section class="order_operate_btn-list" style="display:none;">
    <!--1:待付款-->
    <a href="javascript:void(0);" class="cancel_order">取消订单</a>
    <a href="<?php echo url('Order/toPay',['order_sn'=>$info['sn']]); ?>">去支付</a>

    <!--2:待收货-->
    <a href="javascript:void(0);" class="confirm_receive">确认收货</a>

    <!--3:待评价-->
    <a href="javascript:void(0);" class="apply_after_sales">申请售后</a>
    <a href="javascript:void(0);" class="to_evaluate">评价</a>

    <!--4:已完成-->
    <a href="javascript:void(0);" class="see_evaluation">查看评价</a>
    <a href="javascript:void(0);" class="purchase_again">再次购买</a>
    <!--5:已取消-->
    <a href="javascript:void(0);" class="order_canceled">已取消</a>
    <!--6:售后-->
    <a href="javascript:void(0);" class="">售后</a>
</section>

<article class="f24 ">
    <section class="header_title separation-line">
        <a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2 class="f24">我的订单</h2>
	</section>
    <section class="order_main_wrapper">
        <nav>
            <ul class="menu_nav">
                <li>
                    <a href="<?php echo url('order/manage'); ?>" data-order_status="0">全部</a>
                </li>
                <li>
                    <a href="<?php echo url('order/manage',['order_status'=>1]); ?>" data-order_status="1">待付款</a>
                </li>
                <li>
                    <a href="<?php echo url('order/manage',['order_status'=>2]); ?>" data-order_status="2">待收货</a>
                </li>
                <li>
                    <a href="<?php echo url('order/manage',['order_status'=>3]); ?>" data-order_status="3">待评价</a>
                </li>
                <li>
                    <a href="<?php echo url('order/manage',['order_status'=>6]); ?>" data-order_status="6">售后</a>
                </li>
                <li>
                    <a href="<?php echo url('order/manage',['order_status'=>4]); ?>" data-order_status="4">已完成</a>
                </li>

            </ul>
        </nav>
        <ul class="order_list list" id="list">

        </ul>
    </section>
</article>

<!-- 公共模块-->

<section id="dialLogin" style="display:none;">
    <form class="loginTab active" id="formLogin">
	<div class="login_item">
		<div class="columns_flex">
			<span>中国(+86)</span>
			<input class="username user_phone input-filed" type="tel" placeholder="请输入手机号码" name="mobile_phone">
		</div>
	</div>
	<div class="login_item">
		<div class="columns_flex">
			<input class="input-filed password" type="password" placeholder="密码" name="password">
			<a href="javascript:void(0);" class="hidden view-password" ></a>
		</div>
	</div>
	<a href="javascript:void(0);" class="forget_password forget_dialog">注册/重置密码</a>
	<div class="error_tipc"></div>
	<a href="javascript:void(0);" class="loginBtn entry-button"  data-method="login">登录</a>
</form>
</section>

<section id="sectionForgetPassword" style="display:none;">
    <form id="formForgetPassword">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="http://mch.new.com/static/common/img/ucenter_logo.png" alt="">
        </div>
        <div class="loginNav">
            <span class="current resetpassword">注册/重置密码</span>
        </div>
        <div class="error_tipc" ></div>
        <div class="login_item">
            <div class="columns_flex">
                <span>中国(+86)</span>
                <input class="username user_phone input-filed" type="tel" placeholder="请输入手机号码" name="mobile_phone">
            </div>
        </div>
        <div class="smsLogin login_wrap">
            <div class="login_item">
                <div class="columns_flex l-r-sides">
                    <input type="text" class="tel_code input-filed" placeholder="请输入收到的验证码" name="captcha">
                    <a href="javascript:void(0);" class="send_sms">获取验证码</a>
                </div>
            </div>
        </div>
        <div class="login_item">
            <div class="columns_flex">
                <input class="input-filed password" autocomplete="new-password" type="password" placeholder="设置密码" name="password">
                <a href="javascript:void(0);" class="hidden view-password" ></a>
            </div>
        </div>
    </div>
    <a href="javascript:void(0);" class="comfirmBtn entry-button"  data-method="forgetPassword">确定</a>
</form>
</section>

<!-- 页脚模块-->


<footer class="f24 group_cart_nav">
    <?php if(is_array($unlockingFooterCart['menu']) || $unlockingFooterCart['menu'] instanceof \think\Collection || $unlockingFooterCart['menu'] instanceof \think\Paginator): $i = 0; $__LIST__ = $unlockingFooterCart['menu'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($unlockingFooterCart['count'] == 1): ?>
            <div class="group_btn100 bottom_item">
                <?php if($vo['class']=='amount'): ?>
                <span>￥<price>500</price></span>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 2): ?>
            <div class="group_btn50 bottom_item">
                <?php if($vo['class']=='checked_all'): ?>
                <div class="select_checkbox_box <?php echo htmlentities($vo['class']); ?>">
                    <input type="checkbox" name="" value="" id="" class="item_checkbox checkall ">
                    <label for="" class="left">全选</label>
                </div>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 3): ?>
            <div class="group_btn30 bottom_item">
                <?php if($vo['class']=='amount'): ?>
                总计:<span class="amount">￥<price>0.00</price></span>
                <?php elseif($vo['class']=='add_cart_icon'): ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="add_num"></span><span class="cart_num"></span></a>
                <?php elseif($vo['class']=='checked_all'): ?>
                <div class="select_checkbox_box <?php echo htmlentities($vo['class']); ?>">
                    <input type="checkbox" name="" value="" id="" class="item_checkbox checkall ">
                    <label for="" class="left">全选</label>
                    <a href="javascript:void(0);" class="goodsDel detele_carts">删除</a>
                </div>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><?php echo htmlentities($vo['name']); ?><span class="total_num"></span></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 4): ?>
        <div class="group_btn25 bottom_item">
            <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
        </div>
        <?php else: endif; endforeach; endif; else: echo "" ;endif; ?>
</footer>

<!--js基本插件-->
<script type="text/javascript" src="http://mch.new.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

    <script type="text/javascript" src="http://mch.new.com/static/index/js/footerCart.js"></script>
    <script type="text/javascript">
        var config = {
            url:module+'Order/getList',
            requestEnd:false,
            loadTrigger:false,
            currentPage:1,

        };
        var postData = {
            pageSize:4,
            pageType:'list_tpl'
        };
        $(function(){
            var order_status=<?php echo json_encode($order_status); ?>;
            order_status=parseInt(order_status);
            if(isNaN(order_status)){
                $('.menu_nav li:first').addClass('current');
            }else {
                $.each($('.menu_nav li'),function () {
                    var li_order_status = $(this).find('a').data('order_status');
                    if(li_order_status==order_status){
                        $(this).addClass('current');
                    }
                });
            }

            if(order_status){
                postData.order_status = order_status;
            }
            getPagingList(config,postData);

            //下拉加载
            $(window).on('scroll',function(){
                if(config.loadTrigger && $(document).scrollTop()+$(window).height()+200>$(document).height()){
                    config.loadTrigger = false;
                    getPagingList(config,postData);
                }
            });

            //取消订单
            $('body').on('click','.cancel_order',function () {
                var _this = $(this);
                var _thisTr =_this.parents('li');
                var id = _thisTr.data('id');
                var postData ={
                    id:id,
                    order_status:5
                };
                var url = module+'Order/setOrderStatus';
                layer.open({
                    content:'确定取消订单？',
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
                                if(!data.status){
                                    dialog.error(data.info);
                                    return false;
                                }
                                if(data.status){
                                    _thisTr.remove();
                                }
                            }
                        });
                        layer.close(index);
                    }
                })
             });
            //确认收货
            $('body').on('click','.confirm_receive',function () {
                var _this = $(this);
                var _thisTr =_this.parents('li');
                var id = _thisTr.data('id');
                var postData ={
                    id:id,
                    order_status:3
                };
                var url = module+'Order/setOrderStatus';
                layer.open({
                    content:'确定收货？',
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
                                if(data.status){
                                    location.href =module +'Order/manage/order_status/'+ 3;
                                }
                            }
                        });
                        layer.close(index);
                    }
                })
            });
            //去评价
            $('body').on('click','.to_evaluate',function () {
                location.href =module +'Comment/index';
            });
            //查看评论
            $('body').on('click','.see_evaluation',function () {

            });
            //申请售后
            $('body').on('click','.apply_after_sales',function () {
                location.href =module +'AfterSale/index';
            });
        });
    </script>

</body></html>
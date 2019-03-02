<?php /*a:5:{s:69:"/home/www/web/thinkphp5.1/mch/application/index/view/cart/manage.html";i:1551238210;s:18:"template/base.html";i:1551077175;s:26:"template/login_dialog.html";i:1551077175;s:23:"template/login_tpl.html";i:1551077175;s:33:"template/forget_password_tpl.html";i:1551077175;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN"><head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('custom.title')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://mch.meishangyun.com/static/common/css/base.css">
	<link rel="stylesheet" href="http://mch.meishangyun.com/static/common/css/public.css">
	
	<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
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

<section class="no_cart_data" style="display:none;">
	<li class="no_data">
		<img src="https://www.worldview.com.cn/static/common/img/no-cart.png" alt="">
	</li>
</section>
<article class="f24">
	<section class="shopping_header_title header_title separation-line">
		<a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2>购物车</h2>
	</section>
	<ul class="cart_goods_list" id="list">
	</ul>
	<section id="no_data">
		<div class="cart_data_empty">
			<p>注：采购车里超过7天未结算的商品会被系统自动清理</p>
		</div>
	</section>
	<div class="project_wrapper">
		<div class="mod-part-title">
			<div class="mod-part-title-wrap">
				<span class="icon-title left"></span>
				<span class="title">新零售精选</span>
				<span class="icon-title right"></span>
			</div>
		</div>
		<div id="scroller-wrapper" class="list_wrapper">
			<ul class="columns_flex flex-both-side goods-content-list" id="list">

			</ul>
		</div>
	</div>
</article>
<section class="bottom_nav_fixed bottom_white">
	<nav class=" foot_nav_bar">
		<ul class="columns_flex">
			<li class="each_column">
				<a href="<?php echo url('Index/index'); ?>">
					<span class="store f_icon"></span>
					<span class="f_txt">美创会</span>
				</a>
			</li>
			<li class="each_column">
				<a href="<?php echo url('Company/index'); ?>">
					<span class="practitioners f_icon"></span>
					<span class="f_txt">中心店</span>
				</a>
			</li>
			<li class="each_column">
				<a href="<?php echo url('Consultation/index'); ?>">
					<span class="business f_icon"></span>
					<span class="f_txt">工作室</span>
				</a>
			</li>
			<li class="each_column current">
				<a href="javascript:void(0)" class="my_bottom_cart" data-jump_url="<?php echo url('Cart/manage'); ?>">
					<span class="cart f_icon"></span>
					<span class="f_txt">采购车</span>
				</a>
			</li>
			<li class="each_column ">
				<a href="<?php echo url('Mine/index'); ?>">
					<span class="my f_icon"></span>
					<span class="f_txt">我的</span>
				</a>
			</li>
		</ul>
	</nav>
</section>

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
            <img src="http://mch.meishangyun.com/static/common/img/ucenter_logo.png" alt="">
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

<!--js基本插件-->
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.meishangyun.com/static/index/js/footerCart.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/index/js/cart.js"></script>
<script type="text/javascript">
	var config = {
		url:module+'Cart/getList',
		requestEnd:false,
		loadTrigger:false,
		currentPage:1
	};
	var postData = {
		pageSize:20,
		pageType:'index'
	};
	$(function(){
		//初始化分类商品页面
		getPagingList(config,postData);
		//下拉加载
		$(window).on('scroll',function(){
			if(config.loadTrigger && $(document).scrollTop()+$(window).height()+200>$(document).height()){
				config.loadTrigger = false;
				getPagingList(config,postData);
			}
		});
	});
</script>

</body></html>
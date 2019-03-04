<?php /*a:5:{s:63:"D:\web\thinkphp5.1\mch\application/index/view\notice\index.html";i:1551065534;s:18:"template/base.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;}*/ ?>
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

<article class="f24 notice_wrapper">
	<section class="header_title separation-line" >
		<a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2 class="f24">品牌定制须知</h2>
	</section>
    <section class="notice_banner">
		<img src="http://mch.new.com/static/common/img/notice_01.jpg" alt="">
	</section>
    <section class="content_main weiya_content">
		<p>
			<img src="http://mch.new.com/static/common/img/notice_02.jpg" alt="">
		</p>
		<p>
			<img src="http://mch.new.com/static/common/img/notice_03.jpg" alt="">
		</p>
		<p>
			<img src="http://mch.new.com/static/common/img/notice_04.jpg" alt="">
		</p>
		<p>
			<img src="http://mch.new.com/static/common/img/notice_05.jpg" alt="">
		</p>
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

<!--js基本插件-->
<script type="text/javascript" src="http://mch.new.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.new.com/static/index/js/jump_login.js"></script>

</body></html>
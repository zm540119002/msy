<?php /*a:5:{s:76:"/home/www/web/thinkphp5.1/mch/application/index/view/consultation/index.html";i:1551064593;s:18:"template/base.html";i:1551077175;s:26:"template/login_dialog.html";i:1551077175;s:23:"template/login_tpl.html";i:1551077175;s:33:"template/forget_password_tpl.html";i:1551077175;}*/ ?>
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

<article class="f24 article_main_content">
	<section class="navigate">
		<ul class="nav_menu weiya_menu service_menu">
            <li class="current">
                <a href="<?php echo url('Consultation/index'); ?>">定制需求留言</a>
            </li>
            <li>
                <a href="<?php echo url('OnlineService/index'); ?>">在线客服咨询</a>
            </li>
        </ul>
	</section>
    <section class="content_main">
        <div class="weiya_item">
			<div class="content-padding msg_item msg_top_item">
				<div class="m_l c_mobile">
					<p>品牌定制顾问</p>
					<div class="c_m_l">
						<p><span class="name first f_n">朱田</span><a href="tel:13660613163">13059156679</a></p>
						<p><span class="name f_n">宫学莹</span><a href="tel:13711326631">13711326631</a></p>
					</div>
				</div>
				<div class="m_l c_mobile">
					<p>品牌定制客服</p>
					<div class="c_m_l">
						<p><span class="name first">叶雯雯</span><a href="tel:18820019781">18820019781</a></p>
						<p><span class="name">监督热线</span><a href="tel:18820019781">13710651399</a></p>
					</div>
				</div>
			</div>
            <div class="msg_content">
                <form class="message_form">
                    <div class="msg_item">
                        <input class="input-filed" type="text" name="name" value="" placeholder="你的姓名">
                    </div>
                    <div class="msg_item">
                        <input class="input-filed" type="tel" name="mobile" value="" placeholder="手机号码">
                    </div>
                    <div class="msg_item">
                        <textarea class="input-filed leaving_msg" name="message" rows="" cols="" placeholder="留言文字..."></textarea>
                    </div>
                </form>
                <a href="javascript:void(0);" class="submit_msg">递交留言</a>
            </div>
        </div>
    </section>
</article>
<section class="bottom_nav_fixed bottom_white">
	<nav class="foot_nav_bar">
		<ul class="columns_flex">
			<li class="each_column">
				<a href="<?php echo url('Index/index'); ?>">
					<span class="store f_icon"></span>
					<span class="f_txt">药妆定制</span>
				</a>
			</li>
			<li class="each_column">
				<a href="<?php echo url('Company/index'); ?>">
					<span class="practitioners f_icon"></span>
					<span class="f_txt">走进维雅</span>
				</a>
			</li>
			<li class="each_column current">
				<a href="<?php echo url('Consultation/index'); ?>">
					<span class="business f_icon"></span>
					<span class="f_txt">业务咨询</span>
				</a>
			</li>
			<li class="each_column">
				<a href="javascript:void(0)" class="my_cart" data-jump_url="<?php echo url('Cart/index'); ?>">
					<span class="cart f_icon"></span>
					<span class="f_txt">采购车</span>
				</a>
			</li>
			<li class="each_column">
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

<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/swiper.min.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/index/js/jump_login.js"></script>
<script type="text/javascript">
	$(function(){
		//查看文本
		$('.more-text-box').moreText({
			mainCell:".more-text",
			openBtn:'显示全部>'
		});
		//递交留言
		$('.submit_msg').on('click',function(){
			var postData=$('.message_form').serializeObject();
			var content='';
			if(!postData.name){
				content='请填写你的姓名';
			}else if(!register.phoneCheck(postData.mobile)){
				content='请填写正确的手机号码';
			}else if(!postData.message){
				content='请填写你的留言';
			}
			if(content){
				dialog.error(content);
				return false;
			}
			var url = module+'Consultation/addNeedsMessage';
			$.ajax({
				type:'post',
				url:url,
				data:postData,
				success:function(data){
					 if(data.status){
						 dialog.success(data.info);
						 setTimeout(function(){
							var url =  module + 'Consultation/index';
							location.href = url;
						 },3000);
						 //return false;
					 }
					dialog.error(data.info);
				},
				error:function(xhr){
					dialog.error(xhr);
				}
			});
		});

	});
</script>

</body></html>
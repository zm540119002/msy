<?php /*a:6:{s:75:"/home/www/web/thinkphp5.1/mch/application/index/view/information/index.html";i:1551064593;s:18:"template/base.html";i:1551064594;s:25:"template/footer_menu.html";i:1551064594;s:26:"template/login_dialog.html";i:1551064594;s:23:"template/login_tpl.html";i:1551064594;s:33:"template/forget_password_tpl.html";i:1551064594;}*/ ?>
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

<article class="f24">
	<section class="header_title separation-line">
		<a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2 class="f24">维雅资讯</h2>
	</section>
	<section class="content_main weiya_content">
		<div class="weiya_item ">
			<ul id="list" class="info_list">

			</ul>
		</div>
	</section>
</article>
<div class="big_img">
	<section class="enlarge_img">
		<div class="swiper-container swiper-container-horizontal swiper-container-ios">
			<div class="swiper-wrapper">
				
			</div>
			<div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets">
				
			</div>
		</div>      
	</section>
</div>

<!-- 公共模块-->


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
<script type="text/javascript">
	var config = {
		url:module+'Information/getList',
		requestEnd:false,
		loadTrigger:false,
		currentPage:1,

	};
	var postData = {
		pageSize:10,
		pageType:'list_tpl'
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
		//查看文本
		$('.more-text-box').moreText({
			mainCell:".more-text",
			openBtn:'显示全部>'
		});
		//放大图片
		$("#list").on("click", ".info_img img",function() {
			var imgBox = $(this).parents(".info_img").find("img");
			var i = $(imgBox).index(this);
			console.log(i);
			$(".big_img .swiper-wrapper").html("");
			console.log(imgBox.length);
			for(var j = 0 ,c = imgBox.length; j < c ;j++){
				console.log(imgBox.eq(j).attr("src"));
				$(".big_img .swiper-wrapper").append('<div class="swiper-slide"><img src="' + imgBox.eq(j).attr("src") + '" / ></div>');
			}			
			// mySwiper.updateSlidesSize();swiper-slide-active
			// mySwiper.updatePagination();
			$(".big_img").css({
				"z-index": 1001,
				"opacity": "1"
			});
			//mySwiper.slideTo(i, 0, false);
			/*调起大图*/
			var mySwiper = new Swiper('.swiper-container', {
				// loop:true,
				pagination: {
					el: '.swiper-pagination',
					clickable: true
				}
			})
			$(".big_img .swiper-wrapper").find('div').eq(0).removeClass('swiper-slide-active');
			$(".big_img .swiper-wrapper").find('div').eq(1).removeClass('swiper-slide-next');
			$(".big_img .swiper-wrapper").find('div').eq(i).addClass('swiper-slide-active');
			$(".big_img .swiper-wrapper").find('div').eq(i+1).addClass('swiper-slide-next');
			return false;
		});
		$(".big_img").on("click",function() {
			$('.swiper-wrapper').find('.swiper-slide').remove();
			$('.swiper-pagination').find('.swiper-pagination-bullet').remove();
			$('.swiper-notification').remove();
			$(this).css({
				"z-index": "-1",
				"opacity": "0"
			});

		});  
	});
</script>

</body></html>
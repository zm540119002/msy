<?php /*a:5:{s:62:"D:\web\thinkphp5.1\mch\application/index/view\index\index.html";i:1551336573;s:18:"template/base.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;}*/ ?>
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

<article class="f24 content_main">
	<section class="fixedtop top_nav_fixed">
		<div class="top_bar columns_flex l-r-sides">
			<div class="each_column">
				<a class="home top_hidden">首页</a>
			</div>
			<div class="each_column top_search_module">
				<input type="button" class="search_btn"/>
				<input type="text" class="search_text" placeholder="产品"/>
			</div>
			<div class="each_column">
				<a class="shopping_cart top_icon">购物车</a>
			</div>
			<div class="each_column">
				<a class="personal_center top_icon">我的</a>
			</div>
		</div>
	</section>
	<section class="slider_banner">
		<div class="swiper-container swiper-container-horizontal swiper-container-ios">
			<div class="swiper-wrapper">
				<div class="swiper-slide swiper-slide-active">
					<img src="http://mch.new.com/static/common/img/banner/mch_banner.jpg" alt="" class="common_default_img">
				</div>
				<div class="swiper-slide ">
					<img src="http://mch.new.com/static/common/img/banner/mch_banner.jpg" alt="" class="common_default_img">
				</div>
				<div class="swiper-slide ">
					<img src="http://mch.new.com/static/common/img/banner/mch_banner.jpg" alt="" class="common_default_img">
				</div>
				<div class="swiper-slide ">
					<img src="http://mch.new.com/static/common/img/banner/mch_banner.jpg" alt="" class="common_default_img">
				</div>
			</div>
			<div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets">
				<span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button"
                      aria-label="Go to slide 1"></span>
				<span class="swiper-pagination-bullet " tabindex="0" role="button" aria-label="Go to slide 2"></span>
				<span class="swiper-pagination-bul let " tabindex="0" role="button" aria-label="Go to slide 3"></span>
				<span class="swiper-pagination-bullet " tabindex="0" role="button" aria-label="Go to slide 4"></span>
			</div>
		</div>
	</section>
	<section class="module-navigation separation-line ">
		<div class="columns_flex f20 channel_nav store_organize_nav">
			<a href="" class="deployed-deployment"><span class="union_purchase"></span>美创商城</a>
			<a href="javascript:void(0);" class="store_run"><span class="exclusive"></span>全国美创会</a>
			<a href="<?php echo url('store/Operation/index'); ?>" class="order-management"><span class="classify"></span>创业导师</a>
			<a href="<?php echo url('store/Order/index'); ?>" class="order-management-change"><span class="purchasers"></span>创业美容师</a>
			<a href="javascript:void(0);" class="my-store"><span class="recharge"></span>平台合伙人</a>
		</div>
	</section>
	<section class="">
		<div class="content-padding">
			<div>
				<div class="scroll_news">
					<a class="news_tit">
						美创会头条
						<!--<img src="http://mch.new.com/static/common/img/wy_logo.png" alt="">-->
					</a>
					<div class="news_list_wrapper">
						<ul class="news_list j_scroll_news">
							<li class="news_item">
								<a>
									<span class="red">最新</span>维雅品牌定制平台正式上线啦！
								</a>
							</li>
							<li class="news_item">
								<a>
									<span class="red">hot</span>利润为王,品牌定制是捷径！
								</a>
							</li>
						</ul>
					</div>
					<a class="news_more" href="javascript:void(0)">更多</a>
				</div>
			</div>
			<!--优惠广告入口-->
			<section class="slider_banner">
				<div class="swiper-container swiper-container-horizontal swiper-container-ios">
					<div class="swiper-wrapper">
						<div class="swiper-slide swiper-slide-active">
							<a href="">
								<img src="http://mch.new.com/static/common/img/banner/xianshi.jpg" alt="" class="common_default_img">
							</a>
						</div>
						<div class="swiper-slide ">
							<a href="">
								<img src="http://mch.new.com/static/common/img/banner/xianshi.jpg" alt="" class="common_default_img">
							</a>
						</div>
						<div class="swiper-slide ">
							<a href="">
								<img src="http://mch.new.com/static/common/img/banner/xianshi.jpg" alt="" class="common_default_img">
							</a>
						</div>
					</div>
					<div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets">
						<span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button"
							aria-label="Go to slide 1"></span>
						<span class="swiper-pagination-bullet " tabindex="0" role="button" aria-label="Go to slide 2"></span>
						<span class="swiper-pagination-bul let " tabindex="0" role="button" aria-label="Go to slide 3"></span>
						<span class="swiper-pagination-bullet " tabindex="0" role="button" aria-label="Go to slide 4"></span>
					</div>
				</div>
			</section>
			<!--优惠广告入口end-->
			<div class="scene_wrapper">
				<!--<?php if(is_array($sceneList) || $sceneList instanceof \think\Collection || $sceneList instanceof \think\Paginator): $i = 0; $__LIST__ = $sceneList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
				<div class="graphic_item graphic_item_65 img<?php echo htmlentities($key); ?>" >
					<a href="<?php echo url('Scene/detail',['id'=>$info['id']]); ?>"  data-id="<?php echo htmlentities($info['id']); ?>">
						<div class="show-img">
							<?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
							<img src="http://mch.new.com/static/common/img/default/no_pic_100.jpg" alt="">
							<?php else: ?>
							<img src="http://mch.new.com/uploads/<?php echo htmlentities($info['thumb_img']); ?>" alt="">
							<?php endif; ?>
						</div>
					</a>
				</div>
				<?php endforeach; endif; else: echo "" ;endif; ?>-->
				<div class="columns_flex l-r-sides">
					<a href="<?php echo url('Scene/jiameng'); ?>" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/jiameng.jpg" alt="">
					</a>
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/yiqi.jpg" alt="">
					</a>	
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/kaidian.jpg" alt="">
					</a>
				</div>
				<div class="columns_flex l-r-sides">
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/qs.jpg" alt="">
					</a>	
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/lk.jpg" alt="">
					</a>
				</div>
				<div class="columns_flex l-r-sides">
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/sk.jpg" alt="">
					</a>	
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/hl.jpg" alt="">
					</a>
				</div>
				<div class="columns_flex l-r-sides">
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/xly.jpg" alt="">
					</a>	
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/xlr.jpg" alt="">
					</a>
				</div>
				<div class="columns_flex l-r-sides">
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/jujia.jpg" alt="">
					</a>	
					<a href="" class="graphic_item">
						<img src="http://mch.new.com/static/common/img/wz.jpg" alt="">
					</a>
				</div>
			</div>
			<div class="project_wrapper">
				<div class="mod-part-title">
					<div class="mod-part-title-wrap">
						<span class="icon-title left"></span>
						<span class="title">科技美肤精选</span>
						<span class="icon-title right"></span>
					</div>
				</div>
				<div id="scroller-wrapper" class="list_wrapper">
					<ul class="columns_flex flex-both-side goods-content-list" id="list">
						
					</ul>
				</div>
			</div>
		</div>
	</section>
</article>
<section class="bottom_nav_fixed bottom_white">
	<nav class=" foot_nav_bar">
		<ul class="columns_flex">
			<li class="each_column current">
				<a href="javascript:void(0)">
					<span class="store f_icon"></span>
					<span class="f_txt">美创会</span>
				</a>
			</li>
			<li class="each_column">
				<!--<?php echo url('Company/index'); ?>-->
				<a href="">
					<span class="practitioners f_icon"></span>
					<span class="f_txt">中心店</span>
				</a>
			</li>
			<li class="each_column">
				<!--<?php echo url('Consultation/index'); ?>-->
				<a href="">
					<span class="business f_icon"></span>
					<span class="f_txt">工作室</span>
				</a>
			</li>
			<li class="each_column ">
				<a href="javascript:void(0)" class="my_bottom_cart" data-jump_url="<?php echo url('Cart/manage'); ?>">
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

<script type="text/javascript" src="http://mch.new.com/static/common/js/swiper.min.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/index/js/jump_login.js"></script>
<script type="text/javascript">
	var config = {
		url:module+'Goods/getList',
		requestEnd:false,
		loadTrigger:false,
		currentPage:1
	};
	var postData = {
		pageSize:10,
		pageType:'index'
	};
	$(function(){
		//轮播
		var swiper = new Swiper('.swiper-container', {
			spaceBetween: 30,
			autoplay:true,
			pagination: {
				el: '.swiper-pagination',
				clickable: true
			}
		});
		//新闻滚动
		var len=$('.news_list li').length;
		var timer=setInterval(function(){
			var i=0;
			i++;
			move(i);
		},2200);
		function move(i){
			var h=$('.news_item').height();
			var offsetY=h*i;
			$('.news_list').get(0).style.transform="translate3d(0,-"+offsetY+"px,0)";
			$('.news_list').get(0).style.transition="transform 500ms ease-in-out 0s";
			setTimeout(function(){
				$('.news_list').get(0).style.transform="translate3d(0,0,0)";
				$('.news_list').get(0).style.transition="none 0s ease 0s";
				$('.news_list li').eq(len-1).after($('.news_list li').eq(0));
			},500);
		}
		//初始化分类商品页面
		$('.nav_menu li:eq(0)').addClass('current');
		postData.category_id = $('.nav_menu li.current').data('category-id');
		getPagingList(config,postData);

		$('body').on('click','.nav_menu li',function(){
			var _this = $(this);
			$(this).addClass('current').siblings().removeClass('current');
			config = {
				url:module+'Goods/getList',
				requestEnd:false,
				loadTrigger:false,
				currentPage:1
			};
			postData.category_id = _this.data('category-id');
			getPagingList(config,postData);
		});

		//下拉加载
		//var offsetHeight=$('.nav_menu').offset().top;
		$(window).on('scroll',function(){
			if(config.loadTrigger && $(document).scrollTop()+$(window).height()+200>$(document).height()){
				config.loadTrigger = false;
				postData.category_id = $('.nav_menu li.current').data('category-id');
				getPagingList(config,postData);
			}
			//滚动
			// var top=$(document).scrollTop();
			// if(top>offsetHeight){
			// 	$('.nav_menu').addClass('top-fixed');
			// }else {
			// 	$('.nav_menu').removeClass('top-fixed');
			// }
		});

		$('body').on('click','.my_cart',function () {
			var url = module + 'Cart/index';
			var postData = {};
			$.ajax({
				url: url,
				data: postData,
				type: 'post',
				beforeSend: function(xhr){
					$('.loading').show();
				},
				error:function(xhr){
					$('.loading').hide();
					dialog.error('AJAX错误！');
				},
				success: function(data){

					$('.loading').hide();
					if(data.status==0){
						dialog.error(data.info);
					}else if(data.code==1){
						if(data.data == 'no_login'){
							loginDialog();
						}
					}else if(data.status==1){

					}else{
						location.href = url;
					}
				}
			});
		});
		/**
		 *查看更多资讯
		 */
		$('body').on('click','.news_more',function () {
			location.href = module + 'Information/index';
		})
	});
</script>

</body></html>
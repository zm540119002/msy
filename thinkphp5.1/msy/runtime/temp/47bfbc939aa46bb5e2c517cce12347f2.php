<?php /*a:3:{s:62:"E:\web\thinkphp5.1\msy\application/index/view\index\index.html";i:1520499518;s:62:"E:\web\thinkphp5.1\msy\application/index/view\public\base.html";i:1520499518;s:70:"E:\web\thinkphp5.1\msy\application/index/view\public\rightSideBar.html";i:1520413477;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('WEB_NAME')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="PUBLIC_CSS/common/public.css">
	<link rel="stylesheet" href="PUBLIC_CSS/common/weui.css">
	
<link rel="stylesheet" type="text/css" href="PUBLIC_CSS/home/mall.css">


	<script type="text/javascript" src="http://msy.new.com/public/static/index/js/1.9.1/jquery.min.js"></script>
</head>

<body>
<!-- 基础内容模块，子页面可覆盖 -->

<article class="f24">
	<section class="top_nav_fixed">
		<div class="top_bar columns_flex">
			<div class="each_column">
				<a class="personal_center">我的</a>
			</div>
			<div class="each_column top_search_module">
				<input type="button" class="search_btn"/>
				<input type="text" class="search_text" placeholder="美尚云搜索"/>
			</div>
			<div class="each_column">
				<a class="shopping_cart">购物车</a>
			</div>
			<!--<div class="each_column">
               <a>首页</a>
            </div>-->
		</div>
		<div class="top_menu_box">
			<div class="top_menu_list">
				<a href="" class="current">厂商</a>
				<a href="">培训</a>
				<a href="" >美尚会</a>
			</div>
			<!--<div class="top_person_center">
                <a href="">我的</a>
            </div>-->
		</div>
	</section>
	<section class="slider_banner">
		<div id="slider" >
			<div class='swipe-wrap'>
				<div><img src="Public/img/common/banner/home-banner1.jpg" /></div>
				<div><img src="Public/img/common/banner/home-banner1.jpg" /></div>
				<div><img src="Public/img/common/banner/home-banner1.jpg" /></div>
				<div><img src="Public/img/common/banner/home-banner1.jpg" /></div>
			</div>
			<ul class="position">
				<li class="on"></li>
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>
	</section>
	<section class="bottomLine">
		<nav class="columns_flex channel_nav manufacturer_nav">
			<a href="" class="current">
				<span class="union_purchase"></span>
				入驻部署
			</a>
			<a href="">
				<span class="exclusive"></span>
				采购运营
			</a>
			<a href="">
				<span class="classify"></span>
				分成运营
			</a>
			<a href="">
				<span class="purchasers"></span>
				新零售运营
			</a>
		</nav>
	</section>
	<section class="bottom_nav_fixed">
		<nav class=" foot_nav_bar">
			<ul class="columns_flex">
				<li class="each_column ">
					<a>
						<span class="store"></span>
						店家
					</a>
				</li>
				<li class="each_column">
					<a>
						<span class="practitioners"></span>
						从业人员
					</a>
				</li>
				<li class="each_column current">
					<a>
						<span class="manufacturer"></span>
						厂商
					</a>
				</li>
			</ul>
		</nav>
	</section>
</article>

<!-- 返回顶部-->

<div class="right_sidebar">
    <a href="<?php echo url('index/Index/index'); ?>" class="f24">首页</a>
    <a href="javascript:void(0);" class="f24 backTop">顶部</a>
</div>

<!-- 加载更多-->



<!-- 基础页脚模块，子页面可覆盖 -->


<!--js基本插件-->
<script type="text/javascript" src="PUBLIC_JS/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/public.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/common.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/dialog.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/loginDialog.js"></script>


<script type="text/javascript" src="PUBLIC_JS/common/swipe.js"></script>
<script type="text/javascript">
	$(function(){
		//滑动轮播
		var elem = document.getElementById('slider');
		swipe(elem);
	});
</script>

</body></html>
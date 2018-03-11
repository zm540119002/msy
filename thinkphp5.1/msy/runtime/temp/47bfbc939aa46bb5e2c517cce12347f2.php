<?php /*a:3:{s:62:"E:\web\thinkphp5.1\msy\application/index/view\index\index.html";i:1520734358;s:62:"E:\web\thinkphp5.1\msy\application/index/view\public\base.html";i:1520734358;s:70:"E:\web\thinkphp5.1\msy\application/index/view\public\rightSideBar.html";i:1520734358;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('WEB_NAME')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://msy.new.com/static/common/css/base.css">
	<link rel="stylesheet" href="http://msy.new.com/static/common/css/public.css">
	


	<script type="text/javascript" src="http://msy.new.com/static/common/js/jquery-1.9.1.min.js"></script>

</head>

<body>
<!-- 基础内容模块，子页面可覆盖 -->

<article class="f24">
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
<!--
<div class="right_sidebar">
    <a href="<?php echo url('index/Index/index'); ?>" class="f24">首页</a>
    <a href="javascript:void(0);" class="f24 backTop">顶部</a>
</div>
-->
<!-- 加载更多-->



<!-- 基础页脚模块，子页面可覆盖 -->


<!--js基本插件-->
<script type="text/javascript" src="http://msy.new.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://msy.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://msy.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://msy.new.com/static/common/js/dialog.js"></script>
<script type="text/javascript" src="http://msy.new.com/static/common/js/loginDialog.js"></script>


<script type="text/javascript" src="http://msy.new.com/static/common/js/swipe.js"></script>
<script type="text/javascript">
	$(function(){
		//滑动轮播
		var elem = document.getElementById('slider');
		swipe(elem);
	});
</script>

</body></html>
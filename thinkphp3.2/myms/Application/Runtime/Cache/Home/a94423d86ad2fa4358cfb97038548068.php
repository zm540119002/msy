<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo (C("WEB_NAME")); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/Public/css/common/public.css">
    <link rel="stylesheet" href="/Public/css/common/weui.css">
    <script type="text/javascript" src="/Public/js/common/jquery-1.9.1.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/Public/css/common/style.css">

</head>

<body>
<!-- 基础导航模块，子页面可覆盖 -->

<div class="header">
    <a class="back" href="javascript:history.go(-1)"></a>
    <?php echo (C("WEB_NAME")); ?>
    <a href="<?php echo U('Index/index');?>" class="set"></a>
</div>


<div class="top_space"></div>

<!-- 基础内容模块，子页面可覆盖 -->

    <!-- S= 轮播图 -->
    <div id="slider">
        <div class='swipe-wrap'>
            <div><img src="/Public/img/common/icon/m1.jpg" /></div>
            <div><img src="/Public/img/common/icon/m2.jpg" /></div>
            <div><img src="/Public/img/common/icon/m3.jpg" /></div>
            <div><img src="/Public/img/common/icon/m4.jpg" /></div>
        </div>
        <ul class="position">
            <li class="on"></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <!-- E= 轮播图 -->
    <!-- 采购平台 -->
    <div class="content_box">
        <div class="title">[众创空间]</div>
        <div class="content_wrap">
            <div class="cgLeft"><a href="http://msy.meishangyun.com/" class="aText">美创会</a></div>
            <div class="cgRight cgPxy"><a href="javascript:void(0);" class="aText">培训学院</a></div>
            <div class="cgMiddle cgMyms"><a href="<?php echo U('Mall/Index/index');?>" class="aText">美妍美社</a></div>

            <div class="clear"></div>
        </div>
    </div>
    <!-- 卖手平台 -->
    <div class="content_box">
        <div class="title">[采购平台]</div>
        <div class="content_wrap">
            <div class="fcLeft"><a href="javascript:void(0);" class="aText">采购商城</a></div>
            <div class="fcRight fcRecharge"><a href="javascript:void(0);" class="aText">账户充值</a></div>
            <div class="fcMiddle fcOrder"><a href="javascript:void(0);" class="aText">采购订单</a></div>
            <div class="clear"></div>
        </div>
    </div>
    <!-- 智能运营 -->
    <div class="content_box">
        <div class="title">[分成平台]</div>
        <div class="content_wrap">
            <div class="znLeft znSet"><a href="javascript:void(0);" class="aText">社长设置</a></div>
            <div class="znRight znShare"><a href="javascript:void(0);" class="aText">远程支持</a></div>
            <div class="znMiddle znManage"><a href="javascript:void(0);" class="aText">订单管理</a></div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="footer_space"></div>


<!-- 基础页脚模块，子页面可覆盖 -->

    <footer class="f24">
        <span class="icp_icon">Copyright 2017 维雅生物科技 粤ICP备14021421号</span>
    </footer>


<!--js基本插件-->
<script type="text/javascript" src="/Public/js/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="/Public/js/common/swipe.js"></script>
<script type="text/javascript" src="/Public/js/common/public.js"></script>
<script type="text/javascript" src="/Public/js/common/common.js"></script>
<script type="text/javascript" src="/Public/js/common/dialog.js"></script>
<script type="text/javascript" src="/Public/js/common/loginDialog.js"></script>

<script type="text/javascript">
    var MODULE = '/index.php/Home';
    var ACTION = '/index.php/Home/Index/studio';
    var CONTROLLER = '/index.php/Home/Index';
    var APP = '/index.php';
    var PUBLIC = '/Public';
    var ROOT = '';
    var UPLOAD = ROOT+'/Uploads/';
</script>


    <script type="text/javascript" src='/Public/js/common/swipe.js'></script>
    <!-- 图片轮播JS -->
    <script type="text/javascript">
        var elem = document.getElementById('slider');
        window.mySwipe = Swipe(elem, {
            auto: 1800,
            callback: function(index,element){
                $(".position li").eq(index).addClass("on").siblings().removeClass("on");
            }
        });
        $(".position li").click(
            function () {
                mySwipe.slide($(this).index());
            }
        );
    </script>






</body></html>
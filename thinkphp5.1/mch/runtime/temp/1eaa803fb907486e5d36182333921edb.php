<?php /*a:1:{s:83:"/home/www/web/thinkphp5.1/mch/application/ucenter/view/user_center/login_admin.html";i:1551064593;}*/ ?>
﻿<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/html5shiv.js"></script>
    <script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui.admin/css/chat.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui/css/H-ui.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui.lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="http://mch.meishangyun.com/static/h-ui.admin/css/style.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.meishangyun.com/static/index_admin/css/user.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <title>维雅后台登录</title>
</head><body>
<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"></div>
<div class="weiya_logo">
    <img src="http://mch.meishangyun.com/static/index_admin/img/weiya_logo.png" alt="">
</div>
<div class="loginWraper">
    <div class="loginBox">
        <h2 class="welcome">欢迎登录</h2>
        <form class="form form-horizontal" id="formLogin">
            <div class="row cl">
                <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
                <div class="formControls col-xs-8">
                    <input name="mobile_phone" type="text" placeholder="手机号码" class="input-text size-L">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
                <div class="formControls col-xs-8">
                    <input name="password" type="password" placeholder="密码" class="input-text size-L">
                </div>
            </div>
            <div class="row row-item cl">
                <div>
                    <input type="checkbox"><label>记住密码</label>
                </div>
                <div>
                    <a href="javascript:void(0);">忘记密码?</a>
                </div>
            </div>
            <div class="row cl">
                <div class="formControls col-xs-8 col-xs-offset-3">
                    <a href="javascript:void(0);" class="loginBtn entry-button"  data-method="login_admin">登录</a>
                </div>
            </div>
        </form>
    </div>
</div>
<footer class="f24 bottom_footer">
    <a href="http://www.miitbeian.gov.cn">
        <span class="icp_icon">Copyright@广东美尚网络科技有限公司</span>
        <span class="icp_icon">粤ICP备18130945号-1</span>
    </a>
</footer>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.admin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/jquery.contextmenu/jquery.contextmenu.r2.js"></script>
<!--自定义js-->
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript">
    var domain = '<?php echo request()->domain(); ?>'+'/'+'index.php/';
    var module = domain + '<?php echo request()->module(); ?>/';
    var controller = module + '<?php echo request()->controller(); ?>/';
    var action = controller + '<?php echo request()->action(); ?>';
    var uploads = '<?php echo request()->domain(); ?>'+'/uploads/';
    $(function(){
    });
</script>
</body></html>
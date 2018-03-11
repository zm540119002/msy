<?php /*a:2:{s:68:"E:\web\thinkphp5.1\msy\application/store_admin/view\login\index.html";i:1520739249;s:68:"E:\web\thinkphp5.1\msy\application/store_admin/view\Public\base.html";i:1520738368;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('WEB_NAME')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://msy.dev/static/common/css/common/public.css">
	
<link rel="stylesheet" href="public/static/admin/common/css/user.css" type="text/css"/>

	<script type="text/javascript" src="http://msy.dev/static/common/js/common/jquery-1.9.1.min.js"></script>
</head>

<body>
<!-- 基础内容模块，子页面可覆盖 -->

<article class="msy-bg">
    <section class="userInfoWrapper">
        <img src="http://msy.dev/static/common/img/common/logo.png" alt="" class="msy-logo">
        <h2 class="tc">用户登录</h2>
        <div class="">
            <ul class="loginNav">
                <li class="current">账号密码登录</li>
                <li>短信验证码登陆</li>
            </ul>
            <form class="loginTab" id="formLogin">
                <div class="userLogin login_wrap active">
                    <div class="login_item">
                        <span class="username_txt">手机号</span>
                        <input class="username user_name" type="text" placeholder="请输入用户名(6-16位数字或者字母、下划线)" name="name">
                    </div>
                    <div class="login_item">
                        <span class="password_txt">密码</span>
                        <input class="username password" type="password" placeholder="请输入密码(输入6-16数字或字母的密码)" name="password">
                    </div>
                </div>
                <div class="smsLogin login_wrap hide">
                    <div class="login_item">
                        <span class="username_txt">手机号</span>
                        <input class="username user_phone" type="tel" placeholder="请输入手机号" name="mobile_phone">
                        <a href="javascript:void(0);" class="mesg_code">获取验证码</a>
                    </div>
                    <div class="login_item">
                        <span class="username_txt txt3">验证码</span>
                        <input type="text" class="tel_code username input_txt3" placeholder="请输入收到的验证码(6位数字)" name="captcha">
                    </div>
                </div>
                <input type="hidden" name="captcha_type" value="login" />
            </form>
            <a href="javascript:void(0);" class="loginBtn">登录</a>
            <div class="friend_tipc">
                <a href="__CONTROLLER__/register" class="register_dialog">注册</a>
                <a href="__CONTROLLER__/forget_password" class="forget_dialog">忘记密码？</a>
            </div>
        </div>
    </section>
</article>

<!-- 返回顶部-->

<!-- 加载更多-->



<!-- 基础页脚模块，子页面可覆盖 -->

<footer class="f24">
	<span class="icp_icon">Copyright 2017 维雅生物科技 粤ICP备14021421号</span>
</footer>


<!--js基本插件-->
<script type="text/javascript" src="http://msy.dev/static/common/js/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://msy.dev/static/common/js/common/public.js"></script>
<script type="text/javascript" src="http://msy.dev/static/common/js/common/common.js"></script>
<script type="text/javascript" src="http://msy.dev/static/common/js/common/dialog.js"></script>
<script type="text/javascript" src="http://msy.dev/static/common/js/common/loginDialog.js"></script>


<script type="text/javascript" src="public/static/admin/common/js/login.js"></script>
<script type="text/javascript">
    // 刷新验证码
    function reloadcode(obj) {
        obj.src = "<?php echo url('Admin/User/verify'); ?>";
    }
</script>

</body></html>
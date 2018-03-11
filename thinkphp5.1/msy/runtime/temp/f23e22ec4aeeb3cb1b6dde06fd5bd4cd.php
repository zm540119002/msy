<?php /*a:2:{s:68:"E:\web\thinkphp5.1\msy\application/store_admin/view\login\index.html";i:1520732659;s:68:"E:\web\thinkphp5.1\msy\application/store_admin/view\Public\base.html";i:1520494701;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('WEB_NAME')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="PUBLIC_CSS/common/public.css">
	<link rel="stylesheet" href="PUBLIC_CSS/common/weui.css">
	

  <link href="http://msy.dev/static/hadmin/static/h-ui.admin/css/H-ui.login.css" rel="stylesheet" type="text/css" />


	<script type="text/javascript" src="PUBLIC_JS/common/jquery-1.9.1.min.js"></script>
</head>

<body>
<!-- 基础内容模块，子页面可覆盖 -->

  <input type="hidden" id="TenantId" name="TenantId" value="" />
  <div class="header"></div>
  <div class="loginWraper">
    <div id="loginform" class="loginBox">
      <form class="form form-horizontal" action="<?php echo url('User/login'); ?>" method="post">
        <div class="row cl">
          <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
          <div class="formControls col-xs-8">
            <input id="" name="name" type="text" placeholder="账户" class="input-text size-L">
          </div>
        </div>
        <div class="row cl">
          <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
          <div class="formControls col-xs-8">
            <input id="" name="password" type="password" placeholder="密码" class="input-text size-L">
          </div>
        </div>
        <div class="row cl">
          <div class="formControls col-xs-8 col-xs-offset-3">
            <input class="input-text size-L" type="text" placeholder="验证码"  name="captcha" value="" style="width:150px;">
            <img src="<?php echo url('Admin/User/verify'); ?>" alt="验证码" onclick="reloadcode(this)" title="点击刷新"/>
          </div>
        </div>

        <div class="row cl">
          <div class="formControls col-xs-8 col-xs-offset-3">
            <label for="online">
              <input type="checkbox" name="online" id="online" value="">
              使我保持登录状态</label>
          </div>
        </div>
        <div class="row cl">
          <div class="formControls col-xs-8 col-xs-offset-3">
            <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
            <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
          </div>
        </div>
      </form>
    </div>
  </div>


<!-- 返回顶部-->

<!-- 加载更多-->



<!-- 基础页脚模块，子页面可覆盖 -->

<footer class="f24">
	<span class="icp_icon">Copyright 2017 维雅生物科技 粤ICP备14021421号</span>
</footer>


<!--js基本插件-->
<script type="text/javascript" src="PUBLIC_JS/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/public.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/common.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/dialog.js"></script>
<script type="text/javascript" src="PUBLIC_JS/common/loginDialog.js"></script>


  <script type="text/javascript">
    // 刷新验证码
    function reloadcode(obj) {
      obj.src="<?php echo url('Admin/User/verify'); ?>";
    }
  </script>

</body></html>
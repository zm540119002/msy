<?php /*a:2:{s:62:"E:\web\thinkphp5.1\msy\application/admin/view\index\index.html";i:1520408133;s:62:"E:\web\thinkphp5.1\msy\application/admin/view\public\base.html";i:1520408704;}*/ ?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{block name="title"><?php echo htmlentities(app('config')->get('WEB_NAME')); ?></block></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/h-ui.admin/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/h-ui.admin/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/h-ui.admin/lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/h-ui.admin/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/h-ui.admin/static/h-ui.admin/css/style.css" />
    <link type="text/css" rel="stylesheet" href="PUBLIC_ADMIN_CSS/common/public.css" />
    {block name="css-customize"}

    </block>
</head>

<body>
<!--基础导航模块，子页面可覆盖-->
{block name="nav"}</block>

<div class="top_space"></div>

<!-- 基础内容模块，子页面可覆盖 -->
{block name="content"}</block>

<!-- 基础页脚模块，子页面可覆盖 -->
{block name="footer"}</block>

<!------js基本插件-------->
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/jquery-1.11.3.min.js" ></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/jquery.validate.min.js"></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/common.js" charset="utf-8"></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/global.js"></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/layer/layer.js"></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/dialog.js"></script>
<script type="text/javascript" src="PUBLIC_ADMIN_JS/common/public.js"></script>
<script type="text/javascript" src="__PUBLIC__/h-ui.admin/lib/html5shiv.js"></script>
<script type="text/javascript" src="__PUBLIC__/h-ui.admin/lib/respond.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/h-ui.admin/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/h-ui.admin/static/h-ui.admin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="__PUBLIC__/h-ui.admin/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>

<script type="text/javascript">
    var ROOT = '__ROOT__';
    var MODULE = '__MODULE__';
    var ACTION = '__ACTION__';
    var CONTROLLER = '__CONTROLLER__';
    var APP = '__APP__';
    var PUBLIC = '__PUBLIC__';
    var UPLOAD = ROOT +'/Uploads/';
    var TEMP = UPLOAD + 'temp/';
</script>

<literal>
    {block name="js-customize"></block>
</literal>

{block name="script"}</block>

</body></html>
<?php /*a:2:{s:70:"D:\web\thinkphp5.1\mch\application/index_admin/view\index\welcome.html";i:1551065534;s:27:"template/admin_pc/base.html";i:1551083825;}*/ ?>
<!DOCTYPE HTML>
<html><head>
    
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/skin.css" />
    <!--前置自定义js-->
    
    <script type="text/javascript" src="http://mch.new.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
</head><body>

<div class="page-container">
	<p class="f-20 text-success">欢迎使用维雅后台！</p>
</div>


<footer class="f24">
    <a href="http://www.miitbeian.gov.cn">
        <span class="icp_icon">Copyright@广东维雅生物科技有限公司</span>
        <span class="icp_icon">粤ICP备18130945号-1</span>
    </a>
</footer>

<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/paging.js"></script>
<!--自定义js-->

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
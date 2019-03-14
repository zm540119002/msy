<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8">
    <title><?php echo (C("WEB_NAME")); ?></title>
    <link href="/Public/admin-css/common/index.css" type="text/css" rel="stylesheet"/>
</head>

<body>
<div class="mainbg">
    
        <!-- 用户栏 -->
        <div class="topbg">
            <div class="title"><a href="javascript:void(0);"><img src="/Public/admin-img/common/logo.png" /></a></div>
            <div id="topnav" class="top-nav">
                <ul>
                    <li class="adminid" title="您好:boayang">您好&nbsp;:&nbsp;<strong>boayang</strong></li>
                    <li><a href="" target="workspace"><span>修改密码</span></a></li>
                    <li><a href="" title="安全退出"><span>安全退出</span></a></li>
                </ul>
            </div>
        </div>
        <div class="leftbg"></div>
    

    <?php if(!empty(C("MENU"))): ?><div class="demo">
            <div class="tabs">
                <ul class="tabs-list top">
                    <?php if(is_array(C("MENU"))): foreach(C("MENU") as $k=>$vo): ?><li class="menu"><a data-relevance="<?php echo ($k); ?>" href="javascript:void(0);"><?php echo ($vo["name"]); ?></a></li><?php endforeach; endif; ?>
                </ul>
                <div class="tabs-container">
                    <?php if(is_array(C("MENU"))): foreach(C("MENU") as $k=>$vo): ?><div class="tab-content" data-relevance="<?php echo ($k); ?>">
                            <div class="tabs tabs-vertical-left">
                                <ul class="tabs-list left">
                                    <?php if(is_array($vo["sub_menu"])): foreach($vo["sub_menu"] as $key=>$vv): ?><li class="menu">
                                            <a href="javascript:void(0);" data-act="<?php echo ($vv["act"]); ?>" data-op="<?php echo ($vv["op"]); ?>"><?php echo ($vv["name"]); ?></a>
                                        </li><?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div><?php endforeach; endif; ?>
                </div>
            </div>
            <div class="main">
                <iframe src="" width="100%" frameborder="0" scrolling="no" id="childFrame" ></iframe>
            </div>
        </div><?php endif; ?>
</div>

<script type="text/javascript" src="/Public/admin-js/common/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/Public/admin-js/common/responsive.tabs.js"></script>
<script type="text/javascript" src="/Public/admin-js/common/layer/layer.js"></script>
<script type="text/javascript">
    //定义全局变量
    var MODULE = '/index.php/Admin';
    var ACTION = '/index.php/Admin/Index/index';
    var CONTROLLER = '/index.php/Admin/Index';
    var APP = '/index.php';
    var PUBLIC = '/Public';
    var ROOT = '';
    var UPLOAD = ROOT+'/Uploads/';

    //设置iframe高度  
    function reinitIframe(){  
        var iframe = document.getElementById("childFrame");  
        try{  
            var bHeight = iframe.contentWindow.document.body.scrollHeight;  
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;  
            var height = Math.max(bHeight, dHeight);  
            iframe.height = height;  
        }catch (ex){console.log('设置iframe高度异常');}  
    }   
    var timer1 = window.setInterval("reinitIframe()", 200); //定时调用开始  

    //设置iframe src
    function setIframeSrc(act,op) {
        var url = '<?php echo U("'+act+'/'+op+'");?>';
        $('#childFrame').attr('src',url);
    }
</script>
<script type="text/javascript">
    $(document).ready(function(){
        //菜单切换
        $('.tabs').respTabs();

        //一级菜单点击
        $('.tabs-list.top').find('a').on('click',function(){
            var _thatRelevance = $(this).data('relevance');
            var container = $('.tabs-container');
            $.each(container.find('.tab-content'),function(){
                var _thisRelevance = $(this).data('relevance');
                if(_thatRelevance === _thisRelevance){
                    var firstA = $(this).find('.tabs-list.left').find('li.active').find('a');
                    setIframeSrc(firstA.data('act'),firstA.data('op'));
                }
            });
        });

        //二级菜单点击
        $('.tabs-list.left').find('a').on('click',function(){
            var _this = $(this);
            var act = _this.data('act');
            var op = _this.data('op');

            setIframeSrc(act,op);
        });

        //初始化iframe src
        var firstA = $('.tabs-list.left').find('li:first').find('a');
        setIframeSrc(firstA.data('act'),firstA.data('op'));
    });
</script>

</body></html>
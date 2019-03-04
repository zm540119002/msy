<?php /*a:1:{s:68:"D:\web\thinkphp5.1\mch\application/index_admin/view\index\index.html";i:1551065534;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/html5shiv.js"></script>
    <script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui.admin/css/chat.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui/css/H-ui.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui.lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="http://mch.new.com/static/h-ui.admin/css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <!--前置自定义js-->
    
    <title><?php echo htmlentities(app('config')->get('custom.title')); ?></title>
</head>
<body>
<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl">
            <nav class="nav navbar-nav">
                <ul class="cl">
                    <li class="dropDown dropDown_hover">
                        <a href="javascript:void(0);" class="dropDown_A">
                            <i class="Hui-iconfont">&#xe600;</i> 新增
                            <i class="Hui-iconfont">&#xe6d5;</i>
                        </a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li>
                                <a href="javascript:void(0);" onclick="article_add('添加资讯','article-add.html')">
                                    <i class="Hui-iconfont">&#xe616;</i> 资讯
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="picture_add('添加资讯','picture-add.html')">
                                    <i class="Hui-iconfont">&#xe613;</i> 图片
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="product_add('添加资讯','product-add.html')">
                                    <i class="Hui-iconfont">&#xe620;</i> 产品
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="member_add('添加用户','member-add.html','','510')">
                                    <i class="Hui-iconfont">&#xe60d;</i> 用户
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li>超级管理员</li>
                    <li class="dropDown dropDown_hover">
                        <a href="javascript:void(0);" class="dropDown_A">admin <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:void(0);" onClick="myselfinfo()">个人信息</a></li>
                            <li><a href="javascript:void(0);">切换账户</a></li>
                            <li><a href="<?php echo url('ucenter/UserCenter/logout'); ?>">退出</a></li>
                        </ul>
                    </li>
                    <li id="Hui-msg">
                        <a href="javascript:void(0);" title="消息">
                            <span class="badge badge-danger">1</span>
                            <i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i>
                        </a>
                    </li>
                    <li id="Hui-skin" class="dropDown right dropDown_hover">
                        <a href="javascript:void(0);" class="dropDown_A" title="换肤">
                            <i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i>
                        </a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:void(0);" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                            <li><a href="javascript:void(0);" data-val="blue" title="蓝色">蓝色</a></li>
                            <li><a href="javascript:void(0);" data-val="green" title="绿色">绿色</a></li>
                            <li><a href="javascript:void(0);" data-val="red" title="红色">红色</a></li>
                            <li><a href="javascript:void(0);" data-val="yellow" title="黄色">黄色</a></li>
                            <li><a href="javascript:void(0);" data-val="orange" title="橙色">橙色</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<aside class="Hui-aside">
    <div class="menu_dropdown bk_2">
        <?php if(!(empty($allDisplayMenu) || (($allDisplayMenu instanceof \think\Collection || $allDisplayMenu instanceof \think\Paginator ) && $allDisplayMenu->isEmpty()))): foreach($allDisplayMenu as $key=>$vo): if(!(empty($vo['sub_menu']) || (($vo['sub_menu'] instanceof \think\Collection || $vo['sub_menu'] instanceof \think\Paginator ) && $vo['sub_menu']->isEmpty()))): ?>
        <dl id="menu-tongji">
            <dt><i class="Hui-iconfont">&#xe61a;</i> <?php echo htmlentities($vo['name']); ?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <?php foreach($vo['sub_menu'] as $kk=>$vv): ?>
                    <li>
                        <a data-href="<?php echo url($vv['controller'].'/'.$vv['action']); ?>" data-title="<?php echo htmlentities($vv['name']); ?>" href="javascript:void(0)">
                            <?php echo htmlentities($vv['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </dd>
        </dl>
        <?php endif; endforeach; endif; ?>
    </div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active">
                    <span title="我的桌面" data-href="javascript:void(0);">我的桌面</span>
                    <em></em>
                </li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group">
            <a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:void(0);">
                <i class="Hui-iconfont">&#xe6d4;</i>
            </a>
            <a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:void(0);">
                <i class="Hui-iconfont">&#xe6d7;</i>
            </a>
        </div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div style="display:none" class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="<?php echo url('welcome'); ?>"></iframe>
        </div>
    </div>
</section>
<div class="contextMenu" id="Huiadminmenu">
    <ul>
        <li id="closethis">关闭当前 </li>
        <li id="closeall">关闭全部 </li>
    </ul>
</div>
<footer class="footer mt-20">
    <a href="http://www.miitbeian.gov.cn">
        <span class="icp_icon">Copyright@广东美尚网络科技有限公司</span>
        <span class="icp_icon">粤ICP备18130945号-1</span>
    </a>
</footer>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.admin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/jquery.contextmenu/jquery.contextmenu.r2.js"></script>
<script type="text/javascript">
    $(function(){
        /*
        $("#min_title_list li").contextMenu('Huiadminmenu', {
            bindings: {
                'closethis': function(t) {
                    if(t.find("i")){
                        t.find("i").trigger("click");
                    }
                },
                'closeall': function(t) {
                    alert('Trigger was '+t.id+'\nAction was Email');
                }
            }
         });
         */
        //菜单点击当前页效果
        $('body').on('click','aside li',function(){
            $('aside').find('li').removeClass('current');
            $(this).addClass('current');
        });
    });
    /*个人信息*/
    function myselfinfo(){
        layer.open({
            type: 1,
            area: ['300px','200px'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: '查看信息',
            content: '<div>管理员信息</div>'
        });
    }
    /*资讯-添加*/
    function article_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*图片-添加*/
    function picture_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*产品-添加*/
    function product_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*用户-添加*/
    function member_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
</script>
</body></html>
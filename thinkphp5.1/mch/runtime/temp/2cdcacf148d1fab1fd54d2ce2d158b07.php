<?php /*a:2:{s:78:"D:\web\thinkphp5.1\mch\application/index_admin/view\goods_category\manage.html";i:1551065534;s:27:"template/admin_pc/base.html";i:1551083825;}*/ ?>
<!DOCTYPE HTML>
<html><head>
    
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/skin.css" />
    <!--前置自定义js-->
    
    <script type="text/javascript" src="http://mch.new.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
</head><body>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <ul class="tab-base">
                <li><a href="<?php echo url('manage'); ?>" class="current"><span>管理</span></a></li>
                <li><a href="<?php echo url('edit'); ?>" ><span>编辑</span></a></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <table class="table tb-type2" id="prompt">
        <tbody>
        <tr><td><ul><li class="friendly_tipc">
            <span>*</span>请注意：删除分类时，（1）如果分类存在子分类，也会把子分类删除，（2）如果分类存在商品，删除分类会有可能导致商品无法正常显示
        </li></ul></td></tr>
        </tbody>
    </table>
</div>
<div class="content" id="list">

</div>



<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.new.com/static/index_admin/js/goodsCategoryManage.js"></script>
<script type="text/javascript">

    $(function(){
        //加载第一页
        var config = {
            url:controller+'getList',
        };
        var getData = $('#form1').serializeObject();
        getData.pageType = 'manage';
        getPagingList(config,getData);

        //翻页
        $('body').on('click','.pager2',function(){
            var curIndex= $(this).parents('ul.pagination').find('li.active span').text();
            var selectedPage=$(this).data('page');
            if(selectedPage=='»'){
                curIndex++;
                selectedPage=curIndex;
            }
            if(selectedPage=='«'){
                curIndex--;
                selectedPage=curIndex;
            }
            config.currentPage = selectedPage;
            getPagingList(config,getData);
        });
    });
</script>

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
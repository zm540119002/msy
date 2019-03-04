<?php /*a:3:{s:69:"D:\web\thinkphp5.1\mch\application/index_admin/view\scene\manage.html";i:1551065534;s:27:"template/admin_pc/base.html";i:1551083825;s:83:"D:\web\thinkphp5.1\mch\application/index_admin/view\goods_category\linkage_tpl.html";i:1551065534;}*/ ?>
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
                    <li><a href="<?php echo url('edit'); ?>"><span>编辑</span></a></li>
                </ul>
            </div>
        </div>
        <div class="main-content" id="mainContent">
            <form id="form1">
                <input type="hidden" name="pageType" value="manage">
                <table class="search-form search_product_type">
                    <tbody>
                        <tr>
                            <td class=dotted_bottom_gray height=40 width="11%" align=right>所属分类：</td>
                            <td class=dotted_bottom_gray width="58%" align=left>
                                <select name="category_id_1" class="selectbox">
    <option value="">一级分类</option>
</select>
<select name="category_id_2" class="selectbox">
    <option value="">二级分类</option>
</select>
<select name="category_id_3" class="selectbox">
    <option value="">三级分类</option>
</select>
<!-- 隐藏区 -->
<div style="display:none">
    <select id="allCategoryListTemp">
        <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo htmlentities($vo['id']); ?>" level="<?php echo htmlentities($vo['level']); ?>" parent_id_1="<?php echo htmlentities($vo['parent_id_1']); ?>"
                    parent_id_2="<?php echo htmlentities($vo['parent_id_2']); ?>"><?php echo htmlentities($vo['name']); ?></option>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
</div>
                            </td>
                            <th>项目名称</th>
                            <td class="w160">
                                <input class="text product_management_name" type="text" name="keyword" value="">
                            </td>
                            <td class="w70 tc">
                                <label class="submit-border">
                                    <input type="button" id="searchProject" class="submit product_search_btn" value="搜索">
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <div class="content" id="list"></div>
        </div>
    </div>

    <!-- 隐藏区 -->
    <div style="display:none">
        <div id="loading">
            <div class='loading'><img src='http://mch.new.com/static/index_admin/img/default/loading.gif' alt='loading'></div>
        </div>
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

    <script type="text/javascript" src="http://mch.new.com/static/index_admin/js/categoryLinkage.js"></script>
    <script type="text/javascript">
        $(function () {
            //加载第一页
            var config = {
                url:controller+'getList',
            };
            var getData = $('#form1').serializeObject();
            getPagingList(config,getData);
            //分类筛选
            $('body').on('change','[name=category_id_1],[name=category_id_2],[name=category_id_3]',function () {
                var getData = $('#form1').serializeObject();
                getPagingList(config,getData);
            });
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
            //搜索
            $('#searchGoods').click(function(){
                config.currentPage = 0;
                var getData = $('#form1').serializeObject();
                getPagingList(config,getData);
            });

            //编辑
            $('body').on('click','.edit',function(){
                var _thisTr = $(this).parents('tr');
                var url =  controller + 'edit/id/' + _thisTr.data('id');
                location.href = url;
            });

            //删除
            $('body').on('click','.del',function(){
                var _thisTr = $(this).parents('tr');
                var postData = {};
                postData.id = _thisTr.data('id');
                var url =  controller + 'del';
                layer.open({
                    btn: ['确定','取消'],//按钮
                    content:'删除你选中的商品吗？',
                    yes:function (index) {
                        $.post(url,postData,function(msg){
                            dialog.msg(msg,'',function(){
                                _thisTr.remove();
                            });
                        });
                        layer.close(index);
                    }
                });
            });
            //批量删除数据
            $('body').on('click','.batchDel',function(){
                var postData = {};
                ids = [];
                $("input[name='checkbox']").each(function(){
                    var _thisTr = $(this).parents('tr');
                    if($(this).is(':checked')){
                        ids.push(_thisTr.data('id'));
                    }
                });
                postData.ids= ids;
                var url =  controller + '/del';
                if(ids.length==0){
                    layer.tips('你还没有选择任何内容！','.btn',{
                        tips:[4,'#0d7eff'],
                        time:1500
                    });
                    return false;
                }else{
                    layer.confirm('删除你选中的商品吗？', {
                        btn: ['确定','取消'],//按钮
                        yes:function (index) {
                            $.post(url,postData,function(msg){
                                dialog.msg(msg,'',function () {
                                    $("input[name='checkbox']").each(function(){
                                        if($(this).is(':checked')){
                                            $(this).parents('.edit').remove();
                                        }
                                    })
                                });
                            });
                            layer.close(index);
                        }
                    })
                }
            })
            //上下架
            $('body').on('click','.set-shelf-status',function(){
                var _this = $(this);
                var text = _this.text();
                var shelf_status = _this.data('shelf-status');
                var _thisTr = $(this).parents('tr');
                var postData ={};
                postData.id = _thisTr.data('id');
                postData.shelf_status = shelf_status;
                var url =  controller + 'setShelfStatus';
                layer.open({
                    btn: ['确定','取消'],//按钮
                    content:"是否"+text+'?',
                    yes:function (index) {
                        $.post(url,postData,function(msg){
                            dialog.msg(msg,'',function(){
                                _thisTr.find('.shelf-status').text(text);
                                if(shelf_status == 3){
                                    _this.text('下架');
                                    _this.data('shelf-status',1);
                                }else{
                                    _this.text('上架');
                                    _this.data('shelf-status',3);
                                }
                            });
                        });
                        layer.close(index);
                    }
                });
            });
            ////设置为精选
            $('body').on('click','.set_selection',function(){
                var _this = $(this);
                var text = _this.text();
                var is_selection = _this.data('is-selection');
                var _thisTr = $(this).parents('tr');
                var postData ={};
                postData.id = _thisTr.data('id');
                postData.is_selection = is_selection;
                var url =  controller + 'setSelection';
                _this.addClass("nodisabled");//防止重复提交
                layer.open({
                    btn: ['确定','取消'],//按钮
                    content:"是否"+text+'?',
                    yes:function (index) {
                        $.post(url,postData,function(msg){
                            _this.removeClass("nodisabled");
                            dialog.msg(msg,'',function(){
                                if(is_selection == 1){
                                    _thisTr.find('.is-selection').text('精选');
                                    _this.text('取消为精选');
                                    _this.data('is-selection',0);
                                }else{
                                    _thisTr.find('.is-selection').text('不为精选');
                                    _this.text('设置为精选');
                                    _this.data('is-selection',1);
                                }
                            });
                        });
                        layer.close(index);
                    }
                });
            });

            //去设置项目商品
            $('body').on('click','.addProjectGoods',function(){
                var _thisTr = $(this).parents('tr');
                var url =  controller + 'addSceneGoods/id/' + _thisTr.data('id');
                location.href = url;
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
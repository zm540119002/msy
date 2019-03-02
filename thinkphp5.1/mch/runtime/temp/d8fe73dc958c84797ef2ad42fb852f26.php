<?php /*a:1:{s:78:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/scene/list_tpl.html";i:1551064593;}*/ ?>
<div class="search-result">
    <?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?>
    <table class="table tb-type2">
        <thead>
        <tr class="thead">
            <th></th>
            <th>排序</th>
            <th>项目名称</th>
            <th>所属分类</th>
            <th>操作</th>
            <th>是否为精选</th>
            <th>状态</th>
            <th>上下架设置</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr class="hover" data-id="<?php echo htmlentities($vo['id']); ?>">
            <td class="w48"><input type="checkbox" class="checkitem" name="checkbox">
            <td class="w48 sort"><span title="" class="editable "><?php echo htmlentities($vo['sort']); ?></span></td>
            <td class="w50pre name"><span title="" class="editable "><?php echo htmlentities($vo['name']); ?></span></td>
            <td class="w5"></td>
            <td class="w96">
                <a href="javascript:void(0);" class="edit">编辑</a>
                | <a href="javascript:void(0);" class="del">删除</a>
                | <a href="javascript:void(0);" class="addProjectGoods">添加产品</a>
                <?php if($vo['is_selection'] == 0): ?>
                | <a href="javascript:void(0);" class="set_selection" data-is-selection="1">设置为精选</a>
                <?php else: ?>
                | <a href="javascript:void(0);" class="set_selection" data-is-selection="0">取消为精选</a>
                <?php endif; ?>
            </td>
            <td class="w96 is-selection">
                <?php if($vo['is_selection'] == 1): ?>
                <span>精选</span>
                <?php else: ?>
                <span>不为精选</span>
                <?php endif; ?>
            </td>
            <td class="w96 shelf-status">
                <span><?php echo htmlentities(getShelStatus($vo['shelf_status'])); ?></span>
            </td>
            <td class="w96">
                <?php if(($vo['shelf_status'] == 1) or ($vo['shelf_status'] == 2)): ?>
                <a href="javascript:void(0);" class="set-shelf-status" data-shelf-status="3">上架</a>
                <?php else: ?>
                <a href="javascript:void(0);" class="set-shelf-status" data-shelf-status="1">下架</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
        <tfoot>
        <tr class="tfoot">
            <td><input type="checkbox" class="checkall" id="checkall_2"></td>
            <td id="batchAction" colspan="15">
                <span class="all_checkbox"><label for="checkall_2">全选</label></span>&nbsp;&nbsp;
                <a href="JavaScript:void(0);" class="batchDel" ><span>批量删除</span></a>
            </td>
        </tr>
        </tfoot>
    </table>
    <?php else: ?>
    <div>没有数据</div>
    <?php endif; ?>
</div>
<?php echo $list; ?>




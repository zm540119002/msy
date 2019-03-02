<?php /*a:1:{s:87:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/goods_category/list_tpl.html";i:1551064593;}*/ ?>
<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?>
<div class="search-result">
    <table class="table tb-type2">
        <thead>
        <tr class="thead">
            <th></th>
            <th width="10%">排序</th>
            <th width="20%">名称</th>
            <th width="40%">说明</th>
            <th width="20%">操作</th>
        </tr>
        </thead>
        <tbody >
        <?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr class="hover" data-level="<?php echo htmlentities($vo['level']); ?>" data-id="<?php echo htmlentities($vo['id']); ?>" data-parent-id-1="<?php echo htmlentities($vo['parent_id_1']); ?>">
            <td class="w5">
                <input type="checkbox" class="check_item_2">
                <?php if(($vo['level'] == 1) or ($vo['level'] == 2)): ?>
                <span class="folding" status="open"></span>
                <?php endif; ?>
            </td>
            <td class="w5"><?php echo htmlentities((isset($vo['sort']) && ($vo['sort'] !== '')?$vo['sort']:'')); ?></td>
            <td class="w5"><?php echo htmlentities((isset($vo['name']) && ($vo['name'] !== '')?$vo['name']:'')); ?></td>
            <td class="w5"><?php echo htmlentities((isset($vo['remark']) && ($vo['remark'] !== '')?$vo['remark']:'')); ?></td>
            <td class="w96">
                <a class="a-edit" data-operate="edit" href="javascript:void(0);">编辑</a>
                | <a class="a-del" href="javascript:void(0);">删除</a>
                <?php if(($vo['level'] == 1) or ($vo['level'] == 2)): ?>
                | <a class="a-add" data-operate="addLower" href="javascript:void(0);">新增下级</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; else: endif; ?>
        </tbody>
        <tfoot>
        <tr class="tfoot">
            <td><input type="checkbox" class="check_all_2" id="check_all_2"></td>
            <td id="batchAction" colspan="15">
                <span class="all_checkbox"><label for="check_all_2">全选</label></span>&nbsp;&nbsp;
                <a href="JavaScript:void(0);" class="batchDel" ><span>批量删除</span></a>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<?php endif; ?>

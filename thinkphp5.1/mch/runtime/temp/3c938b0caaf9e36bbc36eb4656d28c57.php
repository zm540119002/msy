<?php /*a:1:{s:71:"D:\web\thinkphp5.1\mch\application/index/view\goods\list_index_tpl.html";i:1551065534;}*/ ?>
<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
<li data-id="<?php echo htmlentities($info['id']); ?>">
    <a href="<?php echo url('Goods/detail',['id'=>$info['id']]); ?>">
        <?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
        <img src="http://mch.new.com/static/common/img/default/no_pic_100.jpg" alt="">
        <?php else: ?>
        <img src="http://mch.new.com/uploads/<?php echo htmlentities($info['thumb_img']); ?>" alt="">
        <?php endif; ?>
    </a>
    <div class="p_bottom">
        <p class="text-intro title_name"><?php echo htmlentities($info['headline']); ?></p>
        <div class="columns_flex l-r-sides">
            <span class="pink">￥<price><?php echo htmlentities($info['bulk_price']); ?></price></span>
        </div>
    </div>
</li>
<?php endforeach; endif; else: echo "" ;endif; endif; ?>

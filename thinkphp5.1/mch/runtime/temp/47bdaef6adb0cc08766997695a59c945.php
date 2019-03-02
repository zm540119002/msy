<?php /*a:1:{s:77:"/home/www/web/thinkphp5.1/mch/application/index/view/collection/list_tpl.html";i:1551064593;}*/ ?>
<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
<li data-goods_id="<?php echo htmlentities($info['goods_id']); ?>" data-id="<?php echo htmlentities($info['id']); ?>">
    <input type="checkbox" class="item_checkbox checkitem sign_checkitem" >
    <a  href="<?php echo url('Goods/detail',['id'=>$info['goods_id']]); ?>" class="cart_goods_img">
        <?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
        <img src="http://mch.meishangyun.com/static/common/img/default/no_pic_100.jpg" alt="" class="c_img">
        <?php else: ?>
        <img src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['thumb_img']); ?>" alt="" class="c_img">
        <?php endif; ?>
    </a>
    <div class="cart_list_r">
        <p><?php echo htmlentities($info['headline']); ?></p>
        <div>商品规格：<span>50ml/瓶</span></div>
        <p class="red">￥<price><?php echo htmlentities($info['bulk_price']); ?></price></p>
    </div>
</li>
<?php endforeach; endif; else: echo "" ;endif; else: if($currentPage ==1): ?>
<li class="no_data">
    <img src="http://mch.meishangyun.com/static/common/img/no-collection.png" alt="">
</li>
<?php endif; endif; ?>


  

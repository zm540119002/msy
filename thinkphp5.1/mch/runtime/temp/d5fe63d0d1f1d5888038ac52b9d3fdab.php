<?php /*a:1:{s:64:"D:\web\thinkphp5.1\mch\application/index/view\cart\list_tpl.html";i:1551065534;}*/ ?>
<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
<li data-id="<?php echo htmlentities($info['id']); ?>" data-cart_id="<?php echo htmlentities($info['cart_id']); ?>" data-buy_type="<?php echo htmlentities($info['buy_type']); ?>"  data-brand_id="<?php echo htmlentities($info['brand_id']); ?>" data-brand_name="<?php echo htmlentities($info['brand_name']); ?>">
    <input type="checkbox" class="item_checkbox checkitem sign_checkitem" >
    <a  href="" class="cart_goods_img">
        <?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
        <img src="http://mch.new.com/static/common/img/default/no_pic_100.jpg" alt="" class="c_img">
        <?php else: ?>
        <img src="http://mch.new.com/uploads/<?php echo htmlentities($info['thumb_img']); ?>" alt="" class="c_img">
        <?php endif; ?>
    </a>
    <div class="cart_list_r">
        <p><?php echo htmlentities($info['name']); ?></p>
        <div>商品规格：<span><?php echo htmlentities($info['specification']); ?></span></div>
        <div class="commodity_purchaser f24">
            <?php if($info['buy_type'] == 1): ?>
            <span class="pink">￥<price><?php echo htmlentities($info['bulk_price']); ?></price></span>
            <div>购买类型：<span>批量</span></div>
            <?php elseif($info['buy_type'] == 2): ?>
            <span class="pink">￥<price><?php echo htmlentities($info['sample_price']); ?></price></span>
            <div>购买类型：<span>样品</span></div>
            <?php else: endif; ?>

        </div>
        <div class="purchase_goods_num cart_goods_operate">
            <!--<span>购买数量(5)</span>-->
            <div class="quantity_wrapper">
                <a href="javascript:void(0);" class="cart_greduce">-</a>
                <input type="text" value="<?php echo htmlentities($info['num']); ?>" class="f24 cart_gshopping_count">
                <a href="javascript:void(0);" class="cart_gplus">+</a>
            </div>
            <a href="javascript:void(0);" class="detele_order goodsDel detele_cart">删除</a>
        </div>
    </div>
</li>
<?php endforeach; endif; else: echo "" ;endif; else: if($currentPage ==1): ?>
<li class="no_data">
    <img src="http://mch.new.com/static/common/img/no-cart.png" alt="">
</li>
<?php endif; endif; ?>


  

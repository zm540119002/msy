<?php /*a:1:{s:72:"/home/www/web/thinkphp5.1/mch/application/index/view/order/list_tpl.html";i:1551064593;}*/ ?>
<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
    <li data-id="<?php echo htmlentities($info['id']); ?>" data-order_sn="<?php echo htmlentities($info['sn']); ?>">
            <a href="<?php echo url('order/detail',['order_sn'=>$info['sn']]); ?>">
                <?php if(is_array($info['goods_list']) || $info['goods_list'] instanceof \think\Collection || $info['goods_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $info['goods_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods_info): $mod = ($i % 2 );++$i;?>
                <div class="order_item" data-id="<?php echo htmlentities($goods_info['goods_id']); ?>" data-buy_type="<?php echo htmlentities($goods_info['buy_type']); ?>"
                     data-brand_id="<?php echo htmlentities($goods_info['brand_id']); ?>"  data-brand_name="<?php echo htmlentities($goods_info['brand_name']); ?>"  >
                        <?php if(empty($goods_info['thumb_img']) || (($goods_info['thumb_img'] instanceof \think\Collection || $goods_info['thumb_img'] instanceof \think\Paginator ) && $goods_info['thumb_img']->isEmpty())): ?>
                        <img src="http://mch.meishangyun.com/static/common/img/default/no_pic_100.jpg" class="left">
                        <?php else: ?>
                        <img src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($goods_info['thumb_img']); ?>" class="left">
                        <?php endif; ?>
                    <div class="order_top_r">
                        <p><?php echo htmlentities($goods_info['name']); ?></p>
                        <p>￥<price><?php echo htmlentities($goods_info['price']); ?></price></p>
                        <p>X<span class="num"><?php echo htmlentities($goods_info['num']); ?></span></p>
                        <div class="commodity_purchaser f24">
                            <?php if($goods_info['buy_type'] == 1): ?>
                            <div>购买类型：<span>批量</span></div>
                            <?php elseif($goods_info['buy_type'] == 2): ?>
                            <div>购买类型：<span>样品</span></div>
                            <?php else: endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </a>
            <div class="order_bottom">
                <p>共<span><?php echo htmlentities($info['goods_num']); ?></span>件商品 需付款:<span class="red">￥<price><?php echo htmlentities($info['amount']); ?></price></span></p>
                <p class="order_operate_btn">
                    <?php if($info['order_status'] == 1): ?>
                    <!--1:待付款-->
                    <a href="javascript:void(0);" class="cancel_order">取消订单</a>
                    <a href="<?php echo url('Order/toPay',['order_sn'=>$info['sn']]); ?>">去支付</a>
                    <?php elseif($info['order_status'] == 2): ?>
                    <!--2:待收货-->
                    <a href="javascript:void(0);" class="confirm_receive">确认收货</a>
                    <?php elseif($info['order_status'] == 3): ?>
                    <!--3:待评价-->
                    <a href="javascript:void(0);" class="apply_after_sales">申请售后</a>
                    <a href="javascript:void(0);" class="to_evaluate">评价</a>
                    <?php elseif($info['order_status'] == 4): ?>
                    <!--4:已完成-->
                    <a href="javascript:void(0);" class="see_evaluation">查看评价</a>
                    <a href="javascript:void(0);" class="purchase_again">再次购买</a>
                    <?php elseif($info['order_status'] == 5): ?>
                    <span class="">已取消</span>
                    <?php elseif($info['order_status'] == 6): ?>
                    售后待处理
                    <?php else: endif; ?>
                </p>
            </div>
    </li>
<?php endforeach; endif; else: echo "" ;endif; else: if($currentPage ==1): ?>
<li class="no_data">
    <img src="http://mch.meishangyun.com/static/common/img/no-order.png" alt="">
</li>
<?php endif; endif; ?>
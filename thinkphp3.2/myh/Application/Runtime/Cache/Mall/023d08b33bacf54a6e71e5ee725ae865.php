<?php if (!defined('THINK_PATH')) exit(); if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($vo["id"]); ?>">
        <a href="<?php echo U('Goods/goodsDetail',array('goodsId'=>$vo['id']));?>">
            <?php if(empty($vo["thumb_img"])): ?><img src="/Public/img/common/default/no_pic_1000.jpg" alt="">
                <?php else: ?>
                <img src="/Uploads/<?php echo ($vo["thumb_img"]); ?>" alt=""><?php endif; ?>
        </a>
        <p class="headlines"><?php echo ($vo["headlines"]); ?></p>
        <div class="business_pdetail">
            <div class="b_f">
                <span class="market_price">￥<price><?php echo ($vo["sale_price"]); ?></price></span>
            </div>
            <div class="b_f">
                <del>￥<?php echo ($vo["price"]); ?></del>
            </div>
            <?php if($vo["buy_type"] == 2): ?><div class="save_money">返现:￥<?php echo ($vo["cash_back"]); ?></div>
                <?php else: ?>
                <div class="b_f commodity_price">
                    <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                </div><?php endif; ?>
        </div>
    </li><?php endforeach; endif; else: echo "" ;endif; ?>
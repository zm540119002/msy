<?php if (!defined('THINK_PATH')) exit(); if(!empty($goodsInfo)): ?><li data-id="<?php echo ($goodsInfo["id"]); ?>">
        <div class="purchase_goods_detail">
            <?php if(empty($goodsInfo["main_img"])): ?><img class="purchase_gs_img" src="/Public/img/common/default/no_pic_40.jpg" alt="">
                <?php else: ?>
                <!--<img src="/Uploads/<?php echo ($goodsInfo["main_img"]); ?>" alt="">--><?php endif; ?>
            <div class="purchase_gs_r">
                <p><?php echo ($goodsInfo["name"]); ?></p>
                <p><?php echo ($goodsInfo["remark"]); ?></p>
                <a href="javascript:void(0);" class="closeBtn">X</a>
            </div>
        </div>
        <div class="purchase_goods_detail">
            <div class="purchase_packing">
                <div>商品规格：<span><?php echo ($goodsInfo["single_specification"]); ?>/<?php echo (getUnitCN($goodsInfo["package_unit"])); ?></span></div>
                <div class="purchase_packing_r">
                    <span class="market level_price">￥<price><?php echo ($goodsInfo["sale_price"]); ?></price></span>
                    <del>￥<?php echo ($goodsInfo["price"]); ?></del>
                </div>
            </div>
        </div>
        <div class="purchase_goods_detail purchase_goods_num">
            <span>购买数量(单位：<?php echo (getUnitCN($goodsInfo["package_unit"])); ?>)</span>
            <div class="quantity_wrapper">
                <a href="javascript:void(0);" class="greduce">-</a>
                <input type="text" value="1" class="f24 gshopping_count">
                <a href="javascript:void(0);" class="gplus">+</a>
            </div>
        </div>
    </li><?php endif; ?>
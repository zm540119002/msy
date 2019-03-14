<?php if (!defined('THINK_PATH')) exit(); if(!empty($speGoodsList)): ?><!--促销-->
    <div class="commodity_category commodity_category_s">
        <div class="promotion_mode">
            <img src=/Public/img/home/wtg.png" alt="本期促销场景图">
        </div>
        <div class="type_segmenting_line">
            <span class="line"></span>
            <span class="txt f24">XX应用主题</span>
            <span class="line"></span>
        </div>
        <ul>
            <?php if(is_array($speGoodsList)): $i = 0; $__LIST__ = $speGoodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($vo["id"]); ?>" data-real-price="<?php echo ($vo["real_price"]); ?>" data-specification="<?php echo ($vo["specification"]); ?>" data-goods_type='1' >
                    <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vo['id']));?>">
                        <div class="goods_img">
                            <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                                <?php else: ?>
                                <img data-img="" data-isloaded="" src="/Public/img/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                        </div>
                        <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                        <div class="commodity_price f24">
                            <span class="price">￥<price><?php echo ($vo["special_price"]); ?></price></span>
                            <del class="">￥<price><?php echo ($vo["price"]); ?></price></del>
                            <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                        </div>
                    </a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
          <a class="view_more" href="javascript:void(0);" data-page="2" data-buytype="2" data-goods_type='1' data-categoryid="<?php echo ($vo["category_id_1"]); ?>">查看更多<i></i></a>
    </div><?php endif; ?>
<?php if(!empty($groupGoodsList)): ?><!--微团-->
    <div class="commodity_category">
        <div class="promotion_mode">
            <img src="/Public/img/home/wtg.png" alt="">
        </div>
        <ul>
            <?php if(is_array($groupGoodsList)): $i = 0; $__LIST__ = $groupGoodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-buytype="3" data-id="<?php echo ($vo["id"]); ?>" data-goods_type='1'>
                    <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vo['id']));?>">
                        <div class="goods_img">
                            <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                                <?php else: ?>
                                <img data-img="" data-isloaded="" src="/Public/img/common/default/no_pic_1000.jpg" alt="" class="c_img" /><?php endif; ?>
                        </div>
                    </a>
                    <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                    <div class="commodity_price f24">
                        <span class="price">￥<price class="price"><?php echo ($vo["special_price"]); ?></price></span>
                        <del>￥<price><?php echo ($vo["price"]); ?></price></del>
                        <span class="pink">节省￥<price><?php echo ($vo["cash_back"]); ?></price></span>
                        <a href="javascript:void(0);" class="shopping_cart wt_cart" data-weituan="1">发起微团购</a>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
          <a class="view_more" href="javascript:void(0);" data-page="2" data-buytype="3" data-goods_type='1' data-categoryid="<?php echo ($vo["category_id_1"]); ?>">查看更多<i></i></a>
    </div><?php endif; ?>



<?php if(!empty($goodsList)): ?><!--单独显示每分类产品-->
    <div class="commodity_category commodity_category_s">
    <ul>
        <?php if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($vo["id"]); ?>" data-real-price="<?php echo ($vo["real_price"]); ?>" data-specification="<?php echo ($vo["specification"]); ?>" data-goods_type='1'>
                <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vo['id']));?>">
                    <div class="goods_img">
                        <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                            <?php else: ?>
                            <img data-img="" data-isloaded="" src="/Public/img/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                    </div>
                    <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                    <div class="commodity_price f24">
                        <span class="price">￥<price><?php echo ($vo["discount_price"]); ?></price></span>
                        <del class="">￥<price><?php echo ($vo["price"]); ?></price></del>
                        <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                    </div>
                </a>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
        </div><?php endif; ?>
<?php if(!empty($goodsListLoad)): ?><!--加载更多商品-->
    <?php if(is_array($goodsListLoad)): $kk = 0; $__LIST__ = $goodsListLoad;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($kk % 2 );++$kk;?><li data-id="<?php echo ($vv["id"]); ?>" data-real-price="<?php echo ($vv["real_price"]); ?>" data-specification="<?php echo ($vv["specification"]); ?>" data-goods_type='1'>
            <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vv['id']));?>">
                <div class="goods_img">
                    <?php if(!empty($vv["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vv["main_img"]); ?>" alt="" class="c_img" />
                        <?php else: ?>
                        <img data-img="" data-isloaded="" src="/Public/img/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                </div>
                <p class="f24 commodity_name"><?php echo ($vv["name"]); ?></p>
                <div class="commodity_price f24">
                    <?php if($vv["buy_type"] == 1 ): ?><span class="price">￥<price><?php echo ($vv["discount_price"]); ?></price></span><?php endif; ?>
                    <?php if($vv["buy_type"] == 2 ): ?><span class="price">￥<price><?php echo ($vv["special_price"]); ?></price></span><?php endif; ?>
                    <?php if($vv["buy_type"] == 3): ?><span class="price">￥<price><?php echo ($vv["discount_price"]); ?></price></span><?php endif; ?>
                    <del class="">￥<price><?php echo ($vo["price"]); ?></price></del>
                    <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                </div>
            </a>
        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
<?php if(!empty($groupGoodsListMore)): ?><!--微团购列表-->
    <?php if(is_array($groupGoodsListMore)): $i = 0; $__LIST__ = $groupGoodsListMore;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-buytype="3" data-id="<?php echo ($vo["id"]); ?>" data-goods_type='1'>
            <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vo['id']));?>">
                <div class="goods_img">
                    <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                        <?php else: ?>
                        <img data-img="" data-isloaded="" src="/Public/img/common/default/no_pic_1000.jpg" alt="" class="c_img" /><?php endif; ?>
                </div>
            </a>
            <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
            <div class="commodity_price f24">
            <span class="price">￥<price class="price"><?php echo ($vo["special_price"]); ?></price></span>
            <del>￥<price><?php echo ($vo["price"]); ?></price></del>
            <span class="pink">节省￥<price><?php echo ($vo["cash_back"]); ?></price></span>
            <a href="javascript:void(0);" class="shopping_cart wt_cart" data-weituan="1">发起微团购</a>
            </div>
        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>

<?php if(!empty($speGoodsListMore)): ?><!--促销购列表-->
    <?php if(is_array($speGoodsListMore)): $i = 0; $__LIST__ = $speGoodsListMore;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($vo["id"]); ?>" data-real-price="<?php echo ($vo["real_price"]); ?>" data-specification="<?php echo ($vo["specification"]); ?>" data-goods_type='1' >
            <a href="<?php echo U('Goods/goodsInfo',array('goodsId'=> $vo['id']));?>">
                <div class="goods_img">
                    <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                        <?php else: ?>
                        <img data-img="" data-isloaded="" src="/Public/img/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                </div>
                <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                <div class="commodity_price f24">
                    <span class="price">￥<price><?php echo ($vo["special_price"]); ?></price></span>
                    <del class="">￥<price><?php echo ($vo["price"]); ?></price></del>
                </div>
            </a>
        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
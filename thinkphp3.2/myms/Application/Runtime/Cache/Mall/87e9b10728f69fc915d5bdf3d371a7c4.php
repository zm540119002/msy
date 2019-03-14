<?php if (!defined('THINK_PATH')) exit();?><div id="areaMask" class="mask" data-show="0"></div>
<section id="areaLayer" class="express-area-box cart" >
    <div class="purchase_goods_module f24 goodsList">
        <ul class="goods_list">
            <li data-id="<?php echo ($goodsInfo["id"]); ?>" data-goods_type="<?php echo ($goodsInfo["goods_type"]); ?>">
                <input type="checkbox" checked="true" class="single_select" style="display: none;">
                <?php if($goodsInfo["buy_type"] == 3): ?><div class="group_members_detail layer_group_members f24">
                        <div class="l_g_top">
                            <span class="icon"></span>
                            <div>
                                <p>美妍美社.微团购</p>
                                <p>三人成团即亨特惠</p>
                            </div>
                        </div>
                        <?php echo ($groupBuyDetail[0]["headimgurl"]); ?>
                        <div class="group_members">
                            <div class="group_m_list">
                                <?php if(empty($groupBuyDetail[0]["headimgurl"])): ?><img src="/Public/img/common/default/wechat_header.png" alt="">
                                    <?php else: ?>
                                    <img src="<?php echo ($groupBuyDetail[0]["headimgurl"]); ?>" alt=""><?php endif; ?>
                                <span>团长</span>
                            </div>
                            <div class="group_m_list">
                                <?php if(empty($groupBuyDetail[1]["headimgurl"])): ?><img src="/Public/img/common/default/wechat_header.png" alt="">
                                    <?php else: ?>
                                    <img src="<?php echo ($groupBuyDetail[1]["headimgurl"]); ?>" alt=""><?php endif; ?>
                                <span>团员</span>
                            </div>
                            <div class="group_m_list">
                                <?php if(empty($groupBuyDetail[2]["headimgurl"])): ?><img src="/Public/img/common/default/wechat_header.png" alt="">
                                    <?php else: ?>
                                    <img src="<?php echo ($groupBuyDetail[2]["headimgurl"]); ?>" alt=""><?php endif; ?>
                                <span>团员</span>
                            </div>
                        </div>
                    </div><?php endif; ?>
                <div class="purchase_goods_detail">
                    <?php if(!empty($goodsInfo["main_img"])): ?><img class="purchase_gs_img" src="/Uploads/<?php echo ($goodsInfo["main_img"]); ?>" alt=""  />
                        <?php else: ?>
                        <img class="purchase_gs_img" src="/Public/img/common/default/purchase40_default.jpg" alt=""><?php endif; ?>
                    <div class="purchase_gs_r concessional_rate">
                        <p><?php echo ($goodsInfo["name"]); ?></p>
                        <div>
                            <span class="real_price">￥<price><?php echo ($goodsInfo["real_price"]); ?></price></span>
                            <del>￥<price><?php echo ($goodsInfo["price"]); ?></price></del>
                        </div>                       
                    </div>
                </div>
                <div class="purchase_goods_detail">
                    <p>产品规格</p>
                    <p class="specification"><?php echo ($goodsInfo["specification"]); ?></p>
                </div>
                <div class="purchase_goods_detail purchase_goods_num">
                    <span>购买数量</span>
                    <div class="quantity_wrapper">
                        <a href="javascript:void(0);" class="greduce">-</a>
                        <input type="text" value="1" class="f24 gshopping_count">
                        <a href="javascript:void(0);" class="gplus">+</a>
                    </div>
                </div>
                <a href="javascript:void(0);" class="closeBtn">X</a>
            </li>
        </ul>
    </div>
    
        <footer class="f24 group_cart_nav">
    <?php if(is_array($unlockingFooterCart)): $i = 0; $__LIST__ = $unlockingFooterCart;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo["share"])): ?><!--分享-->
            <div class="group_btn25 bfff">
                <a href="javascript:void(0);" class="share_column share"><span class="s_i"></span><?php echo ($vo["share"]); ?></a>
            </div><?php endif; ?>
        <?php if(!empty($vo["shopping_basket"])): ?><!--购物篮-->
            <div class="group_btn25 bfff">
                <a href="javascript:void(0);" class="share_column shopping_cart_column shopping_basket">
                    <span class="s_c"><i>5</i></span></a>
            </div><?php endif; ?>
        <?php if(!empty($vo["amount"])): ?><!--总价-->
            <div class="group_btn50 bfff">
                <span class="goods_total_price amount">￥<price>0.00</price></span>
            </div><?php endif; ?>
        <?php if(!empty($vo["add_cart"])): ?><!--加入购物车-->
            <a href="javascript:void(0);" class="bff6482 group_btn50 add_cart"><?php echo ($vo["add_cart"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["clearing_now"])): ?><!--立即结算-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 clearing_now"><i class="collect"></i><?php echo ($vo["clearing_now"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["buy_now"])): ?><!--立即购买-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 buy_now"><i class="collect"></i><?php echo ($vo["buy_now"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["initiate_group_buy"])): ?><!--支付并发起微团购-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 initiate_group_buy"><i class="collect"></i><?php echo ($vo["initiate_group_buy"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["join_group_buy"])): ?><!--支付并发起微团购-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 initiate_group_buy"><i class="collect"></i><?php echo ($vo["join_group_buy"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["determine_order"])): ?><!--确定订单-->
            <a href="javascript:void(0);" class="group_btn50 bff1d25 determine_order"><i class="collect"></i><?php echo ($vo["determine_order"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["pay_now"])): ?><!--立即支付-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 pay_now"><i class="collect"></i><?php echo ($vo["pay_now"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["earnings"])): ?><!--收益-->
        <div class="group_btn100 bfff">
            <span class="goods_total_price earnings">收益￥<?php echo ($goodsInfo["commission"]); ?></span>
        </div><?php endif; ?>
        <?php if(!empty($vo["QR_codes"])): ?><!--我的二维码-->
            <a href="javascript:void(0);" class="group_btn100 bff6482 QR_codes"><i class="collect"></i><?php echo ($vo["QR_codes"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["forward"])): ?><!--一键分享转发-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 forward"><i class="collect"></i><?php echo ($vo["forward"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["forward_to_friends"])): ?><!--将本页转发微信好友-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 forward"><i class="collect"></i><?php echo ($vo["forward_to_friends"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["group_buy_end"])): ?><!--团购已结束-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 group_buy_end  initiate_group_buy"><i class="collect"></i><?php echo ($vo["group_buy_end"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["open_referrer"])): ?><!--开通分享功能-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 open_referrer"><i class="collect"></i><?php echo ($vo["open_referrer"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["address_save"])): ?><!--地址保存-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 address_save"><i class="collect"></i><?php echo ($vo["address_save"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["address_save_order_id"])): ?><!--地址保存带orderid-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 address_save"><i class="collect"></i><?php echo ($vo["address_save_order_id"]); ?></a><?php endif; ?>
        <?php if(!empty($vo["address_add"])): ?><!--新建地址-->
            <a href="javascript:void(0);" class="group_btn100 bff1d25 address_add"><i class="collect"></i><?php echo ($vo["address_add"]); ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
</footer>
    
</section>
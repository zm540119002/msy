<?php if (!defined('THINK_PATH')) exit();?>
<!--闺蜜行-->
<?php if(!empty($groupProjectList['data'])): ?><div class="commodity_category commodity_category_s">
        <div class="promotion_mode">
            <img src="<?php echo (C("/Public/img")); ?>/home/wtg.png" alt="">
        </div>
        <ul>
            <?php if(is_array($groupProjectList['data'])): $i = 0; $__LIST__ = $groupProjectList['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-buytype="3" data-goodsid="<?php echo ($vo["id"]); ?>" data-categoryid="<?php echo ($vo["category_id_1"]); ?>"  data-type="1">
                    <a href="<?php echo U('Project/projectInfo',array('projectId'=> $vo['id'],'buyType'=>'3'));?>">
                        <div class="goods_img">
                            <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                                <?php else: ?>
                                <img data-img="" data-isloaded="" src="<?php echo (C("/Public/img")); ?>/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                        </div>
                        <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                        <div class="commodity_price f24">
                            <span class="price">￥<?php echo ($vo["group_price"]); ?></span>
                            <a href="javascript:void(0);" class="shopping_cart">团购购物车</a>
                        </div>
                    </a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <a class="view_more" href="javascript:void(0);" data-page="2"  data-type="project" >查看更多<i></i></a>
    </div><?php endif; ?>
<!--分类产品-->
<?php if(!empty($catProjectList)): ?><div class="commodity_category commodity_category_s">
        <?php if(is_array($catProjectList)): foreach($catProjectList as $k=>$vo): if(!empty($vo)): ?><span class="beauty_title special_products_title f24"><?php echo ($k); ?> </span>
                <ul >
                    <?php if(is_array($vo['data'])): $kk = 0; $__LIST__ = $vo['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($kk % 2 );++$kk;?><li data-goodsid="<?php echo ($vv["id"]); ?>" data-buytype="1" data-categoryid="<?php echo ($vv["category_id_1"]); ?>" data-type="1">
                            <a href="<?php echo U('Project/projectInfo',array('projectId'=> $vv['id'],'buyType'=>'1'));?>">
                                <div class="goods_img">
                                    <?php if(!empty($vv["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vv["main_img"]); ?>" alt="" class="c_img" />
                                        <?php else: ?>
                                        <img data-img="" data-isloaded="" src="<?php echo (C("/Public/img")); ?>/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                                </div>
                                <p class="f24 commodity_name"><?php echo ($vv["name"]); ?></p>
                                <div class="commodity_price f24">
                                    <span class="price">￥<?php echo ($vv["discount_price"]); ?></span>
                                    <a href="javascript:void(0);"  goodsId="<?php echo ($vv["id"]); ?>" class="shopping_cart">购物车</a>
                                </div>
                            </a>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <a class="view_more" href="javascript:void(0);" data-page="2" data-type="project" >查看更多<i></i></a><?php endif; endforeach; endif; ?>
    </div><?php endif; ?>
<!--单独显示每分类项目-->
<?php if(!empty($projectList)): ?><div class="commodity_category commodity_category_s">
        <ul>
            <?php if(is_array($projectList)): $i = 0; $__LIST__ = $projectList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-goodsid="<?php echo ($vo["id"]); ?>" data-buytype="1" data-categoryid="<?php echo ($vo["category_id_1"]); ?>"  data-type="1">
                    <a href="<?php echo U('Project/projectInfo',array('projectId'=> $vo['id'],'buyType'=>'1'));?>">
                        <div class="goods_img">
                            <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                                <?php else: ?>
                                <img data-img="" data-isloaded="" src="<?php echo (C("/Public/img")); ?>/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                        </div>
                        <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                        <div class="commodity_price f24">
                            <span class="price">￥<?php echo ($vo["discount_price"]); ?></span>
                            <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                        </div>
                    </a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div><?php endif; ?>


<!--加载更多正常项目-->
<?php if(!empty($projectListLoad)): if(is_array($projectListLoad["data"])): $kk = 0; $__LIST__ = $projectListLoad["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($kk % 2 );++$kk;?><li data-buytype="1" data-goodsid="<?php echo ($vv["id"]); ?>" data-categoryid="<?php echo ($vv["category_id_1"]); ?>" data-type="1">
            <a href="<?php echo U('Project/projectInfo',array('projectId'=> $vv['id'],'buyType'=>'1'));?>">
                <div class="goods_img">
                    <?php if(!empty($vv["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vv["main_img"]); ?>" alt="" class="c_img" />
                        <?php else: ?>
                        <img data-img="" data-isloaded="" src="<?php echo (C("/Public/img")); ?>/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                </div>
                <p class="f24 commodity_name"><?php echo ($vv["name"]); ?></p>
                <div class="commodity_price f24">
                    <span class="price">￥<?php echo ($vv["discount_price"]); ?></span>
                    <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                </div>
            </a>
        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>

<!--加载更多微团商品-->
<?php if(!empty($groupProjectListLoad)): if(is_array($groupProjectListLoad)): $kk = 0; $__LIST__ = $groupProjectListLoad;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($kk % 2 );++$kk;?><li data-buytype="3" data-goodsid="<?php echo ($vo["id"]); ?>" data-categoryid="<?php echo ($vo["category_id_1"]); ?>"  data-type="1">
            <a href="<?php echo U('Project/projectInfo',array('projectId'=> $vo['id'],'buyType'=>'3'));?>">
                <div class="goods_img">
                    <?php if(!empty($vo["main_img"])): ?><img data-img="" data-isloaded="" src="/Uploads/<?php echo ($vo["main_img"]); ?>" alt="" class="c_img" />
                        <?php else: ?>
                        <img data-img="" data-isloaded="" src="<?php echo (C("/Public/img")); ?>/home/default/goods_default.png" alt="" class="c_img" /><?php endif; ?>
                </div>
                <p class="f24 commodity_name"><?php echo ($vo["name"]); ?></p>
                <div class="commodity_price f24">
                    <span class="price">￥<?php echo ($vo["discount_price"]); ?></span>
                    <a href="javascript:void(0);" class="shopping_cart">购物车</a>
                </div>
            </a>
        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
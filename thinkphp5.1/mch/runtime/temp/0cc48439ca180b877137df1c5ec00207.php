<?php /*a:8:{s:71:"/home/www/web/thinkphp5.1/mch/application/index/view/scene/jiameng.html";i:1551259789;s:18:"template/base.html";i:1551077175;s:73:"/home/www/web/thinkphp5.1/mch/application/index/view/brand/set_brand.html";i:1551064593;s:77:"/home/www/web/thinkphp5.1/mch/application/index/view/public/sample_layer.html";i:1551064593;s:26:"template/login_dialog.html";i:1551077175;s:23:"template/login_tpl.html";i:1551077175;s:33:"template/forget_password_tpl.html";i:1551077175;s:25:"template/footer_menu.html";i:1551077175;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN"><head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('custom.title')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://mch.meishangyun.com/static/common/css/base.css">
	<link rel="stylesheet" href="http://mch.meishangyun.com/static/common/css/public.css">
	
	<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
		var domain = '<?php echo request()->domain(); ?>'+'/'+'index.php/';
		var module = domain + '<?php echo request()->module(); ?>/';
		var controller = module + '<?php echo request()->controller(); ?>/';
		var action = controller + '<?php echo request()->action(); ?>';
		var uploads = '<?php echo request()->domain(); ?>'+'/uploads/';
		var send_sms_url = '<?php echo url("ucenter/UserCenter/sendSms"); ?>';
		var walletPayCallBackParameter = {};
		var walletPayCallBack = function(parameter){
		};
	</script>
	<!--前置自定义js-->
	
</head><body>
<!-- 内容模块 -->


<article class="f24">
	<ul class="nav_menu">
		<li data-category-id="51" class="current">
			<a href="javascript:void(0);" class="category_nav">加盟套餐</a>
		</li>
		<li data-category-id="44">
			<a href="javascript:void(0);" class="category_nav">仪器选配</a>
		</li>
		<li data-category-id="45">
			<a href="javascript:void(0);" class="category_nav">开店装修</a>
		</li>
	</ul>
	<section class="slider_banner">
		<div class="swiper-container swiper-container-horizontal swiper-container-ios">
			<div class="swiper-wrapper">
				<!--<?php if(!(empty($info['goods_video']) || (($info['goods_video'] instanceof \think\Collection || $info['goods_video'] instanceof \think\Paginator ) && $info['goods_video']->isEmpty()))): ?>
				<div class="swiper-slide swiper-slide-active">
					<video  src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['goods_video']); ?>" autoplay="autoplay"></video>
				</div>
				<?php endif; if(is_array($info['main_img']) || $info['main_img'] instanceof \think\Collection || $info['main_img'] instanceof \think\Paginator): $i = 0; $__LIST__ = $info['main_img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$main_img): $mod = ($i % 2 );++$i;?>
				<div class="swiper-slide swiper-slide-active">
					<img src="http://mch.meishangyun.com/static/common/img/banner/cy_top_banner.jpg" alt="" class="common_default_img">
				</div>
				<?php endforeach; endif; else: echo "" ;endif; ?>-->
				<div class="swiper-slide swiper-slide-active">
					<img src="http://mch.meishangyun.com/static/common/img/banner/cy_top_banner.jpg" alt="" class="common_default_img">
				</div>
			</div>
		</div>
	</section>
	<section class="content-padding content_main wy_list" style="background:#F9CD33;">
		<div class="">
			<div class="columns_flex l-r-sides count_down_module">
				<div class="mid_module_left">
					<img src="http://mch.meishangyun.com/static/common/img/wy_dz.png" alt="">
					<div>
						<span class="left count-down-icons"></span>
						<div class="d-r">
							<p class="friday_end">每周采购<span>.</span>周五截止</p>
							<div id="countDownBox" class="count_down_box">
								<span class="day"></span>天
								<span class="hour"></span>小时
								<span class="minute"></span>分
								<span class="second"></span>秒
							</div>
						</div>
					</div>
				</div>
				<div class="mid_module_right">
					<span><span class="item end_time"></span>周五结单</span>
					<span><span class="item factory"></span>品质工厂</span>
				</div>
			</div>
		</div>
		<div class="apply_item">
			<span class="content-label">产品体验方案</span>
			<a href="">
				<img src="http://mch.meishangyun.com/static/common/img/apply.jpg" alt="">
			</a>
		</div>
		<div class="apply_item">
			<span class="content-label">中心店加盟方案</span>
			<a href="">
				<img src="http://mch.meishangyun.com/static/common/img/apply1.jpg" alt="">
			</a>
			<a href="">
				<img src="http://mch.meishangyun.com/static/common/img/apply2.jpg" alt="">
			</a>
		</div>
		<div class="apply_item">
			<span class="content-label">工作室加盟方案</span>
			<a href="">
				<img src="http://mch.meishangyun.com/static/common/img/apply3.jpg" alt="">
			</a>
			<a href="">
				<img src="http://mch.meishangyun.com/static/common/img/apply4.jpg" alt="">
			</a>
		</div>
	</section>
</article>


<!-- 公共模块-->

    <!--增加商标-->
<section class="add_brand_tpl bg-eae pt-5" style="display:none;">
    <form method="post" class="bg-fff brand_form">
        <!--<input type="hidden" class="" name="address_id" value="" />-->
        <div class="box-flex clearfixed">
            <input type="text" name="name" class="input-filed" placeholder="商标名称(只限第三类)" value="">
        </div>
        <!--<div class="box-flex clearfixed">
            <select name="type" class="input-filed">
                <option value="">商标类别</option>
                <?php if(is_array(app('config')->get('custom.brand_type')) || app('config')->get('custom.brand_type') instanceof \think\Collection || app('config')->get('custom.brand_type') instanceof \think\Paginator): $i = 0; $__LIST__ = app('config')->get('custom.brand_type');if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand_type): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo htmlentities($key); ?>"><?php echo htmlentities($brand_type); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>-->
        <div class="upload-picture-module box-flex f24">
			<div>
				<div class="picture-module">
					<input type="file" class="uploadImg uploadSingleImg" >
					<img class="upload_img" src="" alt="">
					<input type="hidden" name="logo" class="img" data-src="" value=""/>
				</div>
			</div>
            <div class="brand_txt">商标logo</div>
		</div>
        <div class="upload-picture-module box-flex f24">
			<div>
				<div class="picture-module">
					<input type="file" class="uploadImg uploadSingleImg">
					<img class="upload_img" src="" alt="">
					<input type="hidden" name="certificate" class="img" data-src="" value=""/>
				</div>
			</div>
            <div class="brand_txt">
                商标证或受理通知书
            </div>
		</div>
        <div class="upload-picture-module f24">
			<div>
				<div class="picture-module">
					<!--<input type="file" class="uploadImg uploadSingleImg">-->
					<img class="upload_img" src="http://mch.meishangyun.com/static/common/img/brand_template_img.jpg" alt="">
					<!--<input type="hidden" name="authorization" class="img" data-src="" value=""/>-->
				</div>
			</div>
            <div class="brand_txt example">
                <p>如非自有商标需所有权者授权</p>
                <a href="http://mch.meishangyun.com/static/common/img/brand_template_img.jpg" download="brand_template_img.jpg" class="template_btn pink">商品授权书样板></a>
            </div>
		</div>
    </form>
</section>
<!--设定品牌列表-->
<section class="brand_list_layer" style="display:none;">
    <ul class="list brand_list" id="">
        
    </ul>
    <a href="javascript:void(0);" class="add_brand">增加商标</a>
</section>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/uploadImgToTemp.js"></script>
<script type="text/javascript">
    //设置品牌弹窗
    function brandLayer(obj) {
        var brand_list_layer=$('.brand_list_layer').html();
        layer.open({
            title:['设定品牌','border-bottom:1px solid #d9d9d9;'],
            type:1,
            anim:'up',
            className:'setBrandLayer brandLayer',
            content:brand_list_layer,
            btn:['确定'],
            success:function(){
                var winH=$(window).height();
                $('.setBrandLayer .layui-m-layercont').css('height',winH-100+'px');
                var url = module + 'Brand/getList';
                var postData = {};
                $.ajax({
                    url: url,
                    data: postData,
                    type: 'post',
                    beforeSend: function(xhr){
                        $('.loading').show();
                    },
                    error:function(xhr){
                        $('.loading').hide();
                        dialog.error('AJAX错误！');
                    },
                    success: function(data){
                        $('.setBrandLayer').find('.list').html(data);
                        $('.setBrandLayer').find('.list').attr('id',obj.id);
                        $.each($('.setBrandLayer li'),function(){
                            var _this=$(this);
                            var layer_brand_name=_this.find('.brand_name').val();
                            if(layer_brand_name==obj.brand_name){
                                _this.addClass('current').siblings().removeClass('current');
                            }
                        })
                        cancleFixedLayer();
                    }
                });
                //
               
                    
               
            },
            yes:function(index){
                $.each($('.setBrandLayer li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        var brandName=_this.find('input[name="name"]').val();
                        var brand_id=_this.data('id');
                        var backId=$('.setBrandLayer').find('.list').attr('id');
                        $('#'+backId).find('.brand_name').text(brandName).data('id',brand_id);
                    }
                })
                layer.close(index);
            }
        })
    }
    $(function(){
        //设置品牌
        $('body').on('click','.set_brand',function () {
            var url = module + 'Brand/index';
            var id=$(this).parents('li').attr('id');
            var brand_name=$(this).find('.brand_name').text();
            var brand_li_id = {
                id:id,
                brand_name:brand_name
            };
            var postData = {};
            $.ajax({
                url: url,
                data: postData,
                type: 'post',
                beforeSend: function(xhr){
                    $('.loading').show();
                },
                error:function(xhr){
                    $('.loading').hide();
                    dialog.error('AJAX错误！');
                },
                success: function(data){
                    $('.loading').hide();
                    if(data.status==0){
                        dialog.error(data.info);
                    }else if(data.code==1){
                        if(data.data == 'no_login'){
                            loginDialog();
                            loginBackFunctionParameter.id =id;
                            loginBackFunction = brandLayer;
                        }
                    }else if(data.status==1){

                    }else{
                        brandLayer(brand_li_id);
                    }
                }
            });
        });

        //增加商标
        var addBrandInfo=$('.add_brand_tpl').html();
        $('body').on('click','.add_brand',function () {
            layer.open({
                title:['增加商标','border-bottom:1px solid #d9d9d9;'],
                type:1,
                anim:'up',
                className:'setBrandLayer',
                content:addBrandInfo,
                btn:['确定','取消'],
                success:function(){

                },
                yes:function(index){
                    var postData  = $(".setBrandLayer .brand_form").serializeObject();
                    //var brandTpl=$('.brandListTpl').html();
                    var content='';
                    if(!postData.name){
                        content='请填写商标名称';
                    }
                    // else if(!postData.type){
                    //     content='请选择商标类别';
                    // }
                    else if(!postData.logo){
                        content='请上传商标logo';
                    }else if(!postData.certificate){
                        content='请上传商标证或受理通知书';
                    }else if(!postData.authorization){
                        content='请上传商标所有权者授权书';
                    }
                    if(content){
                        dialog.error(content);
                        return false;
                    }
                    var config = {
                        url:module + 'Brand/edit',
                        postData:postData,
                        index:index
                    };
                    dialogFormAdd(config);
                    //添加或修改地址
                }
            });
        });
        //修改商标
        $('body').on('click','.edit_brand',function(event){
            event.stopPropagation();
            var _this=$(this).parents('.list li');
            layer.open({
                title:['修改商标','border-bottom:1px solid #d9d9d9;'],
                type:1,
                anim:'up',
                className:'setBrandLayer',
                content:addBrandInfo,
                btn:['确定','取消'],
                success:function(){
                    copyDataByName(_this,$('.setBrandLayer .brand_form'));
                    var logo=_this.find('input[name="logo"]').val();
                    var certificate=_this.find('input[name="certificate"]').val();
                    var authorization=_this.find('input[name="authorization"]').val();
                    $('.setBrandLayer').find('input[name="logo"]').prev().attr('src',uploads+logo);
                    $('.setBrandLayer').find('input[name="certificate"]').prev().attr('src',uploads+certificate);
                    $('.setBrandLayer').find('input[name="authorization"]').prev().attr('src',uploads+authorization);
                },
                yes:function(index){
                    var postData  = $(".setBrandLayer .brand_form").serializeObject();
                    postData.id = _this.data('id');
                    var brandTpl=$('.brandListTpl').html();
                    var content='';
                    if(!postData.name){
                        content='请填写商标名称';
                    }else if(!postData.type){
                        content='请选择商标类别';
                    }else if(!postData.logo){
                        content='请上传商标logo';
                    }else if(!postData.certificate){
                        content='请上传商标证或受理通知书';
                    }else if(!postData.authorization){
                        content='请上传商标所有权者授权书';
                    }
                    if(content){
                        dialog.error(content);
                        return false;
                    }
                   
                    postData.id = _this.data('id');
                    var config = {
                        url:module + 'Brand/edit',
                        postData:postData,
                        modifyObj:_this,
                        index:index
                    };
                    dialogFormEdit(config);
                   
                }
            });
        });
        //设定品牌
        $('body').on('click','.setBrandLayer li',function(){
            $(this).addClass('current').siblings().removeClass('current');
            // var brandName=$(this).find('input[name="name"]').val();
            // var brand_id=$(this).data('id');
            // var backId=$('.setBrandLayer').find('.list').attr('id');
            // $('#'+backId).find('.brand_name').text(brandName).data('id',brand_id);
            // setTimeout(function(){
            //     layer.closeAll();
            // },1000)
            return false;
        });
        //删除商标
        $('body').on('click','.delete_brand',function (event) {
            event.stopPropagation();
            var _this=$(this);
            layer.open({
                title:['删除商标','border-bottom:1px solid #d9d9d9;'],
                content:'确定删除商标吗？',
                btn:['确定','取消'],
                yes:function(index){
                    _this.parents('li').remove();
                    layer.close(index);
                }
            })
        });
        
    })

</script>
	<!--底部弹窗-->
<section id="goodsInfoLayer" style="display:none;">
	<div class="columns_flex layer_top_name">
        <div class="left l_img">
		    <img src="" alt="" class="goods_img">
        </div>
		<p class="goods_title">台湾原装进口 真世酵素纤丽果冻</p>
	</div>
	<ul class="bottom-layer-info goods_list">
        <li data-id="" data-buy_type="2">
			<div class="columns_flex l-r-sides">
				<div >
					<span class="red">￥<prices class="sample_price">100.00</price></span>
					<span class="specification">300ml/瓶</span>
				</div>
				<div>样品购买限额：<span class="minimum_sample_quantity"></span></div>
			</div>
            <div class="columns_flex l-r-sides">
                <span>采购数量(单位：<?php echo htmlentities(getUnit($info['purchase_unit'])); ?>)</span>
                <div class="quantity_wrapper selected-number">
                    <a href="javascript:void(0);" class="greduce" data-type="sample">-</a>
                    <input type="text" value="0" class="f24 gshopping_count" data-type="sample">
					<input type="hidden" class="minimum_order_quantity" value="">
					<input type="hidden" class="increase_quantity" value="">
                    <a href="javascript:void(0);" class="gplus" data-type="sample">+</a>
                </div>
            </div>
        </li>
    </ul>
	<footer class="f24 group_cart_nav" style="display: flex;">
        <div class="group_btn30 bottom_item">
            总计:<span class="amount">￥<price>0.00</price></span>
        </div>
        <div class="group_btn30 bottom_item">
            <a href="javascript:void(0);" class=" add_cart_icon"><span class="add_num">+3</span><span class="cart_num"></span>购物车</a>
        </div>
        <div class="group_btn30 bottom_item">
            <a href="javascript:void(0);" class=" add_cart_layer">加入购物车<span class="total_num"></span></a>
        </div>
    </footer>
</section>

<section id="dialLogin" style="display:none;">
    <form class="loginTab active" id="formLogin">
	<div class="login_item">
		<div class="columns_flex">
			<span>中国(+86)</span>
			<input class="username user_phone input-filed" type="tel" placeholder="请输入手机号码" name="mobile_phone">
		</div>
	</div>
	<div class="login_item">
		<div class="columns_flex">
			<input class="input-filed password" type="password" placeholder="密码" name="password">
			<a href="javascript:void(0);" class="hidden view-password" ></a>
		</div>
	</div>
	<a href="javascript:void(0);" class="forget_password forget_dialog">注册/重置密码</a>
	<div class="error_tipc"></div>
	<a href="javascript:void(0);" class="loginBtn entry-button"  data-method="login">登录</a>
</form>
</section>

<section id="sectionForgetPassword" style="display:none;">
    <form id="formForgetPassword">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="http://mch.meishangyun.com/static/common/img/ucenter_logo.png" alt="">
        </div>
        <div class="loginNav">
            <span class="current resetpassword">注册/重置密码</span>
        </div>
        <div class="error_tipc" ></div>
        <div class="login_item">
            <div class="columns_flex">
                <span>中国(+86)</span>
                <input class="username user_phone input-filed" type="tel" placeholder="请输入手机号码" name="mobile_phone">
            </div>
        </div>
        <div class="smsLogin login_wrap">
            <div class="login_item">
                <div class="columns_flex l-r-sides">
                    <input type="text" class="tel_code input-filed" placeholder="请输入收到的验证码" name="captcha">
                    <a href="javascript:void(0);" class="send_sms">获取验证码</a>
                </div>
            </div>
        </div>
        <div class="login_item">
            <div class="columns_flex">
                <input class="input-filed password" autocomplete="new-password" type="password" placeholder="设置密码" name="password">
                <a href="javascript:void(0);" class="hidden view-password" ></a>
            </div>
        </div>
    </div>
    <a href="javascript:void(0);" class="comfirmBtn entry-button"  data-method="forgetPassword">确定</a>
</form>
</section>

<!-- 页脚模块-->

	
<footer class="f24 group_cart_nav">
    <?php if(is_array($unlockingFooterCart['menu']) || $unlockingFooterCart['menu'] instanceof \think\Collection || $unlockingFooterCart['menu'] instanceof \think\Paginator): $i = 0; $__LIST__ = $unlockingFooterCart['menu'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($unlockingFooterCart['count'] == 1): ?>
            <div class="group_btn100 bottom_item">
                <?php if($vo['class']=='amount'): ?>
                <span>￥<price>500</price></span>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 2): ?>
            <div class="group_btn50 bottom_item">
                <?php if($vo['class']=='checked_all'): ?>
                <div class="select_checkbox_box <?php echo htmlentities($vo['class']); ?>">
                    <input type="checkbox" name="" value="" id="" class="item_checkbox checkall ">
                    <label for="" class="left">全选</label>
                </div>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 3): ?>
            <div class="group_btn30 bottom_item">
                <?php if($vo['class']=='amount'): ?>
                总计:<span class="amount">￥<price>0.00</price></span>
                <?php elseif($vo['class']=='add_cart_icon'): ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="add_num"></span><span class="cart_num"></span></a>
                <?php elseif($vo['class']=='checked_all'): ?>
                <div class="select_checkbox_box <?php echo htmlentities($vo['class']); ?>">
                    <input type="checkbox" name="" value="" id="" class="item_checkbox checkall ">
                    <label for="" class="left">全选</label>
                    <a href="javascript:void(0);" class="goodsDel detele_carts">删除</a>
                </div>
                <?php else: ?>
                <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><?php echo htmlentities($vo['name']); ?><span class="total_num"></span></a>
                <?php endif; ?>
            </div>
        <?php elseif($unlockingFooterCart['count'] == 4): ?>
        <div class="group_btn25 bottom_item">
            <a href="javascript:void(0);" class="<?php echo htmlentities($vo['class']); ?>"><span class="s_i"></span><?php echo htmlentities($vo['name']); ?></a>
        </div>
        <?php else: endif; endforeach; endif; else: echo "" ;endif; ?>
</footer>

<!--js基本插件-->
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/swiper.min.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/index/js/footerCart.js"></script>
<script type="text/javascript">
	$(function(){
		var id = '<?php echo htmlentities($info['id']); ?>';
		var date=getWeek();
		countDown(date,$('#countDownBox'));
		//查看文本
		$('.wk_notice').moreText({
			mainCell:".more-text",
			openBtn:'展开'
		});
	});
</script>

</body></html>
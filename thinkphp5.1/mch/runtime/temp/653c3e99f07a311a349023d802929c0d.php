<?php /*a:6:{s:65:"D:\web\thinkphp5.1\mch\application/index/view\address\manage.html";i:1551065534;s:18:"template/base.html";i:1551083825;s:25:"template/footer_menu.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN"><head>
	<meta charset="UTF-8">
	<title><?php echo htmlentities(app('config')->get('custom.title')); ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="stylesheet" href="http://mch.new.com/static/common/css/base.css">
	<link rel="stylesheet" href="http://mch.new.com/static/common/css/public.css">
	


	<script type="text/javascript" src="http://mch.new.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
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

<section class="f20 header_title separation-line">
	<a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
	<h2 class="">收货地址列表</h2>
</section>
<article class="f24">
	<section class="delivery_address">
		<input type="hidden" value="<?php echo htmlentities($orderId); ?>" class="order_id">
		<?php if(!(empty($addressList) || (($addressList instanceof \think\Collection || $addressList instanceof \think\Paginator ) && $addressList->isEmpty()))): if(is_array($addressList) || $addressList instanceof \think\Collection || $addressList instanceof \think\Paginator): $i = 0; $__LIST__ = $addressList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<div class="item_addr">
				<div class="ia-l"></div>
				<div class="item_info">
					<input type="hidden" value="<?php echo htmlentities($vo['id']); ?>" class="address_id"> 
					<div class="mt_new">
						<span><?php echo htmlentities($vo['consignee']); ?></span>
						<span><?php echo htmlentities($vo['mobile']); ?></span>
						<?php if($vo['is_default'] == 1): ?>
						<i class="default_tipc">默认</i>
						<?php endif; ?>
					</div>
					<p class=""><span class="area_address" id="<?php echo htmlentities($vo['id']); ?>"></span><?php echo htmlentities($vo['detail_address']); ?></p>
					<input type="hidden" class="area-address-name" value="" data-province="<?php echo htmlentities($vo['province']); ?>" data-city="<?php echo htmlentities($vo['city']); ?>" data-area="<?php echo htmlentities($vo['area']); ?>">
				</div>
				<a href="javascript:void(0)" class="address_delete">删除</a>
				<a href="javascript:void(0);" class="ia-r address_edit">
					<span class="iar-icon"></span>
				</a>
			</div>
		<?php endforeach; endif; else: echo "" ;endif; else: ?>
		<div class="no_data">
			<img src="http://mch.new.com/static/common/img/no-address.png" alt="">
		</div>
		<?php endif; ?>
	</section> 
</article> 

<!-- 公共模块-->


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
            <img src="http://mch.new.com/static/common/img/ucenter_logo.png" alt="">
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

<!--js基本插件-->
<script type="text/javascript" src="http://mch.new.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.new.com/static/common/js/jquery.area.js"></script>
<script type="text/javascript">
	$(function(){
		$('.delivery_address .item_info').each(function(index,val){
			//省市区初始化
			var _this=$(this);
			var province=_this.find('.area-address-name').data('province');
			var city=_this.find('.area-address-name').data('city');
			var area=_this.find('.area-address-name').data('area');
			var region = [];
			if(province && city && area){
				region.push(province);
				region.push(city);
				region.push(area);
				_this.find('.area_address').setArea(region);
			}

		});

		//新建地址
		$('body').on('click','.address_create',function () {
			location.href = module + 'Address/edit';
		});

		//跳转到地址列表
		$('.item_info').click(function () {
			var addressId = $(this).find('.addressId').val();
			var orderId = $(this).parents().find('.orderid').val();
			if(orderId){
				location.href = module + 'Order/orderDetail/order_id/'+orderId+'/address_id/'+addressId;
			}
		});

		//修改地址
		$('body').on('click','.address_edit',function () {
			var addressId = $(this).parent().find('.address_id').val();
			var url = controller + 'edit/address_id/'+addressId;
			var orderId = $(this).parents().find('.orderid').val();
			if(orderId){
				url += '/order_id/'+orderId;
			}
			location.href = url;
		});

		//删除地址
		$('body').on('click','.address_delete',function () {
			var _this = $(this);
			var postData = {};
			postData.address_id = _this.siblings().find('.address_id').val();
			layer.open({
				content : '删除你选中的地址吗',
				time:5,
				btn : ['是','否'],
				title: '提示',
				yes : function(index){
					_this.addClass("nodisabled");//防止重复提交
					var url = module + '/Address/del';
					$.ajax({
						url: url,
						data: postData,
						type: 'post',
						beforeSend: function(){
							$('.loading').show();
						},
						error:function(){
							$('.loading').hide();
							dialog.error('AJAX错误');
						},
						success: function(data){
							_this.removeClass("nodisabled");//删除防止重复提交
							$('.loading').hide();
							if(data.status == 1){
								_this.parents('.item_addr').remove();
							}
							if(data.status == 0){
								dialog.error(data.info);
							}
						}
					});
					layer.close(index);
				}
			});

		})
	});
</script>

</body></html>
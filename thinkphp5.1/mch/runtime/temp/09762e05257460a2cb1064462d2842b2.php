<?php /*a:6:{s:63:"D:\web\thinkphp5.1\mch\application/index/view\address\edit.html";i:1551065534;s:18:"template/base.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;s:25:"template/footer_menu.html";i:1551083825;}*/ ?>
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

	<section class="f20">
		<?php if(empty($address) || (($address instanceof \think\Collection || $address instanceof \think\Paginator ) && $address->isEmpty())): ?>
			<h2 class="address_list_title">新建收货地址</h2>
			<?php else: ?>
			<h2 class="address_list_title">编辑收货地址</h2>
		<?php endif; ?>
	</section>
	<section class="section-address bg-eae pt-5">
		<form action='#' method="post" class="bg-fff form">
			<input type="hidden" name="address_id" value="<?php echo htmlentities($address['id']); ?>">
			<input type="hidden" name="order_id" value="<?php echo htmlentities($orderId); ?>">
			<div class="form-group box-flex clearfixed">
				<label class="form-label">收件人：</label>
				<input type="text" name="consignee" class="input-flex recipient_name" placeholder="请输入姓名" value="<?php echo htmlentities($address['consignee']); ?>">
			</div>
			<div class="form-group box-flex clearfixed">
				<label class="form-label">联系电话：</label>
				<input type="text" name="mobile" class="input-flex recipient_mobile" placeholder="请输入收件人电话" value="<?php echo htmlentities($address['mobile']); ?>">
			</div>
			<div class="express-area ">
				<a id="expressArea" href="javascript:void(0)" class="f24 form-group box-flex">
					<label class="form-label">选择地区：</label>
					<?php if(empty($address) || (($address instanceof \think\Collection || $address instanceof \think\Paginator ) && $address->isEmpty())): ?>
						<div class="area_address">省 &gt; 市 &gt; 区/县</div>
						<?php else: ?>
						<div class="area_address"></div>
					<?php endif; ?>
					<input type="hidden"  data-key="" value="" class="area-address-name" >
				</a>
			</div>
			<div class="form-group box-flex">
				<label class="form-label">详细地址：</label>
				<input type="text" name="detail_address" class="input-flex recipient_detail_address" placeholder="请填写详细地址" value="<?php echo htmlentities($address['detail_address']); ?>">
			</div>
			<div class="set_default_item f24">
				<div>
					<span>设为默认地址</span>
					<p>注：每次下单时会使用该地址</p>
				</div>
				<?php if($address['is_default'] == 1): ?>
				<div class="myswitch myswitched"  data-off="<?php echo htmlentities($address['is_default']); ?>"></div>
				<?php else: ?>
				<div class="myswitch"  data-off="0"></div>
				<?php endif; ?>

			</div>
		</form>
	</section>
	<!--选择地区弹层-->
	<section id="areaLayer" class="express-area-box" style="display:none;">
		<header>
			<h3>选择地区</h3>
			<a id="backUp" class="back" href="javascript:void(0);" title="返回" style="display: none;"></a>
			<a id="closeArea" class="close" href="javascript:void(0);" title="关闭"></a>
		</header>
		<article id="areaBox">
			<ul id="areaList" class="area-list">
				<!--<li onclick="selectP(0);">北京市</li><li onclick="selectP(1);">天津市</li><li onclick="selectP(2);">河北省</li><li onclick="selectP(3);">河南省</li><li onclick="selectP(4);">山西省</li><li onclick="selectP(5);">山东省</li><li onclick="selectP(6);">内蒙古自治区</li><li onclick="selectP(7);">辽宁省</li><li onclick="selectP(8);">吉林省</li><li onclick="selectP(9);">黑龙江省</li><li onclick="selectP(10);">上海市</li><li onclick="selectP(11);">江苏省</li><li onclick="selectP(12);">浙江省</li><li onclick="selectP(13);">福建省</li><li onclick="selectP(14);">江西省</li><li onclick="selectP(15);">安徽省</li><li onclick="selectP(16);">湖北省</li><li onclick="selectP(17);">湖南省</li><li onclick="selectP(18);">广东省</li><li onclick="selectP(19);">广西壮族自治区</li><li onclick="selectP(20);">海南省</li><li onclick="selectP(21);">重庆市</li><li onclick="selectP(22);">四川省</li><li onclick="selectP(23);">贵州省</li><li onclick="selectP(24);">云南省</li><li onclick="selectP(25);">西藏自治区</li><li onclick="selectP(26);">陕西省</li><li onclick="selectP(27);">甘肃省</li><li onclick="selectP(28);">青海省</li><li onclick="selectP(29);">宁夏回族自治区</li><li onclick="selectP(30);">新疆维吾尔自治区</li><li onclick="selectP(31);">台湾省</li><li onclick="selectP(32);">香港特别行政区</li><li onclick="selectP(33);">澳门特别行政区</li>-->
			</ul>
		</article>
	</section>
	<!--遮罩层-->
	<div id="areaMask" class="address_mask"></div>

<!-- 公共模块-->



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
		$(function () {
			//省市区初始化
			var province = '<?php echo htmlentities((isset($address['province']) && ($address['province'] !== '')?$address['province']:"")); ?>';
			var city = '<?php echo htmlentities((isset($address['city']) && ($address['city'] !== '')?$address['city']:"")); ?>';
			var area = '<?php echo htmlentities((isset($address['area']) && ($address['area'] !== '')?$address['area']:"")); ?>';
			var region = [];
			if(province && city && area){
				region.push(province);
				region.push(city);
				region.push(area);
				$('.area_address').setArea(region);
			}

			//设定默认地址
			$('.myswitch').on('click',function(){
				if($(this).hasClass('myswitched')){
					$(this).removeClass('myswitched');
					$(this).attr('data-off',0);
				}else{
					$(this).addClass('myswitched');
					$(this).attr('data-off',1);
				}
			});

			$('body').on('click','.address_save',function () {
				var area_address =$('.area-address-name').getArea();
				var postData  = $(".form").serializeObject();
				var content='';
				if(!postData.consignee){
					content='请填写收货人姓名';
				}else if(!register.phoneCheck(postData.mobile)){
					content='请填写正确的手机号码';
				}else if(!area_address){
					content='请选择地区';
				}else if(!postData.detail_address){
					content='请填写详细地址';
				}
				if(content){
					dialog.error(content);
					return false;
				}
				postData.is_default = $('.myswitch').attr('data-off');
				postData.province = area_address[0];
				postData.city = area_address[1];
				postData.area = area_address[2];
				//添加或修改地址
				var _this = $(this);
				_this.addClass("nodisabled");//防止重复提交
				var url = module + 'Address/edit';
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
						if(data.status == 0){
                            dialog.error(data.info);
						}
						location.href = module + 'Address/manage';
					}
				});

			})
		})
	});
</script>

</body></html>
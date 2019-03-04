<?php /*a:6:{s:67:"D:\web\thinkphp5.1\mch\application/index/view\collection\index.html";i:1551065534;s:18:"template/base.html";i:1551083825;s:25:"template/footer_menu.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;}*/ ?>
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

<section class="no_collection" style="display:none;">
	<li class="no_data">
		暂无收藏商品信息
	</li>
</section>
<article class="f24 article_main_content collection_main_content">
	<section class="header_title separation-line">
		<a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2 class="f24">商品收藏</h2>
	</section>
	<section >
		<ul class="collection_list list" id="list">

		</ul>
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

<script type="text/javascript">
	var config = {
		url:module+'Collection/getList',
		requestEnd:false,
		loadTrigger:false,
		currentPage:1
	};
	var postData = {
		pageSize:10,
		pageType:'list_tpl'
	};
	$(function(){
		//初始化页面
		getPagingList(config,postData);
		//下拉加载
		$(window).on('scroll',function(){
			if(config.loadTrigger && $(document).scrollTop()+$(window).height()+200>$(document).height()){
				config.loadTrigger = false;
				getPagingList(config,postData);
			}
		});

//		//选择删除购物车
		$('body').on('click','.delete',function(){
			var _This=$(this);
			var postData = {};
			var ids = [];
			$.each($('.list li'),function(){
				var _this=$(this);
				if(_this.find('.sign_checkitem').is(':checked')){
					var id = _this.data('id');
					ids.push(id);
				}
			});
			postData.ids =ids;
			if($.isEmptyArray(ids)){
				dialog.error('请选择需要删除的收藏商品！');
				return false;
			}
			var url = module + 'Collection/del'
			_This.addClass("nodisabled");//防止重复提交
			layer.open({
				content:'是否删除？',
				btn:['确定','取消'],
				yes:function(index){
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
							$('.loading').hide();
							_This.removeClass("nodisabled");//防止重复提交
							if(data.status==0){
								dialog.error(data.info);
							}else {
								$.each($('.list li'),function(){
									var _this=$(this);
									var id = _this.data('id');
									if(_this.find('.sign_checkitem').is(':checked')){
										_this.remove();
									}
								});

								if(!$('.list li').length){
									var html=$('.no_collection').html();
									$('.list').append(html);
								}
							}
						}
					});
					layer.close(index);
				}
			});
		});
	});
</script>

</body></html>
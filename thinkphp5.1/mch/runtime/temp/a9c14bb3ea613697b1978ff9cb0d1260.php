<?php /*a:6:{s:61:"D:\web\thinkphp5.1\mch\application/index/view\mine\index.html";i:1551259371;s:18:"template/base.html";i:1551083825;s:31:"template/wallet_pay_dialog.html";i:1551083825;s:26:"template/login_dialog.html";i:1551083825;s:23:"template/login_tpl.html";i:1551083825;s:33:"template/forget_password_tpl.html";i:1551083825;}*/ ?>
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

<!--个人信息-->
<section class="user_info_content" style="display:none;">
    <ul>
        <li class="columns_flex l-r-sides">
            <div>
            <span>头像</span>
            </div>
            <div class="upload-picture-module box-flex f24">
                <div>
                    <div class="picture-module">
                        <input type="file" class="editAvatar uploadImg">
                        <img class="upload_img" src="" alt="">
                        <input type="hidden" name="logo" class="img" data-src="" value=""/>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:;" class="right-arrow nick_btn">
                <span >昵称</span>
                <span class="user_name"></span>
                <input type="hidden" class="old_name" value="<?php echo htmlentities($user['name']); ?>">
            </a>
        </li>
        <li>
            <a href="javascript:;" id="" class="set_wallet_psd right-arrow" data-type="set">设置钱包支付密码</a>
        </li>
        <li>
            <a href="javascript:;" id="logout_dialog" class="sign_out right-arrow">退出</a>
        </li>
    </ul>
</section>
<!--昵称信息-->
<section class="nick_content" style="display:none;">
    <div>
        <input type="text" name="" class="input-filed user_name">

    </div>
</section>
<article class="person-center-wrapper f24">
    <section class="separation-line columns_flex l-r-sides flex_item">
        <?php if(empty($user) || (($user instanceof \think\Collection || $user instanceof \think\Paginator ) && $user->isEmpty())): ?>
        <div class="user_top">
            <a href="javascript:void(0);" id="login_dialog">
               <img src="http://mch.new.com/static/common/img/default/head_50.png" alt="">
               注册登录
           </a>
        </div>
        <?php else: ?>
        <div class="user_top_item user_info_list">
            <?php if(empty($user['avatar']) || (($user['avatar'] instanceof \think\Collection || $user['avatar'] instanceof \think\Paginator ) && $user['avatar']->isEmpty())): ?>
            <img src="http://mch.new.com/static/common/img/default/chat_head.jpg" class="left user_header">
            <?php else: ?>
            <img src="http://mch.new.com/uploads/<?php echo htmlentities($user['avatar']); ?>" class="left user_header">
            <?php endif; ?>
            <div class="user_info_r">
                <p class="user_name"><?php echo htmlentities($user['name']); ?></p>
                <p class="user_mobile"><?php echo htmlentities($user['mobile_phone']); ?></p>
            </div>
        </div>
        <!--<a href="javascript:void(0);" id="logout_dialog" class="sign_out">
            退出>
        </a>-->
        <a href="javascript:void(0);" class="customer_info">
            <img src="http://mch.new.com/static/common/img/modify.png" alt="">
        </a>
        <?php endif; ?>

    </section>
    <section class="separation-line">
        <div class="flex_item">
            <a href="javascript:void(0)" class="person-td my_message" data-jump_url="<?php echo url('MessageCenter/index'); ?>">
                <span class="my-msg">
                    <i class="num"></i>
                </span>
                消息
            </a>
            <a href="javascript:void(0)" class="person-td my_collection"  data-jump_url="<?php echo url('Collection/index'); ?>">
                <span class="collec"></span>
                我的收藏
            </a>
            <a href="<?php echo url('Notice/index'); ?>" class="person-td">
                <span class="notice"></span>
                二维码
            </a>
        </div>
    </section>
    <section class="separation-line">
        <div class="flex_item">
            <a href="javascript:void(0)" class="person-td my_brand"  data-jump_url="<?php echo url('Brand/index'); ?>">
                <span class="brand"></span>
                代金券
            </a>
            <a href="javascript:void(0)" class="person-td recharge" data-jump_url="<?php echo url('Wallet/recharge'); ?>">
                <span class="charge"></span>
                采购充值
            </a>
            <a href="<?php echo url('Report/index'); ?>" class="person-td">
                <span class="pur_wallet"></span>
                采购钱包
            </a>
            <a href="<?php echo url('Report/index'); ?>" class="person-td">
                <span class="s_wallet"></span>
                收益钱包
            </a>
        </div>
    </section>
    <section class="separation-line">
        <div class="flex_item">
            <a href="javascript:void(0)" class="person-td my_cart"  data-jump_url="<?php echo url('Cart/index'); ?>">
                <span class="cart"></span>
                采购车
            </a>
            <a href="javascript:void(0)" class="person-td address_manage" data-jump_url="<?php echo url('Address/manage'); ?>">
                <span class="addr"></span>
                收货地址
            </a>
            <a href="javascript:void(0)" class="person-td order_manage"  data-jump_url="<?php echo url('Order/manage',['order_status'=>1]); ?>">
                <span class="wait_pay"></span>
                待付款
            </a>
            <a href="javascript:void(0)" class="person-td order_manage"  data-jump_url="<?php echo url('Order/manage',['order_status'=>2]); ?>">
                <span class="wait_recevie"></span>
                待收货
            </a>
        </div>
        <div class="flex_item">
            <a href="javascript:void(0)" class="person-td order_manage" data-jump_url="<?php echo url('Order/manage',['order_status'=>3]); ?>">
                <span class="commit"></span>
                待评价
            </a>
            <a href="javascript:void(0)" class="person-td order_manage"  data-jump_url="<?php echo url('Order/manage',['order_status'=>6]); ?>">
                <span class="after_s"></span>
                售后
            </a>
            <a href="javascript:void(0)" class="person-td order_manage" data-jump_url="<?php echo url('Order/manage',['order_status'=>4]); ?>">
                <span class="complete"></span>
                已完成
            </a>
            <a href="javascript:void(0)" class="person-td order_manage" data-jump_url="<?php echo url('Order/manage',['order_status'=>0]); ?>">
                <span class="all"></span>
                全部
            </a>
        </div>
    </section>
</article>
<section class="bottom_nav_fixed bottom_white">
	<nav class=" foot_nav_bar">
		<ul class="columns_flex">
			<li class="each_column">
				<a href="<?php echo url('Index/index'); ?>">
					<span class="store f_icon"></span>
					<span class="f_txt">美创会</span>
				</a>
			</li>
			<li class="each_column">
				<a href="<?php echo url('Company/index'); ?>">
					<span class="practitioners f_icon"></span>
					<span class="f_txt">中心店</span>
				</a>
			</li>
			<li class="each_column">
				<a href="<?php echo url('Consultation/index'); ?>">
					<span class="business f_icon"></span>
					<span class="f_txt">工作室</span>
				</a>
			</li>
			<li class="each_column">
				<a href="javascript:void(0)" class="my_cart" data-jump_url="<?php echo url('Cart/manage'); ?>">
					<span class="cart f_icon"></span>
					<span class="f_txt">采购车</span>
				</a>
			</li>
			<li class="each_column current">
				<a href="<?php echo url('Mine/index'); ?>">
					<span class="my f_icon"></span>
					<span class="f_txt">我的</span>
				</a>
			</li>
		</ul>
	</nav>
</section>

<!-- 公共模块-->

<section id="walletPay" style="display:none;">
    <div class="payment_password">
        <ul class="payment_password_list" id="wrap">
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
            <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
        </ul>
        <div class="login_item">
            <a href="javascript:void(0);" class="forget_wallet_password">忘记支付密码/设置密码</a>
        </div>
    </div>
</section>
<section id="WalletPasswordHtml" style="display:none;">
    <div class="f24 bomb_box">
        <form class="" id="ForgetWalletPassword">
            <div class="login_item">
                <div class="columns_flex">
                    <span>中国(+86)</span>
                    <input class="username user_phone input-filed" readonly="readonly" type="tel" placeholder="请输入手机号码" name="mobile_phone" value="<?php echo htmlentities($user['mobile_phone']); ?>">
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
                    <ul class="payment_password_list" id="wrap">
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                        <li class=""><input class="password_item" type="password" name="key" placeholder="-" data-index=""></li>
                    </ul>
                    <!--<input class="input-filed password" autocomplete="new-password" type="password" placeholder="设置密码" name="password">-->
                    <!--<a href="javascript:void(0);" class="hidden view-password" ></a>-->
                </div>
            </div>
        </form>
    </div>

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

<script type="text/javascript" src="http://mch.new.com/static/index/js/jump_login.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/wallet.js"></script>
<script type="text/javascript" src="<?php echo request()->scheme(); ?>://api.map.baidu.com/api?v=2.0&ak=Fimtwu6FDRrHWvg3oq44YCw0c0YonNpv"></script>
<script type="text/javascript" src="<?php echo request()->scheme(); ?>://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.js"></script>
<script type="text/javascript">
    $(function () {
        var mobile=$('.user_mobile').text();
        $('.user_mobile').text(mobileNHide(mobile));
        //个人信息
        var user_info_content=$('.user_info_content').html();
        $('body').on('click','.customer_info',function () {
            var _this=$(this);
            layer.open({
                title:['个人信息','border-bottom:1px solid #d9d9d9;'],
                type:1,
                anim:'up',
                className:'userInfoLayer',
                content:user_info_content,
                btn:['确定'],
                success:function(){
                    var user_header=_this.prev().find('.user_header').attr('src');
                    var user_name=_this.prev().find('.user_name').text();
                    $('.userInfoLayer').find('.upload_img').attr('src',user_header);
                    $('.userInfoLayer .user_name').text(user_name);
                },
                yes:function(index){
                    layer.close(index);
                }
            });
        });

        var uploadsSingleImgFlag = true; //上传单图片成功标识位
        // 选择头像图片
        $('body').on('change','.editAvatar',function () {
            var _this=$(this);
            uploadsSingleImgFlag = false;
            var img = event.target.files[0];
            var uploadfileSize=img.size/1024/1024;
            var obj=$(this).parent();
            // 判断是否图片
            if(!img){
                return false;
            }
            // 判断图片格式
            var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
            if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
                dialog.error('请上传：jpg、jpeg、png、gif格式图片');
                return false;
            }
            if(uploadfileSize>1){
                dialog.error('图片大小不能超过1M');
                return false;
            }
            var reader = new FileReader();
            reader.readAsDataURL(img);
            reader.onload = function(e){
                var imgUrl=e.target.result;
                $(obj).addClass('active');
                var postData = {fileBase64: e.target.result};
                // postData.imgWidth = 145;
                // postData.imgHeight = 100;
                $(obj).find('img').attr('src',imgUrl);
                var type = _this.data('type');
                if(type == 'notupload'){
                    $(obj).find('.img').val(imgUrl);
                    return false;
                }
                //提交
                $.post(module+"Mine/editAvatar",postData,function(data){
                    if(data.status == 1){
                        uploadsSingleImgFlag = true;
                        $(obj).find('.img').val(data.avatar);
                        dialog.success(data.info);
                        $('.user_info_list').find('.user_header').attr('src',uploads+data.avatar);
                    }else{
                        dialog.error(data.info)
                    }
                })
            }
        });
        //昵称修改
        var nick_content=$('.nick_content').html();
         $('body').on('click','.nick_btn',function () {
            var _this=$(this);
            layer.open({
                title:['昵称','border-bottom:1px solid #d9d9d9;'],
                type:1,
                anim:'up',
                className:'nickInfoLayer',
                content:nick_content,
                btn:['确定'],
                success:function(){
                    var user_name=_this.find('.user_name').text();
                    $('.nickInfoLayer .user_name').val(user_name);
                },
                yes:function(index){
                  var user_name=  $('.nickInfoLayer .user_name').val();
                  $('.userInfoLayer').find('.user_name').text(user_name);
                   var  postData = {
                       name:user_name
                   }
                    var old_name =   $('.userInfoLayer ').find('.old_name').val();
                    if(old_name !== user_name){
                        //提交
                        $.post(module+"Mine/editName",postData,function(data){
                            if(data.status == 1){
                                dialog.success(data.info);
                                $('.user_info_list').find('.user_name').text(data.name);
                                $('.userInfoLayer ').find('.old_name').val(data.name);
                            }else{
                                dialog.error(data.info)
                            }
                        })
                    }

                  layer.close(index);
                }
            });
        });
        //设置钱包支付密码
        $('body').on('click','.set_wallet_psd',function () {
            var type=$(this).data('type');
            forgetWalletPasswordDialog(type);
        });
       
    })
</script>

</body></html>
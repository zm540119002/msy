<?php /*a:5:{s:73:"/home/www/web/thinkphp5.1/mch/application/index/view/wallet/recharge.html";i:1551340852;s:18:"template/base.html";i:1551077175;s:26:"template/login_dialog.html";i:1551077175;s:23:"template/login_tpl.html";i:1551077175;s:33:"template/forget_password_tpl.html";i:1551077175;}*/ ?>
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

<!--转账凭证弹窗-->
<section class="bank_transfer_tpl" style="display:none;">
    <form id="bankTransfer">
        <div class="upload-picture-module f24">
			<div>
				<div class="picture-module">
					<input type="file" class="uploadImg uploadSingleImg" >
					<img class="upload_img" src="" alt="">
					<input type="hidden" name="voucher" class="img" data-src="" value=""/>
				</div>
			</div>
		</div>
        <p>上传转账汇款凭证(截屏或拍照)，以便通知客服人员及时进行账户人工充值，以免影响采购结算</p>
        <a href="javascript:;" class="entry_btn">确定</a>
    </form>
</section>
<article class="f24 pay_wrapper">
    <section class="header_title separation-line">
        <a href="javascript:void(0);" class="back_prev_page" data-jump_url="<?php echo url('Mine/index'); ?>"></a>
		<h2 class="f24">采购充值</h2>
	</section>
    <section class="recharge_wallet_item">
        <span class="content-label">采购钱包余额</span>
        <div class="wallet_balance">¥<span>5000</span></div>
    </section>
    <section class="recharge_amount_item content-padding">
        <div class="columns_flex l-r-sides">
            <span>选择充值金额</span>
            <span>充值记录></span>
        </div>
        <ul class="recharge_m_list">
            <li>
                <span>1000</span>元
            </li>
            <li>
                <span>5000</span>元
            </li>
            <li>
                <span>10000</span>元
            </li>
            <li>
                <span>20000</span>元
            </li>
            <li>
                <span>50000</span>元
            </li>
            <li>
                <span>100000</span>元
            </li>
        </ul>
    </section>
    <section class="content-padding ">
        <p class="content-label">选择充值方式</p>
        <div>
            <ul class="pay_nav">
                <li data-paycode="1">
                    微信支付
                    <!--<input type="checkbox" checked="checked" class=""/>-->
                    <span class="selected"></span>
                    <span class="wx"></span>
                </li>
                <li data-paycode="2">
                    支付宝支付
                    <!--<input type="checkbox" class=""/>-->
                    <span class="selected"></span>
                    <span class="wx"></span>
                <!--</li>-->
                <!--<li data-paycode="3">银联支付-->
                    <!--&lt;!&ndash;<input type="checkbox" class=""/>&ndash;&gt;-->
                    <!--<span class="selected"></span>-->
                    <!--<span class="wx"></span>-->
                <!--</li>-->
            </ul>
        </div>
        <div class="bank_transfer_item">
            <span class="content-label">银行转账</span>
            <p>收款单位：广东美尚网络科技有限公司</p>
            <p>开户银行：广州市华景新城支行</p>
            <p>银行账户：XXXXXXXXXXXXXXXXXXXXXXXX</p>
            <a href="javascript:;" class="bank_transfer right-arrow ">银行转账支付后将凭证拍照发给平台客服</a>
        </div>
        <form action="<?php echo url('Payment/rechargePayment'); ?>" method="post" class="recharge_form">
            <input type="hidden" name="amount" value=""/>
            <input type="hidden" name="pay_code" value="" class="pay_code"/>
            <span class="red">￥<price>0.00</price></span>
            <input type="submit" value="充值支付" class="pay_btn"/>
        </form>
    </section>
</article>

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

<!--js基本插件-->
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/layer.mobile/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/forbidscroll.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/login.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_index/js/paging.js"></script>
<!--自定义js-->

<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/uploadImgToTemp.js"></script>
<script type="text/javascript">
    $(function(){
        $('.pay_nav li').on('click',function(){
            $(this).addClass('current').siblings().removeClass('current');
            var pay_code = $(this).data('paycode');
            $(this).find('input[type="checkbox"]').prop('checked',true);
            $('.pay_code').val(pay_code);
        });
        $('.recharge_m_list li').on('click',function(){
            $(this).addClass('current').siblings().removeClass('current');
            var rechargeMoney = $(this).find('span').text();
            $('.recharge_form price').text(rechargeMoney);
            $('input[name="amount"]').val(rechargeMoney);
        });
        //充值支付
        $('body').on('click','.pay_btn',function () {
            var pay_code = $('input[name=pay_code]').val();
            var rechargeMoney = $('input[name=amount]').val();
            if(!rechargeMoney){
                dialog.error('充值金额不能为0');
                return false;
            }
            if(!pay_code){
                dialog.error('请选择支付方式');
                return false;
            }
        });
        //转账凭证
        var banksTransferTpl=$('.bank_transfer_tpl').html();
        $('body').on('click','.bank_transfer',function () {
            layer.open({
                title:['转账凭证','border-bottom:1px solid #d9d9d9;'],
                type:1,
                anim:'up',
                className:'bankTransferLayer',
                content:banksTransferTpl
            })
        });
        $('body').on('click','.entry_btn',function () {
            var postData={};
            var voucher=$('.bankTransferLayer').find('.img').val();
            postData.voucher=voucher;
            var content='';
            console.log(voucher);
            if(!postData.voucher){
                content='请上传转账凭证';
            }
            if(content){
                dialog.error(content);
                return false;
            }
            var url =  module + 'Mine/index';
            location.href = url;
        });
    });
</script>

</body></html>
<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenter/login';//登录页
    protected $indexUrl = 'index/Index/index';//平台首页
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if(request()->isAjax()){
                $this->success('您还未登录平台，请先登录！',url($this->indexUrl),'no_login',0);
            }else{
//                $this->error('您还未登录平台，请先登录！',str_replace('/index.php','',url($this->loginUrl)));
                echo '<section class="userInfoWrapper">
	<div class="ucenter_logo">
		<img src="api_common_img/ucenter_logo.png" alt="">
	</div>
    <div class="f24 bomb_box">
        <ul class="loginNav">
            <li class="current">登录</li>
            <li >注册/重置密码</li>
        </ul>
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
			<a href="javascript:void(0);" class="loginBtn entry-button"  data-method="login">登录</a>
		</form>
		<form class="loginTab hide" id="formRegister">
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
			<a href="javascript:void(0);" class="registerBtn entry-button" data-method="register">确定</a>
		</form>
    </div>
</section>';
                exit;
            }
        }
    }
}
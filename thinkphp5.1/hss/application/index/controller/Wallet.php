<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{

    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['recharge']) ){

            $model = new \app\index\model\Wallet();;
            if(!$wallet = $model->getWalletInfo($this->user['id'])){
                $this->redirect('walletOpening');
                exit;
            }

            $this->assign('wallet',$wallet);
        }

    }

    /**
     * 开通钱包页
     */
    public function walletOpening(){
        $user = session('user');
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 钱包充值页面
     */
    public function recharge(){

        if (request()->isAjax()) {
        } else {

        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            $payOpenId =  session('pay_open_id');
            if(empty($payOpenId)){
                $tools = new \common\component\payment\weixin\getPayOpenId(config('wx_config.appid'), config('wx_config.appsecret'));
                $payOpenId  = $tools->getOpenid();
                session('pay_open_id',$payOpenId);
            }
        }
        return $this->fetch();

        }
    }

    /**
     * 充值记录页
     */
    public function rechargeDetail(){
        if (request()->isAjax()) {
        } else {
            return $this->fetch();
        }
    }



    /**
     * 登录 不需要 2019-4-19 张
     */
/*    public function login(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->login($postData);
        } else {
            return $this->fetch();
        }
    }*/
    
    /**
     * 设置||重置支付密码
     */
    public function forgetPassword(){

        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->resetPassword($postData);

        } else {
            return $this->fetch();
        }
    }



}
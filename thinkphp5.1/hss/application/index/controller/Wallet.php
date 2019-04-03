<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
    public function rechargeDetail(){
        if (request()->isAjax()) {
        } else {
            return $this->fetch();
        }
    }
    /**登录
     */
    public function login(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->login($postData);
        } else {
            return $this->fetch();
        }
    }
    
    /**忘记密码 /注册
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

    /**
     * 钱包充值页面
     */
    public function recharge(){
        if (request()->isAjax()) {
        } else {
            $model = new \app\index\model\Wallet();;
            $condition = [
                'where' => [
                    ['user_id', '=', $this->user['id']]
                ], 'field' => [
                    'id', 'amount',
                ]
            ];
            $wallet = $model->getInfo($condition);
            if(empty($wallet)){ // 返回钱包开通页
                return redirect('Mine/index');

            }else{
                $this->assign('wallet',$wallet);

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
    }

    /**
     * 是否已设置钱包
     */
    public function getWalletInfo(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $condition = [
                'where' => [
                    ['user_id', '=', $this->user['id']]
                ], 'field' => [
                    'id', 'amount',
                ]
            ];
            if ($model->getInfo($condition)) {
                return successMsg('成功');
            }
        }
        return errorMsg('失败');
    }

}
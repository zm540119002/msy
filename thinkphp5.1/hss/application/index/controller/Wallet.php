<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{


    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['recharge']) ){

            if(!$this->getWalletInfo()){
                $this->redirect('walletOpening');
                exit;
            }
        }

    }


    /**
     * 首页
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

    /**
     * 开通钱包页
     */
    public function walletOpening(){
        $user = session('user');
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 登录
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
    
    /**
     * 设置||重置密码
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
     * 获取钱包信息
     */
    public function getWalletInfo(){

        $model = new \app\index\model\Wallet();;
        $condition = [
            'where' => [
                ['user_id', '=', $this->user['id']]
            ], 'field' => [
                'id', 'amount',
            ]
        ];

        if (!$model->getInfo($condition)) {
            return false;

        }else{
            return true;
        }

    }

}
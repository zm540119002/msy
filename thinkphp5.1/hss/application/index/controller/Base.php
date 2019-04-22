<?php
namespace app\index\controller;

/**
 * 平台基类
 */
class Base extends \common\controller\UserBase{

    protected $wallet = null;

    public function __construct(){
        parent::__construct();

        // 平台初始化
        if (!$wallet = session(config('app.app_name'))) {
            // 自动开通钱包
            $model = new \app\index\model\Wallet();;
            if(!$wallet = $model->getWalletInfo($this->user['id'])){

                $model->edit(['user_id'=>$this->user['id']]);
                $wallet = $model->getWalletInfo($this->user['id']);
            }
            session(config('app.app_name'), $wallet);
        }
        $this->wallet = $wallet;
    }
}
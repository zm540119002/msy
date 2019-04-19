<?php
namespace app\index\controller;

class Wallet extends \common\controller\Base{

    /**
     * 钱包充值页面
     */
    public function recharge(){

        return $this->fetch();

    }

}
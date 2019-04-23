<?php
namespace app\index\controller;

class Income extends Base {

    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['index']) ){
            if(empty($this->wallet['password'])){
                $this->redirect('walletOpening');
                exit;
            }

            $this->assign('wallet',$this->wallet);
        }

    }

    /**
     * 收益钱包详情页面
     */
    public function index(){

        if (request()->isAjax()) {
        } else {

        return $this->fetch();
        }
    }



}
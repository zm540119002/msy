<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class RechargeController extends AuthUserController {
    //充值-首页
    public function index(){
        $modelWalletDetail = D('WalletDetail');
        $modelWallet = D('Wallet');
        if(IS_POST){
        }else{
            //机构名称
            $this->name = $this->user['name'];
            //账户余额
            $where = array(
                'w.user_id' => $this->user['id'],
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $this->amount = $walletInfo[0]['amount'];

            $this->display();
        }
    }

    //充值-首页
    public function index2(){
        $this->display();
    }

    //充值-输入
    public function amount_input(){
        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
            $this->amount = floatval($_POST['amount']);
            if(isset($_POST['amount']) && $this->amount>0){
                $_POST['user_id'] = $this->user['id'];
                $_POST['create_time'] = time();
                $_POST['amount'] = $this->amount;
                $_POST['sn'] = generateSN();
                $res = $modelWalletDetail->addWalletDetail();
                $this->ajaxReturn($res);
            }
        }else{
            $this->display();
        }
    }
}
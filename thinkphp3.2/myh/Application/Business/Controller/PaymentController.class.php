<?php
namespace Business\Controller;
use web\all\Controller\AuthUserController;
use web\all\Lib\Pay;
use web\all\Cache\PartnerCache;
class PaymentController extends AuthUserController {
    //订单-支付
    public function orderPayment(){
        if(IS_POST){
        }else{
            if(isset($_GET['orderId']) && !empty($_GET['orderId'])){
                $modelOrder = D('Order');
                $orderId = $_GET['orderId'];
                $where = array(
                    'o.user_id' => $this->user['id'],
                    'o.id' => $orderId,
                );
                $orderInfo = $modelOrder -> selectOrder($where);
                $orderInfo = $orderInfo[0];
                $this->orderInfo = $orderInfo;
                //检查订单状态
                $result = $modelOrder->checkOrderLogisticsStatus($orderInfo['logistics_status']);
                if($result['status'] == 0){
                    $this->error($result['message']);
                }
                $payInfo = array(
                    'sn'=>$orderInfo['sn'],
                    'actually_amount'=>$orderInfo['actually_amount'],
                    'cancel_back' => U('payCancel'),
                    'fail_back' => U('payFail'),
                    'success_back' => U('payComplete'),
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'] .
                        ($orderInfo['type']==0?'/weixin.order':'/weixin.group_buy'),
                );
                if($orderInfo['type']==1){//团购订单
                    $where = array(
                        'gbd.user_id' => $this->user['id'],
                        'gbd.order_id' => $orderId,
                    );
                    $groupBuy = D('GroupBuyDetail')->selectGroupBuyDetail($where);
                    session('returnUrl') && $payInfo['success_back'] = substr(session('returnUrl'),0,strrpos(session('returnUrl'),'.html')).
                        '/groupBuyId/'.$groupBuy[0]['group_buy_id'].'/shareType/groupBuy';
                }
                Pay::wxPay($payInfo);
            }
        }
    }

    //席位订金-支付
    public function depositPayment(){
        if(IS_POST){
        }else{
            $partner = PartnerCache::get($this->user['id']);
            if(!$partner){
                $this->error('合伙人未登记！');
            }
            $modelCity = D('City');
            $where = array(
                'ct.id' => $partner['city'],
            );
            $city = $modelCity->selectCity($where);
            $city = $city[0];
            if(!$city['id']){
                $this->error('合伙人城市信息有误！',session('returnUrl'));
            }
            if(!floatval($city['deposit'])){
                $this->error('合伙人订金有误！',session('returnUrl'));
            }
            $res = $this->checkWallet();
            if(!($res===true)){
                $this->error($res,session('returnUrl'));
            }
            $SN = generateSN();
            if(!$this->saveWalletDetail($city['deposit'],$SN)){
                $this->error('钱包充值出错！',session('returnUrl'));
            }
            $payInfo = array(
                'sn'=>$SN,
                'actually_amount'=>$city['deposit'],
                'cancel_back' => U('payCancel'),
                'fail_back' => U('payFail'),
                'success_back' => session('returnUrl')?:U('payComplete'),
                'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'] .'/weixin.deposit',
            );
            print_r($payInfo);exit;
            Pay::wxPay($payInfo);
        }
    }

    //充值-支付
    public function rechargePayment(){
        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
        }else{
            if(isset($_GET['walletDetailId']) && intval($_GET['walletDetailId'])){
                $where = array(
                    'wd.id' => I('get.walletDetailId'),
                    'wd.user_id' => $this->user['id'],
                );
                $walletDetailInfo = $modelWalletDetail->selectWalletDetail($where);
                $walletDetailInfo = $walletDetailInfo[0];
                $this->amount = $walletDetailInfo['amount'];
                $payInfo = array(
                    'sn'=>$walletDetailInfo['sn'],
                    'actually_amount'=>$this->amount,
                    'cancel_back' => U('payCancel'),
                    'fail_back' => U('payFail'),
                    'success_back' => U('payComplete'),
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'].'/weixin.recharge',
                );
                Pay::wxPay($payInfo);
            }
        }
    }

    /**钱包明细
     * @param $amount
     * @param int $type 1：充值 2：支付
     * @return bool
     */
    private function saveWalletDetail($amount,$SN,$type=1){
        $modelWalletDetail = D('WalletDetail');
        if(floatval($amount)){
            $_POST['user_id'] = $this->user['id'];
            $_POST['type'] = $type;
            $_POST['recharge_status'] = 0;
            $_POST['create_time'] = time();
            $_POST['amount'] = $amount;
            $_POST['sn'] = $SN;
            $res = $modelWalletDetail->addWalletDetail();
            if($res['status']==1){
                return true;
            }
        }
        return false;
    }

    /**检查钱包（1，钱包记录不存在，则新增记录；2，如果为支付，会检查余额是否足够）
     * @param int $amount 金额
     * @param int $type 1：充值 2：支付
     * @return bool === true 表示通过
     */
    private function checkWallet($amount,$type=1){
        $msg = '';
        $modelWallet = D('Wallet');
        $where = array(
            'w.user_id' => $this->user['id'],
        );
        $walletInfo = $modelWallet->selectWallet($where);
        $walletInfo = $walletInfo[0];
        if($walletInfo){//存在
            if($type==2 && $walletInfo['amount']<$amount){
                $msg = '余额不足';
            }
        }else{//不存在
            if($type==2){
                $msg = '余额不足';
            }
            $_POST = [];
            $_POST['user_id'] = $this->user['id'];
            $res = $modelWallet->addWallet();
            if($res['status']==0){
                $msg = $modelWallet->getLastSql();
            }
        }
        return $msg?:true;
    }
}
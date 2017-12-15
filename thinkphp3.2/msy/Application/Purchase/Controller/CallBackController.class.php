<?php
namespace Purchase\Controller;

use Think\Controller;

class CallBackController extends Controller {
    //微信支付回调-充值
    public function rechargeCallBack(){
        $data = array(
            'code'=> 111,
            'config'=>222,
            'name'=>'回调2'
        );
        D('Plugin')->add($data);
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        //保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = makeSign($data);
        $dataWalletSn = $data['out_trade_no'];
        $totalFee = $data['total_fee'];

        //判断签名是否正确  判断支付状态
        if($sign !== $data_sign || ($data['return_code'] != 'SUCCESS') || ($data['result_code'] != 'SUCCESS')) {
            //返回状态给微信服务器
            $this->errorReturn($dataWalletSn,'签名错误','充值');
        }
        $modelWalletDetail = D('WalletDetail');
        $where = array(
            'wd.sn'=> $dataWalletSn,
        );
        $walletDetailInfo = $modelWalletDetail -> selectWalletDetail($where);
        $walletDetailInfo = $walletDetailInfo[0];
        if($walletDetailInfo['recharge_status'] != 1){//判定账户明细-充值状态，不成功
            if( $walletDetailInfo['amount']*100 != $totalFee ){//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                $this->errorReturn($dataWalletSn,'回调的金额和充值的金额不符，终止交易','充值');
            }
            $modelWalletDetail -> startTrans();
            //更新-账户明细-充值状态
            $_POST = [];
            $_POST['recharge_status'] = 1;
            $where = array(
                'user_id' =>  $walletDetailInfo['user_id'],
                'sn' => $dataWalletSn,
            );
            $res = $modelWalletDetail->saveOrder($where);
            if($res['status']==0){
                $modelWalletDetail->rollback();
                //返回状态给微信服务器
                $this->errorReturn($dataWalletSn,$modelWalletDetail->getLastSql(),'充值');
            }
            //更新-账户-金额
            $modelWallet = D('Wallet');
            $where = array(
                'w.user_id' =>  $walletDetailInfo['user_id'],
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $walletInfo = $walletInfo[0];
            if($walletInfo['id']){
                $_POST = [];
                $_POST['amount'] = $walletInfo['amount'] + $walletDetailInfo['amount'];
                $res = $modelWallet->saveWallet($where);
                if($res['status'] ==0){
                    //返回状态给微信服务器
                    $this->errorReturn($dataWalletSn,$modelWallet->getLastSql(),'充值');
                }
            }
            $modelWalletDetail->commit();//提交事务

            $data = array(
                'name'=>'回调4'
            );
            D('Plugin')->add($data);
            //返回状态给微信服务器
            $this->successReturn($dataWalletSn);
        }
    }

    //微信支付回调-订单
    public function orderCallBack(){
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        //保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = makeSign($data);
        $dataOrderSn = $data['out_trade_no'];
        $totalFee = $data['total_fee'];

        // 判断签名是否正确  判断支付状态
        if ($sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            $modelOrder = D('Order');
            $modelOrderDetail = D('OrderDetail');
            $modelGoods = D('Goods');
            $modelCoupons = D('CouponsReceive');
            $modelWallet = D('Wallet');
            $modelWalletDetail = D('WalletDetail');
            $where = array(
                'sn'=> $dataOrderSn,
            );
            $orderInfo = $modelOrder -> selectOrder($where);
            $orderInfo = $orderInfo[0];
            $user_id = $orderInfo['user_id'];
            if($orderInfo['pay_status'] == 10){//判定订单状态，如已处理过，直接返回true
                if( $orderInfo['actually_amount']*100 != $totalFee ){//校验返回的订单金额是否与商户侧的订单金额一致
                    //返回状态给微信服务器
                    $this->errorReturn($dataOrderSn,'回调的金额和订单的金额不符，终止购买');
                }else{
                    $modelOrder -> startTrans();
                    //更新订单状态
                    $_POST = [];
                    $_POST['pay_status'] = 20;
                    $_POST['payment_code'] = 0;
                    $_POST['pay_sn'] = $data['transaction_id'];
                    $_POST['payment_time'] = $data['time_end'];
                    $_POST['orderId'] = $orderInfo['id'];
                    $where = array(
                        'user_id' =>  $user_id,
                        'sn' => $dataOrderSn,
                    );
                    $returnData = $modelOrder->saveOrder($where);
                    if(!$returnData['id']){
                        $modelOrder->rollback();
                        //返回状态给微信服务器
                        $this->errorReturn($dataOrderSn,$modelOrderDetail->getLastSql());
                    }
                    //更新代金券，已使用
                    if($orderInfo['coupons_id'] && $orderInfo['coupons_pay'] > 0){
                        $_POST = [];
                        $_POST['status'] = 1;
                        $_POST['couponsId'] = $orderInfo['coupons_id'];
                        $where = array(
                            'user_id' =>  $user_id,
                        );
                        $res = $modelCoupons->saveCouponsReceive($where);
                        if($res['status']==0){
                            $modelOrder->rollback();
                            //返回状态给微信服务器
                            $this->errorReturn($dataOrderSn,$modelOrderDetail->getLastSql());
                        }
                    }
                    //更新账户
                    if($orderInfo['wallet_pay'] > 0){
                        //钱包信息
                        $where = array(
                            'w.user_id' =>  $user_id,
                        );
                        $walletInfo = $modelWallet->selectWallet($where);
                        $walletInfo = $walletInfo[0];
                        if($walletInfo['amount'] >= $orderInfo['wallet_pay']){
                            //更新账户
                            $_POST = [];
                            $_POST['amount'] = $walletInfo['amount'] - $orderInfo['wallet_pay'];
                            $where = array(
                                'user_id' =>  $user_id,
                            );
                            $res = $modelWallet -> saveWallet($where);
                        }
                        if($res['status'] == 0){
                            $modelWallet->rollback();
                            //返回状态给微信服务器
                            $this->errorReturn($dataOrderSn,$modelOrderDetail->getLastSql());
                        }
                        //增加账户记录
                        $_POST = [];
                        $_POST['user_id'] = $user_id;
                        $_POST['amount'] = $orderInfo['wallet_pay'];
                        $_POST['type'] = 2;
                        $_POST['create_time'] = time();
                        $res = $modelWalletDetail -> addWalletDetail();
                        if($res['status'] == 0){
                            $modelWallet->rollback();
                            //返回状态给微信服务器
                            $this->errorReturn($dataOrderSn,$modelOrderDetail->getLastSql());
                        }
                    }
                    //减库存
                    $where = array(
                        'order_sn' => $orderInfo['sn'],
                    );
                    $orderDetail = $modelOrderDetail -> selectOrderDetail($where);
                    $res = $modelGoods -> decGoodsNum($orderDetail);
                    if($res['status'] ==0){
                        $modelWallet->rollback();
                        //返回状态给微信服务器
                        $this->errorReturn($dataOrderSn,$modelOrderDetail->getLastSql());
                    }
                    $modelOrder->commit();//提交事务
                    //返回状态给微信服务器
                    $this->successReturn($dataOrderSn);
                }
            }
        }else{
            //返回状态给微信服务器
            $this->errorReturn($dataOrderSn);
        }
    }

    //成功返回
    private function successReturn(){
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return true;
    }

    //失败返回
    private function errorReturn($dataSn='',$error='签名错误',$type='订单'){
        \Think\Log::write($type . '支付失败：' . $dataSn . "\r\n失败原因：" . $error, 'NOTIC');
        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        return false;
    }


    public function notifyUrl(){
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        if(strpos($_SERVER['QUERY_STRING'],'weixin.recharge') == true)
        {
            $this->rechargeCallBack($data);
        }
        if(strpos($_SERVER['QUERY_STRING'],'weixin.order') == true){
            $this->orderCallBack();
        }
    }
}
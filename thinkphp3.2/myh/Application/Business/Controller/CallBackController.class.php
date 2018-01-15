<?php
namespace Business\Controller;
use Think\Controller;
use web\all\Component\payment\unionpay\sdk\AcpService;
use web\all\Component\payment\alipayMobile\lib\AlipayNotify;
class CallBackController extends Controller{
    //支付回调
    public function notifyUrl(){
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.recharge') == true) {
            $xml = file_get_contents('php://input');
            $data = xmlToArray($xml);
            $this->callBack($data, $payment_type = 'weixin', $order_type = 'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.order') == true) {
            $xml = file_get_contents('php://input');
            $data = xmlToArray($xml);
            $this->callBack($data, $payment_type = 'weixin', $order_type = 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.deposit') == true) {
            $xml = file_get_contents('php://input');
            $data = xmlToArray($xml);
            $this->callBack($data, $payment_type = 'weixin', $order_type = 'deposit');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'alipayMobile.recharge') == true) {
            $data = $_POST;
            $this->callBack($data, $payment_type = 'alipayMobile', $order_type = 'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'alipayMobile.order') == true) {
            $data = $_POST;
            $this->callBack($data, $payment_type = 'alipayMobile', $order_type = 'order');
        }
    }

    //支付完成，调用不同的支付的回调处理
    private function callBack($data, $payment_type, $order_type){
        if ($payment_type == 'weixin') {
            $this->weixinBack($data, $order_type);
        }
        if ($payment_type == 'alipayMobile') {
            $this->alipayMobileBack($data, $order_type);
        }
        if ($payment_type = 'unionpay') {
            $this->unionpayBack($data, $order_type);
        }
    }

    //微信支付回调处理
    private function weixinBack($data, $order_type){
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = makeSign($data);
        // 判断签名是否正确  判断支付状态
        if ($sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            $parameter = array(
                'payment_code' => 'weixin',
                'order_sn' => $data['out_trade_no'],//微信回的商家订单号
                'total_fee' => $data['total_fee'],//支付金额
                'pay_sn' => $data['transaction_id'],//微信交易订单
                'payment_time' => $data['time_end']//支付时间
            );
            if ($order_type == 'recharge') {
                $this->rechargeHandle($parameter);
            }
            if ($order_type == 'order') {
                $this->orderHandle($parameter);
            }
            if ($order_type == 'deposit') {
                $this->depositHandle($data);
            }
        } else {
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no']);
        }
    }

    //银联支付回调处理
    private function unionpayBack($data, $order_type){
        //计算得出通知验证结果
        $unionpayNotify = new AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($_POST);
        if ($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $data['orderId']; //商户订单号
            $queryId = $data['queryId']; //银联支付流水号
            $respMsg = $data['respMsg']; //交易状态
            // 解释: 交易成功且结束，即不可再做任何操作。
            if ($data['respMsg'] == 'Success!') {
                // 修改订单支付状态
                if ($order_type == 'recharge') {
                    $this->rechargeHandle($data);
                }
                if ($order_type == 'order') {
                    $this->orderHandle($data);
                }
            }
            echo "success"; // 处理成功
        } else {
            echo "fail"; //验证失败
        }
    }

    //支付宝支付回调处理
    private function alipayMobileBack($data, $order_type){
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config); // 使用支付宝原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $alipayNotify->verifyNotify();
        if (!$verify_result) {
            echo "fail";
            exit;
        }
        $order_sn = $out_trade_no = $_POST['out_trade_no']; //商户订单号
        $trade_no = $_POST['trade_no']; //支付宝交易号
        $trade_status = $_POST['trade_status']; //交易状态
        // 支付宝解释: 交易成功且结束，即不可再做任何操作。
        if ($trade_status == 'TRADE_FINISHED') {
            //支付成功，做自己的逻辑

        } //支付宝解释: 交易成功，且可对该交易做操作，如：多级分润、退款等。
        elseif ($trade_status == 'TRADE_SUCCESS') {
            //支付成功，做自己的逻辑
        }
        echo "success"; // 告诉支付宝处理成功
    }

    /**充值支付回调
     * @param $parameter
     */
    private function rechargeHandle($parameter){
        $modelWalletDetail = D('WalletDetail');
        $where = array(
            'wd.sn' => $parameter['order_sn'],
        );
        $walletDetailInfo = $modelWalletDetail->selectWalletDetail($where);
        $walletDetailInfo = $walletDetailInfo[0];
        if ($walletDetailInfo['recharge_status'] != 1) {//判定账户明细-充值状态，不成功
            if ($walletDetailInfo['amount'] * 100 != $parameter['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                $this->errorReturn($parameter['order_sn'], '回调的金额和充值的金额不符，终止交易', '充值');
            }
            $modelWalletDetail->startTrans();
            //更新-账户明细-充值状态
            $_POST = [];
            $_POST['recharge_status'] = 1;
            $_POST['pay_sn'] = $parameter['pay_sn'];
            $_POST['payment_code'] = 'weixn';
            $_POST['payment_time'] = $parameter['payment_time'];
            $where = array(
                'user_id' => $walletDetailInfo['user_id'],
                'sn' => $parameter['order_sn'],
            );
            $res = $modelWalletDetail->saveWalletDetail($where);
            if ($res['status'] == 0) {
                $modelWalletDetail->rollback();
                //返回状态给微信服务器
                $this->errorReturn($parameter['order_sn'], $modelWalletDetail->getLastSql(), '充值');
            }
            //更新-账户-金额
            $modelWallet = D('Wallet');
            $where = array(
                'user_id' => $walletDetailInfo['user_id'],
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $walletInfo = $walletInfo[0];
            if ($walletInfo['id']) {
                $_POST = [];
                $_POST['amount'] = $walletInfo['amount'] + $walletDetailInfo['amount'];
                $res = $modelWallet->saveWallet($where);
                if ($res['status'] == 0) {
                    //返回状态给微信服务器
                    $this->errorReturn($parameter['order_sn'], $modelWallet->getLastSql(), '充值');
                }
            }
            $modelWalletDetail->commit();//提交事务
            //返回状态给微信服务器
            $this->successReturn();
        }
    }

    /**
     * @param $data
     * 普通订单支付回调
     */
    private function orderHandle($data){
        $orderSn = $data['out_trade_no'];
        $totalFee = $data['total_fee'];
        $modelOrder = D('Order');
        $modelOrderDetail = D('OrderDetail');
        $modelCoupons = D('CouponsReceive');
        $modelWallet = D('Wallet');
        $modelWalletDetail = D('WalletDetail');
        $where = array(
            'sn' => $orderSn,
        );
        $orderInfo = $modelOrder->selectOrder($where);
        $orderInfo = $orderInfo[0];
        if ($orderInfo['logistics_status'] != 1) {//判定订单状态，如已处理过，直接返回true
            if ($orderInfo['actually_amount'] * 100 != $totalFee) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                $this->errorReturn($orderSn, '回调的金额和订单的金额不符，终止购买');
            } else {
                $modelOrder->startTrans();
                //更新订单状态
                $_POST = [];
                $_POST['logistics_status'] = 2;
                $_POST['payment_code'] = 0;
                $_POST['pay_sn'] = $data['transaction_id'];
                $_POST['payment_time'] = $data['time_end'];
                $_POST['orderId'] = $orderInfo['id'];
                $where = array(
                    'user_id' => $orderInfo['user_id'],
                    'sn' => $orderSn,
                );
                $returnData = $modelOrder->saveOrder($where);
                if (!$returnData['id']) {
                    $modelOrder->rollback();
                    //返回状态给微信服务器
                    $this->errorReturn($orderSn, $modelOrderDetail->getLastSql());
                }
                //更新代金券，已使用
                if ($orderInfo['coupons_id'] && $orderInfo['coupons_pay'] > 0) {
                    $_POST = [];
                    $_POST['status'] = 1;
                    $_POST['couponsId'] = $orderInfo['coupons_id'];
                    $where = array(
                        'user_id' => $orderInfo['user_id'],
                    );
                    $res = $modelCoupons->saveCouponsReceive($where);
                    if ($res['status'] == 0) {
                        $modelOrder->rollback();
                        //返回状态给微信服务器
                        $this->errorReturn($orderSn, $modelOrderDetail->getLastSql());
                    }
                }
                //更新账户
                if ($orderInfo['wallet_pay'] > 0) {
                    //钱包信息
                    $where = array(
                        'w.user_id' => $orderInfo['user_id'],
                    );
                    $walletInfo = $modelWallet->selectWallet($where);
                    $walletInfo = $walletInfo[0];
                    if ($walletInfo['amount'] >= $orderInfo['wallet_pay']) {
                        //更新账户
                        $_POST = [];
                        $_POST['amount'] = $walletInfo['amount'] - $orderInfo['wallet_pay'];
                        $where = array(
                            'user_id' => $orderInfo['user_id'],
                        );
                        $res = $modelWallet->saveWallet($where);
                    }
                    if ($res['status'] == 0) {
                        $modelWallet->rollback();
                        //返回状态给微信服务器
                        $this->errorReturn($orderSn, $modelOrderDetail->getLastSql());
                    }
                    //增加账户记录
                    $_POST = [];
                    $_POST['user_id'] = $orderInfo['user_id'];
                    $_POST['amount'] = $orderInfo['wallet_pay'];
                    $_POST['type'] = 2;
                    $_POST['create_time'] = time();
                    $res = $modelWalletDetail->addWalletDetail();
                    if ($res['status'] == 0) {
                        $modelWallet->rollback();
                        //返回状态给微信服务器
                        $this->errorReturn($orderSn, $modelOrderDetail->getLastSql());
                    }
                }
                $modelOrder->commit();//提交事务
                //返回状态给微信服务器
                $this->successReturn();
            }
        }
    }

    public function test(){
        $parameter = array(
            'out_trade_no' =>'20180115133056462855292312274664',//微信回的商家订单号
            'total_fee' => 1,//支付金额
            'transaction_id' => '4200000056201801154355151127',//微信交易订单
            'time_end' => '20180109172790',//支付时间
            'attach' => '4',//支付时间
        );
        $this->depositHandle($parameter);
    }

    /**席位订金充值回调
     */
    private function depositHandle($data){
        /**$data['out_trade_no'],//微信回的商家订单号
         * $data['attach'],//user.id
         * $data['total_fee'],//支付金额，单位为分
         * $data['transaction_id'],//微信交易订单
         * $data['time_end']//支付时间
         */
        $tradeAmount = $data['total_fee']/100;
        if (!$tradeAmount) {
            //返回状态给微信服务器
            $this->errorReturn($data['transaction_id'],'交易金额错误！');
        }
        $modelWallet = D('Wallet');
        $modelWalletDetail = D('WalletDetail');
        $modelPartner = D('Partner');
        $where = array(
            'p.user_id' => $data['attach'],
        );
        $partnerInfo = $modelPartner->selectPartner($where);
        $partnerInfo = $partnerInfo[0];
        if($partnerInfo && $partnerInfo['auth_status'] ==2){
            //返回状态给微信服务器
            $this->successReturn();
            exit;
        }
        $modelPartner->startTrans();//开启事务
        //更新合伙人认证状态为席位订金
        $_POST = [];
        $_POST['auth_status'] = 2;
        $where = array(
            'user_id' => $data['attach'],
        );
        $returnData = $modelPartner->savePartner($where);
        if ($returnData['status'] == 0) {
            $modelPartner->rollback();
            //返回状态给微信服务器
            $this->errorReturn($data['transaction_id'], '更新合伙人认证状态错误！');
        }
        //更新钱包
        $where = array(
            'w.user_id' => $data['attach'],
        );
        $walletInfo = $modelWallet->selectWallet($where);
        $walletInfo = $walletInfo[0];
        $_POST = [];
        $_POST['amount'] = $walletInfo['amount'] + $tradeAmount;
        $where = array(
            'user_id' => $data['attach'],
        );
        $returnData = $modelWallet->saveWallet($where);
        if ($returnData['status'] == 0) {
            $modelPartner->rollback();
            //返回状态给微信服务器
            $this->errorReturn($data['transaction_id'], '更新钱包错误！');
        }
        //新增钱包明细
        $_POST = [];
        $_POST['sn'] = $data['out_trade_no'];
        $_POST['pay_sn'] = $data['transaction_id'];
        $_POST['user_id'] = $data['attach'];
        $_POST['type'] = 1;
        $_POST['recharge_status'] = 1;
        $_POST['amount'] = $tradeAmount;
        $_POST['create_time'] = time();
        $returnData = $modelWalletDetail->addWalletDetail();
        if ($returnData['status'] == 0) {
            $modelPartner->rollback();
            //返回状态给微信服务器
            $this->errorReturn($data['transaction_id'], '新增钱包明细错误！');
        }
        $modelWalletDetail->commit();//提交事务
        //返回状态给微信服务器
        $this->successReturn();
    }

    //成功返回
    private function successReturn(){
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return true;
    }

    //失败返回
    private function errorReturn($dataSn = '', $error = '签名错误', $type = '订单'){
        \Think\Log::write($type . '支付失败：' . $dataSn . "\r\n失败原因：" . $error, 'NOTIC');
        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        return false;
    }
}
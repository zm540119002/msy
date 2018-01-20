<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Component\payment\unionpay\sdk\AcpService;
use web\all\Component\payment\alipayMobile\lib\AlipayNotify;
use  web\all\Controller\CommonController;
use web\all\Component\WxpayAPI\Jssdk;
class CallBackController extends CommonController{
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
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.group_buy') == true) {

            $xml = file_get_contents('php://input');
            $data = xmlToArray($xml);
            $this->callBack($data, $payment_type = 'weixin', $order_type = 'group_buy');
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
            if ($order_type == 'recharge') {
                $this->rechargeHandle($data);
            }
            if ($order_type == 'order') {
                $this->orderHandle($data);
            }
            if ($order_type == 'group_buy') {
                $this->groupBuyHandle($data);
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
    private function rechargeHandle($data){

        $modelWalletDetail = D('WalletDetail');
        $where = array(
            'wd.sn' => $data['out_trade_no'],
        );
        $walletDetailInfo = $modelWalletDetail->selectWalletDetail($where);
        $walletDetailInfo = $walletDetailInfo[0];
        if ($walletDetailInfo['recharge_status'] > 1) {
            $this->successReturn();
            exit;
        }
        if ($walletDetailInfo['amount'] * 100 != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no'], '回调的金额和充值的金额不符，终止交易', '充值');
            exit;
        }
        $modelWalletDetail->startTrans();
        //更新-账户明细-充值状态
        $_POST = [];
        $_POST['recharge_status'] = 1;
        $_POST['pay_sn'] = $data['transaction_id'];
        $_POST['payment_code'] = 0;
        $_POST['payment_time'] = $data['time_end'];
        $where = array(
            'user_id' => $walletDetailInfo['user_id'],
            'sn' => $data['out_trade_no'],
        );
        $res = $modelWalletDetail->saveWalletDetail($where);
        if ($res['status'] == 0) {
            $modelWalletDetail->rollback();
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no'], $modelWalletDetail->getLastSql(), '充值');
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
                $this->errorReturn($data['out_trade_no'], $modelWallet->getLastSql(), '充值');
            }
        }
        $modelWalletDetail->commit();//提交事务
        //返回状态给微信服务器
        $this->successReturn();

    }

    public function a(){
        $data = array(
            'out_trade_no' => '20180118160105872689491098932765',
            'total_fee' => 1,
            'transaction_id' => 125587,
            'time_end' => 125487,
        );
        $this -> groupBuyHandle($data);
    }

    /**团购订单支付回调
     * @param $parameter
     */
    private function groupBuyHandle($data){
        $orderSn = $data['out_trade_no'];
        $totalFee = $data['total_fee'];
        $modelOrder = D('Order');
        $modelCoupons = D('CouponsReceive');
        $modelWallet = D('Wallet');
        $modelWalletDetail = D('WalletDetail');
        $modelGroupBuy = D('GroupBuy');
        $modelGroupBuyDetail = D('GroupBuyDetail');
        $where = array(
            'sn' => $orderSn,
        );
        $orderInfo = $modelOrder->selectOrder($where);
        $orderInfo = $orderInfo[0];
        $userId = $orderInfo['user_id'];
        if ($orderInfo['logistics_status'] > 1) {
            $this->successReturn();
            exit;
        }
        if ($orderInfo['actually_amount'] * 100 != $totalFee) {//校验返回的订单金额是否与商户侧的订单金额一致
            //返回状态给微信服务器
            $this->errorReturn($orderSn, '回调的金额和订单的金额不符，终止购买');
            exit;
        }
        $modelOrder->startTrans();
        //更新订单状态
        $_POST = [];
        $_POST['logistics_status'] = 2;
        $_POST['payment_code'] = 0;
        $_POST['pay_sn'] = $data['transaction_id'];
        $_POST['payment_time'] = $data['time_end'];
        $_POST['orderId'] = $orderInfo['id'];
        $where = array(
            'user_id' =>$userId,
            'sn' => $orderSn,
        );
        $returnData = $modelOrder->saveOrder($where);
        if ($returnData['status'] == 0) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            $this->errorReturn($orderSn, $modelOrder->getLastSql());
        }
        //更新团购表和团购详情表
        //1.先更新团购详情表
        $_POST = [];
        $_POST['pay_status'] = 2;
        $_POST['pay_time'] = $data['time_end'];
        unset($where);
        $where = array(
            'user_id' =>$userId,
            'order_id' => $orderInfo['id'],
        );
        $returnData = $modelGroupBuyDetail-> saveGroupBuyDetail($where);
        if ($returnData['status'] == 0) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            $this->errorReturn($orderSn, $modelGroupBuyDetail->getLastSql());
        }
        $groupBuyDetail = $modelGroupBuyDetail->selectGroupBuyDetail($where);
        $groupBuyDetail = $groupBuyDetail[0];
        $ownOpenid = $groupBuyDetail['openid'];//自己的openid
        $groupBuyId = $groupBuyDetail['group_buy_id']; //团购ID
        $goodsId = $groupBuyDetail['goods_id'];//产品ID
        //2.查看团购详情表此次团购有几人
        unset($where);
        $where = array(
            'group_buy_id' => $groupBuyId,
            'pay_status' => 2,
        );
        $groupBuyNum = $modelGroupBuyDetail->where($where)->count();
       \Think\Log::write( '团购人数：' . $groupBuyNum . "\r\n失败原因：" , 'NOTIC');
        $field=[ 'g.cash_back','g.goods_base_id','g.commission',
            'gb.name','wxu.headimgurl','wxu.nickname','o.sn as order_sn'
        ];
        $join=[ ' left join goods g on g.id = gbd.goods_id',
            ' left join goods_base gb on g.goods_base_id = gb.id ',
            ' left join wx_user wxu on wxu.openid = gbd.openid',
            ' left join orders o on o.id = gbd.order_id',
        ];
        $templateMessageList = $modelGroupBuyDetail->selectGroupBuyDetail($where,$field,$join);
        $cashBack = $templateMessageList[0]['cash_back'];//团购完成后返现
        $goodsName = $templateMessageList[0]['name'];//产品名称
        foreach ($templateMessageList as &$item){
            if($item['type'] == 1){
                $header = $item['nickname'];//团长呢称
                break;
            }
        }
        //团购成功通知
        $template = array(
            'touser'=>$ownOpenid,
            'template_id'=>'u7WmSYx2RJkZb-5_wOqhOCYl5xUKOwM99iEz3ljliyY',//参加团购通知模板Id
            "url"=>$this->host.U('Goods/goodsDetail',array(
                    'goodsId'=>$goodsId,
                    'groupBuyId'=> $groupBuyId,
                    'shareType'=>'groupBuy' )),
            'data'=>array(
                'first'=>array(
                    'value'=>'亲，您已成功参加团购！','color'=>'#173177',
                ),
                'Pingou_ProductName'=>array(
                    'value'=>$goodsName,'color'=>'#173177',
                ),
                'Weixin_ID'=>array(
                    'value'=>$header,'color'=>'#173177',
                ),
                'Remark'=>array(
                    'value'=>'三人可以成团，团长发起团三天有效，团购人数不限哦，快点击详情，邀请好友参团','color'=>'#FF0000',
                ),
            ),
        );
        $jssdk = new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $rst = $jssdk->send_template_message($template);

        if($rst['errmsg'] != 'ok'){
            \Think\Log::write('发送团购通知失败', 'NOTIC');
        }
        //修改团购表 已成团
        if($groupBuyNum == 3){
            $_POST = [];
            $_POST['tag'] = 1;
            unset($where);
            $where = array(
                'id' => $groupBuyId,
            );
            $returnData = $modelGroupBuy-> saveGroupBuy($where);
            if ($returnData['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelGroupBuy->getLastSql());
            }
            //返现 //返现退三个
            //更新账户
            unset($where);
            $where['user_id'] = array('in',array_column($templateMessageList,"user_id"));
            $where['status'] = 0;
            $res = $modelWallet->where($where)->setInc('amount',$cashBack);
            if(!$res){
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWallet->getLastSql());
            }
            //增加账户记录
            foreach (array_column($templateMessageList,"user_id") as &$useId){
                $_POST = [];
                $_POST['user_id'] = $useId;
                $_POST['amount'] = $cashBack;
                $_POST['type'] = 3;
                $_POST['create_time'] = time();
                $res = $modelWalletDetail->addWalletDetail();
                if ($res['status'] == 0) {
                    $modelWallet->rollback();
                    //返回状态给微信服务器
                    $this->errorReturn($orderSn, $modelWalletDetail->getLastSql());
                }
            }
            //返现通知三人
            foreach (array_column($templateMessageList,"openid","order_sn") as $order_sn => &$openid){
                //返现通知
                $template = array(
                    'touser'=>$openid,
                    'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',//参加团购通知模板Id
                    "url"=>$this->host.U('Goods/goodsDetail',array(
                            'goodsId'=>$goodsId,
                            'groupBuyId'=> $groupBuyId,
                            'shareType'=>'groupBuy' )),
                    'data'=>array(
                        'first'=>array(
                            'value'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户。','color'=>'#173177',
                        ),
                        'keyword1'=>array(
                            'value'=>$order_sn,'color'=>'#173177',
                        ),
                        'keyword2'=>array(
                            'value'=> round( $totalFee / 100, 2).'元','color'=>'#173177',
                        ),
                        'keyword3'=>array(
                            'value'=>$cashBack.'元','color'=>'#173177',
                        ),
                        'Remark'=>array(
                            'value'=>'祝您购物愉快！','color'=>'#FF0000',
                        ),
                    ),
                );
                $rst = $jssdk->send_template_message($template);
                if($rst['errmsg'] != 'ok'){
                    \Think\Log::write('发送团购通知失败', 'NOTIC');
                }
            }
        }
        //只返现自己
        if($groupBuyNum > 3){
            //更新账户
            unset($where);
            $where['user_id'] =$userId;
            $where['status'] = 0;
            $res = $modelWallet->where($where)->setInc('amount',$cashBack);
            if(!$res){
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWallet->getLastSql());
            }
            //增加账户记录
            $_POST = [];
            $_POST['user_id'] =$userId;
            $_POST['amount'] = $cashBack;
            $_POST['type'] = 3;
            $_POST['create_time'] = time();
            $res = $modelWalletDetail->addWalletDetail();
            if ($res['status'] == 0) {
                $modelWallet->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWalletDetail->getLastSql());
            }
            //返现通知
            $template = array(
                'touser'=>$ownOpenid,
                'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',//参加团购通知模板Id
                "url"=>$this->host.U('Goods/goodsDetail',array(
                        'goodsId'=>$goodsId,
                        'groupBuyId'=> $groupBuyId,
                        'shareType'=>'groupBuy' )),
                'data'=>array(
                    'first'=>array(
                        'value'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户。','color'=>'#173177',
                    ),
                    'keyword1'=>array(
                        'value'=>$orderSn,'color'=>'#173177',
                    ),
                    'keyword2'=>array(
                        'value'=> round( $totalFee / 100, 2).'元','color'=>'#173177',
                    ),
                    'keyword3'=>array(
                        'value'=>$cashBack.'元','color'=>'#173177',
                    ),
                    'Remark'=>array(
                        'value'=>'祝您购物愉快！','color'=>'#FF0000',
                    ),
                ),
            );
            $rst = $jssdk->send_template_message($template);
            if($rst['errmsg'] != 'ok'){
                \Think\Log::write('发送团购通知失败', 'NOTIC');
            }
        }

        //更新代金券，已使用
        if ($orderInfo['coupons_id'] && $orderInfo['coupons_pay'] > 0) {
            $_POST = [];
            $_POST['status'] = 1;
            $_POST['couponsId'] = $orderInfo['coupons_id'];
            $where = array(
                'user_id' =>$userId,
            );
            $res = $modelCoupons->saveCouponsReceive($where);
            if ($res['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelCoupons->getLastSql());
            }
        }
        //更新账户
        if ($orderInfo['wallet_pay'] > 0) {
            //钱包信息
            $where = array(
                'w.user_id' =>$userId,
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $walletInfo = $walletInfo[0];
            if ($walletInfo['amount'] >= $orderInfo['wallet_pay']) {
                //更新账户
                $_POST = [];
                $_POST['amount'] = $walletInfo['amount'] - $orderInfo['wallet_pay'];
                $where = array(
                    'user_id' =>$userId,
                );
                $res = $modelWallet->saveWallet($where);
                if ($res['status'] == 0) {
                    $modelWallet->rollback();
                    //返回状态给微信服务器
                    $this->errorReturn($orderSn, $modelWallet->getLastSql());
                }
            }

            //增加账户记录
            $_POST = [];
            $_POST['user_id'] =$userId;
            $_POST['amount'] = $orderInfo['wallet_pay'];
            $_POST['type'] = 2;
            $_POST['create_time'] = time();
            $res = $modelWalletDetail->addWalletDetail();
            if ($res['status'] == 0) {
                $modelWallet->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWalletDetail->getLastSql());
            }
        }

        $modelOrder->commit();//提交事务
        //返回状态给微信服务器
        $this->successReturn();
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
        $userId = $orderInfo['user_id'];
        if ($orderInfo['logistics_status'] > 1) {
            $this->successReturn();
            exit;
        }
        if ($orderInfo['actually_amount'] * 100 != $totalFee) {//校验返回的订单金额是否与商户侧的订单金额一致
            //返回状态给微信服务器
            $this->errorReturn($orderSn, '回调的金额和订单的金额不符，终止购买');
            exit;
        }
        $modelOrder->startTrans();
        //更新订单状态
        $_POST = [];
        $_POST['logistics_status'] = 2;
        $_POST['payment_code'] = 0;
        $_POST['pay_sn'] = $data['pay_sn'];
        $_POST['payment_time'] = $data['transaction_id'];
        $_POST['orderId'] = $orderInfo['id'];
        $where = array(
            'user_id' =>$userId,
            'sn' => $orderSn,
        );
        $returnData = $modelOrder->saveOrder($where);
        if (!$returnData['id']) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            $this->errorReturn($orderSn, $modelOrder->getLastSql());
        }
        //更新代金券，已使用
        if ($orderInfo['coupons_id'] && $orderInfo['coupons_pay'] > 0) {
            $_POST = [];
            $_POST['status'] = 1;
            $_POST['couponsId'] = $orderInfo['coupons_id'];
            $where = array(
                'user_id' =>$userId,
            );
            $res = $modelCoupons->saveCouponsReceive($where);
            if ($res['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelCoupons->getLastSql());
            }
        }
        //更新账户
        if ($orderInfo['wallet_pay'] > 0) {
            //钱包信息
            $where = array(
                'w.user_id' =>$userId,
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $walletInfo = $walletInfo[0];
            if ($walletInfo['amount'] >= $orderInfo['wallet_pay']) {
                //更新账户
                $_POST = [];
                $_POST['amount'] = $walletInfo['amount'] - $orderInfo['wallet_pay'];
                $where = array(
                    'user_id' =>$userId,
                );
                $res = $modelWallet->saveWallet($where);
            }
            if ($res['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWallet->getLastSql());
            }
            //增加账户记录
            $_POST = [];
            $_POST['user_id'] =$userId;
            $_POST['amount'] = $orderInfo['wallet_pay'];
            $_POST['type'] = 2;
            $_POST['create_time'] = time();
            $res = $modelWalletDetail->addWalletDetail();
            if ($res['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderSn, $modelWalletDetail->getLastSql());
            }
        }
        $modelOrder->commit();//提交事务
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
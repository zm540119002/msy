<?php
namespace app\purchase\controller;
use Think\Controller;
use common\component\payment\unionpay\sdk\AcpService;
use common\component\payment\alipayMobile\lib\AlipayNotify;
use common\component\payment\weixin\Jssdk;
class CallBack extends \common\controller\Base{
    //支付回调
    public function notifyUrl(){
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.order') == true) {
            $this->callBack('weixin', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.recharge') == true) {
            $this->callBack('weixin',  'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.group_buy') == true) {
            $this->callBack('weixin',  'group_buy');
        }
        //支付宝回调
        if (strpos($_SERVER['QUERY_STRING'], 'ali.order') == true) {
            $this->callBack('ali', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'ali.recharge') == true) {
            $this->callBack('ali',  'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'ali.group_buy') == true) {
            $this->callBack('ali',  'group_buy');
        }
        //银联回调
        if (strpos($_SERVER['QUERY_STRING'], 'union.recharge') == true) {
            $this->callBack('union',  'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'union.order') == true) {
            $this->callBack('union', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'union.group_buy') == true) {
            $this->callBack('union',  'group_buy');
        }

    }

    /**
     * @param $data ///支付商返回的数据
     * @param $payment_type //支付方式
     * @param $order_type //支付单类型
     */
    //支付完成，调用不同的支付的回调处理
    private function callBack($payment_type, $order_type){
        if ($payment_type == 'weixin') {
            $this->weixinBack($order_type);
        }
        if ($payment_type == 'ali') {
            $this->aliBack($order_type);
        }
        if ($payment_type = 'union') {
            $this->unionBack($order_type);
        }
    }

    //微信支付回调处理
    private function weixinBack($order_type){
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = $this->makeSign($data);
        $data['payment_code'] = 1;
        $data['actually_amount'] =  $data['total_fee'];//支付金额
        $data['pay_sn'] = $data['transaction_id'];//服务商返回的交易号
        $data['order_sn'] = $data['out_trade_no'];//系统的订单号
        $data['payment_time'] = $data['time_end'];//支付时间
        // 判断签名是否正确  判断支付状态
        if ($sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            if ($order_type == 'order') {
                $res = $this->orderHandle($data);
                if($res['status']){
                    $this->successReturn();
                }else{
                    $this->errorReturn();
                }
            }
            if ($order_type == 'recharge') {
                $res = $this->rechargeHandle($data);
                if($res['status']){
                    $this->successReturn();
                }else{
                    $this->errorReturn();
                }
            }

            if ($order_type == 'group_buy') {
                $res = $this->groupBuyHandle($data);
                if($res['status']){
                    $this->successReturn();
                }else{
                    $this->errorReturn();
                }
            }
        } else {
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no']);
        }
    }

    //银联支付回调处理
    private function unionBack($order_type){
        $data = $_POST;
        //计算得出通知验证结果
        $unionpayNotify = new AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($data);
        if ($verify_result) //验证成功
        {
            $data['payment_code'] = 3;
            $data['order_sn'] = $data['orderId'];//系统的订单号
            $data['actually_amount'] =  $data['txnAmt'];//支付金额
            $data['pay_sn'] = $data['queryId'];//服务商返回的交易号
            $data['payment_time'] = $data['time_end'];//支付时间
            // 解释: 交易成功且结束，即不可再做任何操作。
            if ($data['respMsg'] == 'Success!') {
                // 修改订单支付状态
                if ($order_type == 'order') {
                    $res = $this->orderHandle($data);
                    if($res['status']){
                        echo "success"; // 处理成功
                    }else{
                        echo "fail"; //验证失败
                    }
                }

                if ($order_type == 'recharge') {
                    $res = $this->rechargeHandle($data);
                    if($res['status']){
                        echo "success"; // 处理成功
                    }else{
                        echo "fail"; //验证失败
                    }
                }
            }
        } else {
            echo "fail"; //验证失败
        }
    }

    //支付宝支付回调处理
    private function aliBack($order_type){
        $data = $_POST;
        $data['payment_code'] = 2; //支付类型
        $data['order_sn'] = $data['out_trade_no'];//系统的订单号
        $data['actually_amount'] =  $data['receipt_amount'];//支付金额
//        $data['actually_amount'] =  $data['total_amount'];//支付金额
        $data['pay_sn'] = $data['trade_no'];//服务商返回的交易号
        $data['payment_time'] = $data['gmt_close'];//支付时间
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config); // 使用支付宝原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $alipayNotify->verifyNotify();
        if (!$verify_result) {
            echo "fail";
            exit;
        }
        $trade_status = $data['trade_status']; //交易状态
        // 支付宝解释: 交易成功且结束，即不可再做任何操作。
        if ($trade_status == 'TRADE_FINISHED') {
            // 修改订单支付状态
            if ($order_type == 'recharge') {
                $this->rechargeHandle($data);
            }
            if ($order_type == 'order') {
                $this->orderHandle($data);
            }

        } //支付宝解释: 交易成功，且可对该交易做操作，如：多级分润、退款等。
        elseif ($trade_status == 'TRADE_SUCCESS') {
            // 修改订单支付状态
            if ($order_type == 'recharge') {
                $this->rechargeHandle($data);
            }
            if ($order_type == 'order') {
                $this->orderHandle($data);
            }
        }
        echo "success"; // 告诉支付宝处理成功
    }

    /**
     * @param $data
     * 普通订单支付回调
     */

    private function orderHandle($data){
        $modelOrder = new \app\purchase\model\Order();
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $data['order_sn']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id',
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);
        $userId = $orderInfo['user_id'];
        if ($orderInfo['logistics_status'] > 1) {
            return successMsg('已回调过，订单已处理');
        }
        if ($orderInfo['actually_amount'] * 100 != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
            //返回状态给微信服务器
            return errorMsg('回调的金额和订单的金额不符，终止购买');
        }
        $modelOrder->startTrans();
        //更新订单状态
        $data2 = [];
        $data2['logistics_status'] = 2;
        $data2['payment_code'] = $data['payment_code'];
        $data2['pay_sn'] = $data['pay_sn'];
        $data2['payment_time'] = $data['payment_time'];
        $condition = [
            ['user_id','=',$userId],
            ['sn','=',$data['order_sn']],
        ];

        $returnData = $modelOrder->edit($data2,$condition);
        if (!$returnData['status']) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            return errorMsg($modelOrder->getLastSql());
        }

        $modelOrder->commit();//提交事务
        //返回状态给微信服务器
        return successMsg('成功');

    }

    /**充值支付回调
     * @param $parameter
     */
    private function rechargeHandle($data){

    }

    /**团购订单支付回调
     * @param $parameter
     */
    private function groupBuyHandle($data){

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



    /**
     * 微信生成签名
     * @return 签名，本函数不覆盖sign成员变量
     */
    function makeSign($data){
        //获取微信支付秘钥
        $key = config('wx_config.key');
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config);
        $verify_result = $alipayNotify->verifyReturn();

        if($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $_GET['out_trade_no']; //商户订单号
            $trade_no = $_GET['trade_no']; //支付宝交易号
            $trade_status = $_GET['trade_status']; //交易状态

            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS')
            {
                return  array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面
            }
            else {
                return  array('status'=>0,'order_sn'=>$order_sn); //跳转至失败页面
            }
        }
        else
        {
            return  array('status'=>0,'order_sn'=>$_GET['out_trade_no']);//跳转至失败页面
        }
    }


    
}
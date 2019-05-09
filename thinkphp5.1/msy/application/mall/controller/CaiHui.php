<?php
namespace app\mall\controller;

class CaiHui extends \common\controller\Base{
    //商城首页
    public function index(){
        return $this->fetch();
    }

    //产品展示
    public function goods(){
        return $this->fetch();
    }

    //关于我们
    public function about(){
        return $this->fetch();
    }
    //关于美尚云
    public function about1(){
        return $this->fetch();
    }
    //联系方式
    public function contact(){
        return $this->fetch();
    }

    //商品详情页
    public function detail(){
        $goods_id = input('goods_id');
        $this->assign('goods_id',$goods_id);
        return $this->fetch();
    }

    //结算
    public function pay(){
        $goodsInfo = input();
        $goodsInfo['amount'] = $goodsInfo['price'] * $goodsInfo['num'];
        $this -> assign('goodsInfo',$goodsInfo);
        return $this->fetch();
    }


    //订单-支付
    public function orderPayment(){
        $data = input('');
        $payInfo = [
            'sn'=>generateSN(),
            'actually_amount'=>$data['amount'],
            'return_url' => $this->host.url('payComplete'),
            'notify_url'=>$this->host.url('notifyUrl')
        ];

        $payCode = input('pay_code','0','int');
        if($payCode == 1){
            $payInfo['notify_url'] = $payInfo['notify_url'].'/weixin';
            \common\component\payment\weixin\weixinpay::wxPay($payInfo);
        }
        //支付宝支付
        if($payCode == 2){
            $payInfo['notify_url'] = $payInfo['notify_url'].'/ali';
            $model = new \common\component\payment\alipay\alipay;
            $model->aliPay($payInfo);
        }
        //银联支付
        if($payCode == 3){
            $payInfo['notify_url'] = $payInfo['notify_url'].'/union';
            $model = new \common\component\payment\unionpay\unionpay;
            $model->unionPay($payInfo);
        }
    }

    public function notifyUrl(){
        //微信回调
        if (strpos($_SERVER['QUERY_STRING'], 'weixin') == true) {
            $this->weixinBack();
        }
        //支付宝回调
        if (strpos($_SERVER['QUERY_STRING'], 'ali') == true) {
            $this->unionBack();
        }
        //银联回调
        if (strpos($_SERVER['QUERY_STRING'], 'union') == true) {
            $this->aliBack();
        }

    }

    //微信支付回调处理
    private function weixinBack()
    {
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = $this->makeSign($data);
        $data['pay_code'] = 1;//weixin 支付
        $data['actually_amount'] = $data['total_fee'];//支付金额
        $data['pay_sn'] = $data['transaction_id'];//服务商返回的交易号
        $data['order_sn'] = $data['out_trade_no'];//系统的订单号
        $data['payment_time'] = $data['time_end'];//支付时间

        // 判断签名是否正确  判断支付状态
        if ($sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            $modelOrder = new \app\purchase\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $data['order_sn']],
                ], 'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id', 'o.actually_amount', 'o.logistics_status'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            if ($orderInfo['logistics_status'] > 1) {
                return successMsg('已回调过，订单已处理');
            }
            if ($orderInfo['actually_amount'] * 100 != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                return errorMsg('回调的金额和订单的金额不符，终止购买');
            }
            $res = $this->orderHandle($data, $orderInfo);
            if ($res['status']) {
                $this->successReturn();
            } else {
                $this->errorReturn();
            }
        } else {
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no']);
        }
    }


    //银联支付回调处理
    private function unionBack()
    {
        $data = $_POST;
        //计算得出通知验证结果

        $unionpayNotify = new AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($data);
        if ($verify_result) //验证成功
        {
            $data['pay_code'] = 3;
            $data['order_sn'] = $data['orderId'];//系统的订单号
            $data['actually_amount'] = $data['txnAmt'];//支付金额
            $data['pay_sn'] = $data['queryId'];//服务商返回的交易号
            $data['payment_time'] = $data['time_end'];//支付时间
            // 解释: 交易成功且结束，即不可再做任何操作。
            if ($data['respMsg'] == 'Success!') {
                // 修改订单支付状态
                $modelOrder = new \app\purchase\model\Order();
                $config = [
                    'where' => [
                        ['o.status', '=', 0],
                        ['o.sn', '=', $data['order_sn']],
                    ], 'field' => [
                        'o.id', 'o.sn', 'o.amount',
                        'o.user_id', 'o.actually_amount', 'o.logistics_status'
                    ],
                ];
                $orderInfo = $modelOrder->getInfo($config);
                $res = $this->orderHandle($data, $orderInfo);
                if ($res['status']) {
                    echo "success"; // 处理成功
                } else {
                    echo "fail"; //验证失败
                }

            }
        } else {
            echo "fail"; //验证失败
        }
    }

    //支付宝支付回调处理
    private function aliBack()
    {
        require_once dirname(__DIR__) . './../../../common/component/payment/alipay/wappay/service/AlipayTradeService.php';
        require_once dirname(__DIR__) . './../../../common/component/payment/alipay/config.php';
        $data = $_POST;
        $payInfo['pay_code'] = 2; //支付类型
        $payInfo['order_sn'] = $data['out_trade_no'];//系统的订单号
        $payInfo['actually_amount'] = $data['receipt_amount'];//支付金额
        $payInfo['pay_sn'] = $data['trade_no'];//服务商返回的交易号
        $payInfo['payment_time'] = $data['gmt_payment'];//支付时间

        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST, true));
        $result = $alipaySevice->check($_POST);
        if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
            //如果有做过处理，不执行商户的业务程序
            //注意：
            //付款完成后，支付宝系统发送该交易状态通知

            $modelOrder = new \app\purchase\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $payInfo['order_sn']],
                ], 'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id', 'o.actually_amount'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            $res = $this->orderHandle($payInfo, $orderInfo);
            if (!$res['status']) {
                echo "fail";    //请不要修改或删除
            } else {
                echo "success";        //请不要修改或删除
            }
        }
        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
//        if(!$result) {//验证成功
//            file_put_contents('ali3.text',json_encode($data));
//            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            //请在这里加上商户的业务逻辑程序代
//
//            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
//
//            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
//
//            //商户订单号
//
//            if($_POST['trade_status'] == 'TRADE_FINISHED') {
//
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//
//                //注意：
//                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
//
//                // 修改订单支付状态
//                if ($order_type == 'order') {
//                    file_put_contents('ali4.text',json_encode($data));
//                    $modelOrder = new \app\purchase\model\Order();
//                    $config = [
//                        'where' => [
//                            ['o.status', '=', 0],
//                            ['o.sn', '=', $data['order_sn']],
//                        ],'field' => [
//                            'o.id', 'o.sn', 'o.amount',
//                            'o.user_id','o.actually_amount','o.logistics_status'
//                        ],
//                    ];
//                    $orderInfo = $modelOrder->getInfo($config);
//                    $res = $this->orderHandle($data,$orderInfo);
//                    if($res['status']){
//                        echo "success"; // 处理成功
//                    }else{
//                        echo "fail"; //验证失败
//                    }
//                }
//                //修改支付订单支付状态
//                if ($order_type == 'recharge') {
//                    $this->rechargeHandle($data);
//                }
//
//            }
//
//            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//                //注意：
//                //付款完成后，支付宝系统发送该交易状态通知
//
//
//                if ($order_type == 'order') {
//                    file_put_contents('ali5.text',json_encode($data));
//                    $modelOrder = new \app\purchase\model\Order();
//                    $config = [
//                        'where' => [
//                            ['o.status', '=', 0],
//                            ['o.sn', '=', $data['order_sn']],
//                        ],'field' => [
//                            'o.id', 'o.sn', 'o.amount',
//                            'o.user_id','o.actually_amount'
//                        ],
//                    ];
//                    $orderInfo = $modelOrder->getInfo($config);
//                    $res = $this->orderHandle($data,$orderInfo);
//                    if(!$res['status']){
//                        echo "fail";	//请不要修改或删除
//                    }
//                }
//
//                if ($order_type == 'recharge') {
//                    $this->rechargeHandle($data);
//                }
//            }
//            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
//            echo "success";		//请不要修改或删除
//
//        }else {
//            //验证失败
//            echo "fail";	//请不要修改或删除
//
//        }

    }

    /**
     * @param $data
     * 普通订单支付回调
     */

    private function orderHandle($data, $orderInfo)
    {
        $modelOrder = new \app\purchase\model\Order();
        $modelOrder->startTrans();
        //更新订单状态
        $data2 = [];
        $data2['logistics_status'] = 2;
        $data2['pay_code'] = $data['pay_code'];
        $data2['pay_sn'] = $data['pay_sn'];
        $data2['payment_time'] = $data['payment_time'];
        $condition = [
            ['user_id', '=', $orderInfo['user_id']],
            ['sn', '=', $data['order_sn']],
        ];
        $returnData = $modelOrder->edit($data2, $condition);
        if (!$returnData['status']) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            return errorMsg($modelOrder->getLastSql());
        }

        $modelOrder->commit();//提交事务
        //返回状态给微信服务器
        return successMsg('成功');

    }

    //成功返回
    private function successReturn()
    {
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return true;
    }

    //失败返回
    private function errorReturn($dataSn = '', $error = '签名错误', $type = '订单')
    {
        \Think\Log::write($type . '支付失败：' . $dataSn . "\r\n失败原因：" . $error, 'NOTIC');
        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        return false;
    }


    /**
     * 微信生成签名
     * @return 签名，本函数不覆盖sign成员变量
     */
    private function makeSign($data)
    {
        //获取微信支付秘钥
        $key = config('wx_config.key');
        // 去空
        $data = array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = http_build_query($data);
        $string_a = urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp = $string_a . "&key=" . $key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);
        return $result;
    }
}
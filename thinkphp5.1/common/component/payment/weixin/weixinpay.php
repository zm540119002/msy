<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 14:45
 */

namespace common\component\payment\weixin;
require_once(dirname(__FILE__) . '/lib/WxPay.Api.php');
//require_once(dirname(__FILE__) . '/lib/WxPay.Exception.php');
require_once(dirname(__FILE__)  . '/WxPay.JsApiPay.php');
require_once(dirname(__FILE__)  . '/WxPay.NativePay.php');
require_once(dirname(__FILE__)  . '/log.php');

class weixinpay{
    public $msg = '';
    /**支付端判断
     * @param $payInfo
     * @param $backUrl
     */
    public static function wxPay($payInfo){

        if (!isPhoneSide()) {//pc端微信扫码支付
            return weixinpay::pc_pay($payInfo);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false ){
            //手机端非微信浏览器
            return weixinpay::h5_pay($payInfo);
        }else{//微信浏览器(手机端)
            return weixinpay::getJSAPI($payInfo);
        }
    }

    /**微信公众号支付
     * @param  string   $openId 	openid
     * @param  string   $goods 		商品名称
     * @param  string   $attach 	附加参数,我们可以选择传递一个参数,比如订单ID
     * @param  string   $order_sn	订单号
     * @param  string   $total_fee  金额
     */
    public static function getJSAPI($payInfo){
        try{
            $payInfo['success_url'] = $payInfo['success_url']?:url('Index/index');
            $input = new \WxPayUnifiedOrder();
            $input->SetBody('美尚云');					                  //商品名称
            $input->SetAttach($payInfo['attach']);			              //附加参数,可填可不填,填写的话,里边字符串不能出现空格
            $input->SetOut_trade_no($payInfo['sn']);			          //订单号
            $input->SetProduct_id($payInfo['product']);			          //商品ID
            $input->SetTotal_fee($payInfo['actually_amount'] * 100);	  //支付金额,单位:分
            $input->SetTime_start(date("YmdHis"));		                  //支付发起时间
            $input->SetTime_expire(date("YmdHis", time() + 600));         //支付超时
            $input->SetGoods_tag("test3");
            $input->SetNotify_url($payInfo['notify_url']);                //支付回调验证地址
            $input->SetTrade_type("JSAPI");				                  //支付类型
            $input->SetOpenid($payInfo['open_id']);					  //用户openID
            $order = \WxPayApi::unifiedOrder($input);	                  //统一下单
            $tools = new \JsApiPay();
            $jsApiParameters = $tools->GetJsApiParameters($order);
            return $jsApiParameters;
        } catch(\Exception $e) {
            //\Log::ERROR(json_encode($e));
            return $e->getMessage();
        }


    }

    /**生成支付代码 扫码支付
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    public static function pc_pay($payInfo)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("美尚云"); // 商品描述
        $input->SetAttach($payInfo['attach']); // 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($payInfo['sn']); // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($payInfo['actually_amount']*100); // 订单总金额，单位为分，详见支付金额
        $input->SetNotify_url($payInfo['notify_url']); // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("NATIVE"); // 交易类型   取值如下：JSAPI，NATIVE，APP，详细说明见参数规定    NATIVE--原生扫码支付
        $input->SetProduct_id($payInfo['product']); // 商品ID trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input); // 获取生成二维码的地址
        $url2 = $result["code_url"];
        $code_url = createLogoQRcode($url2,config('upload_dir.pay_QRcode'));
        return $code_url;
//        $html = <<<EOF
//            <head>
//               <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/jquery/jquery-1.9.1.min.js"></script>
//			   <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/layer.mobile/layer.js"></script>
//			   <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/dialog.js"></script>
//            </head>
//            <body>
//                    <script type="text/javascript">
//                        $(function(){
//                          layer.open({
//                                title:['微信支付二维码','border-bottom:1px solid #d9d9d9'],
//                                className:'',
//                                content:'<img src="/uploads/{$code_url}">'
//                         })
//                     });
//                </script>
//            <body>
//EOF;
//        echo  $html;
    }

    //生成支付二维码
    public static function payQRcode($url,$logo=''){
        //生成二维码图片
        $object = new \common\component\code\Qrcode();
        $value =$url; //二维码内容
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $qrcodePath = config('uploads');//保存文件路径
        $fileName = time().'.png';//保存文件名
        $outFile = $qrcodePath.$fileName;
       //生成二维码图片
        $object->png($value, $outFile, $errorCorrectionLevel, $matrixPointSize, 2,$saveandprint=false);
        $QR = $outFile;//已经生成的原始二维码图
        if (!empty($logo)) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }
         //输出图片
        imagepng($QR, 'helloweixin.png');
        echo '<img src="helloweixin.png">';


    }

    /**
     * @param $payInfo
     * H5 微信支付
     */
    public static function h5_pay($payInfo){
        //统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
        //使用统一支付接口
        $input = new \WxPayUnifiedOrder();
        $input->SetBody('美尚云');					//商品名称
        $input->SetAttach($payInfo['attach']);					//附加参数,可填可不填,填写的话,里边字符串不能出现空格
        $input->SetOut_trade_no($payInfo['sn']);			//订单号
        $input->SetTotal_fee($payInfo['actually_amount'] *100);			//支付金额,单位:分
        $input->SetTime_start(date("YmdHis"));		//支付发起时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
        $input->SetGoods_tag("test3");
        $input->SetNotify_url($payInfo['notify_url']);//支付回调验证地址
        $input->SetTrade_type("MWEB");				//支付类型
        $order2 = \WxPayApi::unifiedOrder($input);	//统一下单
        $url = $order2['mweb_url'];
        $url = $url.'&redirect_url='.$payInfo['success_url'];//拼接支付完成后跳转的页面redirect_url
        $html = <<<EOF
            <head>
               <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/jquery/jquery-1.9.1.min.js"></script>
			   <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/layer.mobile/layer.js"></script>
			   <script type="text/javascript" src="https://api.worldview.com.cn/static/common/js/dialog.js"></script>	
            </head>
            <body>
                 <a class="weixin_pay_h5" href="javascript:void(0);"></a>
                 <input type="hidden" class="url" value="$url">
                    <script type="text/javascript">
                        $(function(){
                        var url =$('.url').val();
                       location.href=url;
                     });
                </script>
            <body>
EOF;
        echo  $html;
    }

    /**微信退款
     * @param  array   $refund 	订单ID
     * $refund 需传4个参数：
     * 'order_sn', 商家生成订单Sn
     * 'transaction_id', 微信官方生成的订单流水号，在支付成功中有返回
     * 'total_price',' 订单标价金额，单位为分
     * 'refund_amount', 退款总金额，订单总金额，单位为分，只能为整数
     * @return 成功时返回(array类型)，其他抛异常
     */
    public static function wxRefund($refund){
        //查询订单,根据订单里边的数据进行退款
        $merchid = config('wx_config.mchid');
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($refund['order_sn']);			//自己的订单号
        $input->SetTransaction_id($refund['transaction_id']);  	//微信官方生成的订单流水号，在支付成功中有返回
        $input->SetOut_refund_no(generateSN(10));			//退款单号
        $input->SetTotal_fee($refund['total_price']);			//订单标价金额，单位为分
        $input->SetRefund_fee($refund['refund_amount']);			//退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOp_user_id($merchid);

        $result = \WxPayApi::refund($input);	//退款操作

        // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log3.txt',arrayToXml($result),FILE_APPEND);
        return $result;
    }

    // 订单查询
    public static function wxOrderQuery($orderSn,$transactionId){
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($orderSn);			//自己的订单号
        $input->SetTransaction_id($transactionId);  //微信官方生成的订单流水号，在支付成功中有返回
        $result = \WxPayApi::orderQuery($input);	//退款操作

        // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log3.txt',arrayToXml($result),FILE_APPEND);
        return $result;
    }


    // 微信支付回调认证
    public function wxNotify(){

        try {
            //获取通知的数据
            //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $xml = file_get_contents('php://input');


            //file_put_contents('./array.json',json_encode($xml));

            $xml = json_decode(file_get_contents('./array.json'),true);



            $data = \WxPayResults::Init($xml);

            if(!$this->Queryorder($data)){
                //$msg = "订单查询失败";
                return false;
            }
        } catch (\WxPayException $e){
            //$msg = $e->errorMessage();
            // 记录日志
            //\think\facade\Log::init(['path' => '../logs/wx/']);
            \think\facade\Log::init(['path' => './logs/pay/']);
            \think\facade\Log::error('微信支付回调',$e);
            \think\facade\Log::save();

            return false;
        }
        return $data;

    }

    //查询订单
    public function queryOrder($data)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($data['transaction_id']);
        $input->SetOut_trade_no($data['out_trade_no']);
        $result = \WxPayApi::orderQuery( $input);

        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }

        //Log::DEBUG("query:" . json_encode($result));
        // 记录日志
        //\think\facade\Log::init(['path' => '../logs/wx/']);
        \think\facade\Log::init(['path' => './logs/pay/']);
        \think\facade\Log::error('微信支付后订单查询',$result);
        \think\facade\Log::save();
        return false;
    }

    // 确定退款
    public function refundOrder($data){

        try {
            $this->getWxOpenid();
            $input = new \WxPayRefund();
            $input->SetTransaction_id($data['pay_sn']);
            $input->SetOut_refund_no($data['sn']);
            $input->SetTotal_fee($data['actually_amount'] * 100);
            $input->SetRefund_fee($data['actually_amount'] * 100);
            $input->SetOp_user_id(session('pay_open_id'));
            $result =  \WxPayApi::refund( $input);

        } catch (\WxPayException $e){

            $this->msg = $e->errorMessage();
            \think\facade\Log::init(['path' => './logs/pay/']);
            \think\facade\Log::error('微信退款失败: ',$this->msg);
            \think\facade\Log::error($data);
            \think\facade\Log::save();
            return false;
        }

        // 结果处理
        if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return $result;

        }else{
            \think\facade\Log::init(['path' => './logs/pay/']);
            \think\facade\Log::error('微信退款失败: ',$result['err_code_des']);
            \think\facade\Log::error($data);
            \think\facade\Log::save();

            return false;
        }

    }


    private function getWxOpenid(){
        if(empty(session('pay_open_id'))){
            $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
            $payOpenId  = $tools->getOpenid();
            session('pay_open_id',$payOpenId);
        }
    }




}
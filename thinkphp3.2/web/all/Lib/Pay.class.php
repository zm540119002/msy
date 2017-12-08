<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 14:45
 */

namespace web\all\Lib;
use web\all\Component\UnionPay\SDKConfig;
use web\all\Component\UnionPay\AcpService;
use web\all\Component\UnionPay\LogUtil;

require_once(dirname(dirname(__FILE__)) . '/Component/WxpayAPI/lib/WxPay.Api.php');
require_once(dirname(dirname(__FILE__)) . '/Component/WxpayAPI/WxPay.JsApiPay.php');
require_once(dirname(dirname(__FILE__)) . '/Component/WxpayAPI/log.php');

class Pay
{

//    /**
//     * 微信支付
//     * @param  string   $openId 	openid
//     * @param  string   $goods 		商品名称
//     * @param  string   $attach 	附加参数,我们可以选择传递一个参数,比如订单ID
//     * @param  string   $order_sn	订单号
//     * @param  string   $total_fee  金额
//     */
//
//    public static function wxPay($total_fee,$notify_url,$order_sn,$goods='美尚云',$attach=''){
//        $tools = new \JsApiPay();
//        $openId = $tools->GetOpenid();
//        $input = new \WxPayUnifiedOrder();
//        $input->SetBody($goods);					//商品名称
//        $input->SetAttach($attach);					//附加参数,可填可不填,填写的话,里边字符串不能出现空格
//        $input->SetOut_trade_no($order_sn);			//订单号
//        $input->SetTotal_fee($total_fee *100);			//支付金额,单位:分
//        $input->SetTime_start(date("YmdHis"));		//支付发起时间
//        $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
//        $input->SetGoods_tag("test3");
//        $input->SetNotify_url($notify_url);//支付回调验证地址
//        $input->SetTrade_type("JSAPI");				//支付类型
//        $input->SetOpenid($openId);					//用户openID
//        $order = \WxPayApi::unifiedOrder($input);	//统一下单
//        $jsApiParameters = $tools->GetJsApiParameters($order);
//        return $jsApiParameters;
//    }
//
//
//
//    /**
//     * 微信退款
//     * @param  array   $refund 	订单ID
//     * $refund 需传4个参数：
//     * 'order_sn', 商家生成订单Sn
//     * 'transaction_id', 微信官方生成的订单流水号，在支付成功中有返回
//     * 'total_price',' 订单标价金额，单位为分
//     * 'refund_amount', 退款总金额，订单总金额，单位为分，只能为整数
//     * @return 成功时返回(array类型)，其他抛异常
//     */
//    public static function wxRefund($refund){
//        //查询订单,根据订单里边的数据进行退款
//        $merchid = C('WX_CONFIG')['MCHID'];
//        $input = new \WxPayRefund();
//        $input->SetOut_trade_no($refund['order_sn']);			//自己的订单号
//        $input->SetTransaction_id($refund['transaction_id']);  	//微信官方生成的订单流水号，在支付成功中有返回
//        $input->SetOut_refund_no(generateOrderSN(10));			//退款单号
//        $input->SetTotal_fee($refund['total_price']);			//订单标价金额，单位为分
//        $input->SetRefund_fee($refund['refund_amount']);			//退款总金额，订单总金额，单位为分，只能为整数
//        $input->SetOp_user_id($merchid);
//
//        $result = \WxPayApi::refund($input);	//退款操作
//
//        // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了
//        //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log3.txt',arrayToXml($result),FILE_APPEND);
//        return $result;
//    }

    /**
     * 架构函数
     *
     */
    public function __construct() {
        require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件
        require_once("example/WxPay.NativePay.php");
        require_once("example/WxPay.JsApiPay.php");
        $paymentPlugin = D('Plugin')->where("code='weixin' and  type = 'payment' ")->find(); // 找到微信支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
        \WxPayConfig::$appid = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        \WxPayConfig::$mchid = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        \WxPayConfig::$key = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        \WxPayConfig::$appsecret = $config_value['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，
    }


    /**
     * 生成支付代码 扫码支付
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    function pc_pay($order, $config_value)
    {
        $notify_url = SITE_URL.'/index.php/Home/Payment/notifyUrl/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("美尚云"); // 商品描述
        $input->SetAttach("weixin"); // 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($order['sn']); // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($order['actually_amount']*100); // 订单总金额，单位为分，详见支付金额
        $input->SetNotify_url($notify_url); // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("NATIVE"); // 交易类型   取值如下：JSAPI，NATIVE，APP，详细说明见参数规定    NATIVE--原生扫码支付
        $input->SetProduct_id("123456789"); // 商品ID trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input); // 获取生成二维码的地址
        $url2 = $result["code_url"];
        $this->payQRcode($url2);
    }

    //生成支付二维码
    public function payQRcode($url){
        //生成二维码图片
        $object = new Qrcode();
        $qrcodePath = WEB_URL.'Public/images/qrcode/';//保存文件路径
        $fileName = time().'.png';//保存文件名
        $outFile = $qrcodePath.$fileName;
        $level = 'L'; //容错级别
        $size = 10; //生成图片大小
        $frameSize = 2; //边框像素
        $saveAndPrint = true;
        $object->png($url, $outFile, $level, $size, $frameSize,$saveAndPrint);
    }

    function h5_pay($order){
        //统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
        //使用统一支付接口
        $input = new \WxPayUnifiedOrder();
        $input->SetBody('美尚云');					//商品名称
        $input->SetAttach('weixin');					//附加参数,可填可不填,填写的话,里边字符串不能出现空格
        $input->SetOut_trade_no($order['sn']);			//订单号
        $input->SetTotal_fee($order['actually_amount'] *100);			//支付金额,单位:分
        $input->SetTime_start(date("YmdHis"));		//支付发起时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
        $input->SetGoods_tag("test3");
        $input->SetNotify_url(SITE_URL.'/index.php/Purchase/Payment/notifyUrl/pay_code/weixin');//支付回调验证地址
        $input->SetTrade_type("MWEB");				//支付类型
        $order2 = \WxPayApi::unifiedOrder($input);	//统一下单
        $url = $order2['mweb_url'];
        return $url;

    }

    /**
     * 服务器点对点响应操作给支付接口方调用
     *
     */
    function response()
    {
        require_once("example/notify.php");
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }

    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        // 微信扫码支付这里没有页面返回
    }

    function getJSAPI($order){
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody('美尚云');					//商品名称
        $input->SetAttach('weixin');					//附加参数,可填可不填,填写的话,里边字符串不能出现空格
        $input->SetOut_trade_no($order['sn']);			//订单号
        $input->SetTotal_fee($order['actually_amount'] *100);			//支付金额,单位:分
        $input->SetTime_start(date("YmdHis"));		//支付发起时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
        $input->SetGoods_tag("test3");
        $input->SetNotify_url(SITE_URL.'/index.php/Purchase/Payment/notifyUrl/pay_code/weixin');//支付回调验证地址
        $input->SetTrade_type("JSAPI");				//支付类型
        $input->SetOpenid($openId);					//用户openID
        $order2 = \WxPayApi::unifiedOrder($input);	//统一下单
        $jsApiParameters = $tools->GetJsApiParameters($order2);
        $html = <<<EOF
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',$jsApiParameters,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok") {
 						location.href = '/index.php/Purchase/recharge/payComplete';
				 }else{
				 	alert(res.err_code+res.err_desc+res.err_msg);
				   
				 }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	callpay();
	</script>
EOF;

        echo  $html;

    }

    function transfer($data){
        //CA证书及支付信息
        $wxchat['appid'] = \WxPayConfig::$appid;
        $wxchat['mchid'] = \WxPayConfig::$smchid;
        $wxchat['api_cert'] = './Component/payment/weixin/cert/apiclient_cert.pem';
        $wxchat['api_key'] = './Component/payment/weixin/cert/apiclient_key.pem';
//    	$wxchat['api_ca'] = './Component/payment/weixin/cert/rootca.pem';
        $webdata = array(
            'mch_appid' => $wxchat['appid'],
            'mchid'     => $wxchat['smchid'],
            'nonce_str' => md5(time()),

            'partner_trade_no'=> $data['pay_code'], //商户订单号，需要唯一
            'openid' => $data['openid'],//转账用户的openid
            'check_name'=> 'NO_CHECK', //OPTION_CHECK不强制校验真实姓名, FORCE_CHECK：强制 NO_CHECK：
            //'re_user_name' => 'jorsh', //收款人用户姓名
            'amount' => $data['money'] * 100, //付款金额单位为分
            'desc'   => empty($data['desc'])? '退款' : $data['desc'],
            'spbill_create_ip' => request()->ip(),
        );
        foreach ($webdata as $k => $v) {
            $tarr[] =$k.'='.$v;
        }
        sort($tarr);
        $sign = implode($tarr, '&');
        $sign .= '&key='.\WxPayConfig::$key;
        $webdata['sign']=strtoupper(md5($sign));
        $wget = $this->array2xml($webdata);
        $pay_url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $res = $this->http_post($pay_url, $wget, $wxchat);
        if(!$res){
            return array('status'=>1, 'msg'=>"Can't connect the server" );
        }
        $content = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(strval($content->return_code) == 'FAIL'){
            return array('status'=>1, 'msg'=>strval($content->return_msg));
        }
        if(strval($content->result_code) == 'FAIL'){
            return array('status'=>1, 'msg'=>strval($content->err_code),':'.strval($content->err_code_des));
        }
        $rdata = array(
            'mch_appid'        => strval($content->mch_appid),
            'mchid'            => strval($content->mchid),
            'device_info'      => strval($content->device_info),
            'nonce_str'        => strval($content->nonce_str),
            'result_code'      => strval($content->result_code),
            'partner_trade_no' => strval($content->partner_trade_no),
            'payment_no'       => strval($content->payment_no),
            'payment_time'     => strval($content->payment_time),
        );
        return $rdata;
    }

    /**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }



    function http_post($url, $param, $wxchat) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        if($wxchat){
            curl_setopt($oCurl,CURLOPT_SSLCERT,dirname(THINK_PATH).$wxchat['api_cert']);
            curl_setopt($oCurl,CURLOPT_SSLKEY,dirname(THINK_PATH).$wxchat['api_key']);
            curl_setopt($oCurl,CURLOPT_CAINFO,dirname(THINK_PATH).$wxchat['api_ca']);
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    function http_post2($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    //支付金额原路退还
    public function payment_refund($data){
        if(!empty($data["transaction_id"])){
            $input = new \WxPayRefund();
            $input->SetTransaction_id($data["transaction_id"]);
            $input->SetTotal_fee($data["total_fee"]);
            $input->SetRefund_fee($data["refund_fee"]);
            $input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
            $input->SetOp_user_id(\WxPayConfig::MCHID);
            return \WxPayApi::refund($input);
        }else{
            return false;
        }
    }


}
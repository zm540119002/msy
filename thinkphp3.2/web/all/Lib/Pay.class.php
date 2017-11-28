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
    public static function unionPay(){
        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',				  //编码方式
            'txnType' => '01',				      //交易类型
            'txnSubType' => '01',				  //交易子类
            'bizType' => '000201',				  //业务类型
            'frontUrl' =>  SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
            'backUrl' => SDKConfig::getSDKConfig()->backUrl,	  //后台通知地址
            'signMethod' => SDKConfig::getSDKConfig()->signMethod,	              //签名方法
            'channelType' => '08',	              //渠道类型，07-PC，08-手机
            'accessType' => '0',		          //接入类型
            'currencyCode' => '156',	          //交易币种，境内商户固定156


            //TODO 以下信息需要填写
//            'merId' => $_POST["merId"],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
//            'orderId' => $_POST["orderId"],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
//            'txnTime' => $_POST["txnTime"],	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
//            'txnAmt' => $_POST["txnAmt"],	//交易金额，单位分，此处默认取demo演示页面传递的参数
           'merId' => '1111',		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
           'orderId' =>'11111',	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => 20171111021214,	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
           'txnAmt' => 1,	//交易金额，单位分，此处默认取demo演示页面传递的参数


            // 订单超时时间。
            // 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
            // 此时间建议取支付时的北京时间加15分钟。
            // 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
            'payTimeout' => date('YmdHis', strtotime('+15 minutes')),

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),

            //TODO 其他特殊用法请查看 special_use_purchase.php
        );

        AcpService::sign ( $params );
        $uri = SDKConfig::getSDKConfig()->frontTransUrl;
        $html_form = AcpService::createAutoFormHtml( $params, $uri );
        echo $html_form;;
    }




    /**
     * 微信支付
     * @param  string   $openId 	openid
     * @param  string   $goods 		商品名称
     * @param  string   $attach 	附加参数,我们可以选择传递一个参数,比如订单ID
     * @param  string   $order_sn	订单号
     * @param  string   $total_fee  金额
     */

    public static function wxPay($total_fee,$notify_url,$order_sn,$goods='美尚云',$attach=''){
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($goods);					//商品名称
        $input->SetAttach($attach);					//附加参数,可填可不填,填写的话,里边字符串不能出现空格
        $input->SetOut_trade_no($order_sn);			//订单号
        $input->SetTotal_fee($total_fee *100);			//支付金额,单位:分
        $input->SetTime_start(date("YmdHis"));		//支付发起时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
        $input->SetGoods_tag("test3");
        $input->SetNotify_url($notify_url);//支付回调验证地址
        $input->SetTrade_type("JSAPI");				//支付类型
        $input->SetOpenid($openId);					//用户openID
        $order = \WxPayApi::unifiedOrder($input);	//统一下单
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }



    /**
     * 微信退款
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
        $merchid = C('WX_CONFIG')['MCHID'];
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($refund['order_sn']);			//自己的订单号
        $input->SetTransaction_id($refund['transaction_id']);  	//微信官方生成的订单流水号，在支付成功中有返回
        $input->SetOut_refund_no(generateOrderSN(10));			//退款单号
        $input->SetTotal_fee($refund['total_price']);			//订单标价金额，单位为分
        $input->SetRefund_fee($refund['refund_amount']);			//退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOp_user_id($merchid);

        $result = \WxPayApi::refund($input);	//退款操作

        // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Api/wxpay/logs/log3.txt',arrayToXml($result),FILE_APPEND);
        return $result;
    }
}
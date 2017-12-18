<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 14:45
 */

namespace  web\all\Component\payment\weixin;
require_once(dirname(__FILE__) . '/lib/WxPay.Api.php');
require_once(dirname(__FILE__) . '/example/WxPay.JsApiPay.php');
require_once(dirname(__FILE__) . '/example/log.php');

class Pay
{


    /**
     * 架构函数
     *
     */
    public function __construct() {
//		require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件
//		require_once("example/WxPay.NativePay.php");
//		require_once("example/WxPay.JsApiPay.php");
        $paymentPlugin = D('Plugin')->where("code='weixin' and  type = 'payment' ")->find(); // 找到微信支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
        \WxPayConfig::$appid = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        \WxPayConfig::$mchid = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        \WxPayConfig::$key = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        \WxPayConfig::$appsecret = $config_value['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，
    }
    /**
     * 微信支付
     * @param  string   $openId 	openid
     * @param  string   $goods 		商品名称
     * @param  string   $attach 	附加参数,我们可以选择传递一个参数,比如订单ID
     * @param  string   $order_sn	订单号
     * @param  string   $total_fee  金额
     */

    public static function wxPay($order){
        print_r($order);exit;
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
        $input->SetNotify_url($order['notify_url']);//支付回调验证地址
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
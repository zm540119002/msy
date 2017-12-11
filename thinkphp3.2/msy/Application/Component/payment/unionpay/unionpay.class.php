<?php
namespace Component\payment\unionpay;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 10:16
 */
require_once(dirname(__FILE__) .'/sdk/acp_service.php');
class unionpay
{
    public $unionpay_config = array();// 银联支付配置参数

    /**
     * 析构流函数
     */
    public function  __construct() {
        echo  '开发中';exit;
        unset($_GET['pay_code']);   // 删除掉 以免被进入签名
        unset($_REQUEST['pay_code']);// 删除掉 以免被进入签名
        $paymentPlugin = D('Plugin')->where("code='unionpay' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化

        $this->unionpay_config['unionpay_mid']           = $config_value['unionpay_mid']; // 商户号
        $this->unionpay_config['unionpay_cer_password']  = $config_value['unionpay_cer_password'];// 商户私钥证书密码
        $this->unionpay_config['unionpay_user']          = $config_value['unionpay_user'];//企业网银账号
        $this->unionpay_config['unionpay_password']	     = $config_value['unionpay_password'];//企业网银密码

    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    function get_code($order, $config_value)
    {
      
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',                 //版本号
            'encoding' => 'utf-8',				  //编码方式
            'txnType' => '01',				      //交易类型
            'txnSubType' => '01',				  //交易子类
            'bizType' => '000201',				  //业务类型
            'frontUrl' => SITE_URL.U('Payment/returnUrl',array('pay_code'=>'unionpay')),  //前台通知地址     SITE_URL.U('User/order_detail',array('id'=>$order['order_id']))
            'backUrl' => SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'unionpay')),	  //后台通知地址   SDK_BACK_NOTIFY_URL
            'signMethod' => '01',	              //签名方法
            'channelType' => '08',	              //渠道类型，07-PC，08-手机
            'accessType' => '0',		          //接入类型
            'currencyCode' => '156',	          //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $this->unionpay_config['unionpay_mid'],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $order['sn'],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date('YmdHis',$order['create_time']),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' =>(int)( $order['actually_amount']*100),	//交易金额，单位分，此处默认取demo演示页面传递的参数
            // 		'reqReserved' =>'透传信息',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据

            //TODO 其他特殊用法请查看 special_use_purchase.php
        );
        //建立请求
        //dump(SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'unionpay')));die;
        \AcpService::sign ( $params );

        $uri = SDK_FRONT_TRANS_URL;
        $html_form = \AcpService::createAutoFormHtml( $params, $uri );
        return $html_form;
    }

    /**
     * 服务器点对点响应操作给支付接口方调用
     *
     */
    function response()
    {
        //计算得出通知验证结果
        $unionpayNotify = new \AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($_POST);

        if($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $_POST['orderId']; //商户订单号
            $queryId = $_POST['queryId']; //银联支付流水号
            $respMsg = $_POST['respMsg']; //交易状态

            // 解释: 交易成功且结束，即不可再做任何操作。
            if($_POST['respMsg'] == 'Success!')
            {
                // 修改订单支付状态
            }
            // header("Location:".SITE_URL.U('User/order_detail',array('id'=>$order['order_id'])));
            echo "success"; // 处理成功
        }
        else
        {
            echo "fail"; //验证失败
        }
    }

    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {


        //计算得出通知验证结果
        $unionpayNotify = new \AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($_POST);

        if($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $_POST['orderId']; //商户订单号
            $queryId = $_POST['queryId']; //银联支付流水号
            $respMsg = $_POST['respMsg']; //交易状态

            if($_POST['respMsg'] == 'success')
            {
                return array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面
            }
            else {
                return array('status'=>0,'order_sn'=>$order_sn); //跳转至失败页面
            }
        }
        else
        {
            return array('status'=>0,'order_sn'=>$_POST['orderId']);//跳转至失败页面
        }
    }


}
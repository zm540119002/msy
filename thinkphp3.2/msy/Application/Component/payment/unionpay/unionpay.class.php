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
        unset($_GET['pay_code']);   // 删除掉 以免被进入签名
        unset($_REQUEST['pay_code']);// 删除掉 以免被进入签名
        $paymentPlugin = D('Plugin')->where("code='unionpay' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化

//        $this->unionpay_config['unionpay_mid']           = $config_value['unionpay_mid']; // 商户号
//        $this->unionpay_config['unionpay_cer_password']  = $config_value['unionpay_cer_password'];// 商户私钥证书密码
//        $this->unionpay_config['unionpay_user']          = $config_value['unionpay_user'];//企业网银账号
//        $this->unionpay_config['unionpay_password']	     = $config_value['unionpay_password'];//企业网银密码
        $this->unionpay_config['unionpay_mid']           =  802440072770520;// 商户号
        $this->unionpay_config['unionpay_cer_password']  = 171212;// 商户私钥证书密码
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
        print_r($params);exit;
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


    //退款有密接口接口
    public function payment_refund($data){

        /**
         * 重要：联调测试时请仔细阅读注释！
         *
         * 产品：跳转网关支付产品<br>
         * 交易：退货交易：后台资金类交易，有同步应答和后台通知应答<br>
         * 日期： 2015-09<br>
         * 版本： 1.0.0
         * 版权： 中国银联<br>
         * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考，不提供编码性能规范性等方面的保障<br>
         * 该接口参考文档位置：open.unionpay.com帮助中心 下载  产品接口规范  《网关支付产品接口规范》<br>
         *              《平台接入接口规范-第5部分-附录》（内包含应答码接口规范，全渠道平台银行名称-简码对照表）<br>
         * 测试过程中的如果遇到疑问或问题您可以：1）优先在open平台中查找答案：
         * 							        调试过程中的问题或其他问题请在 https://open.unionpay.com/ajweb/help/faq/list 帮助中心 FAQ 搜索解决方案
         *                             测试过程中产生的7位应答码问题疑问请在https://open.unionpay.com/ajweb/help/respCode/respCodeList 输入应答码搜索解决方案
         *                          2） 咨询在线人工支持： open.unionpay.com注册一个用户并登陆在右上角点击“在线客服”，咨询人工QQ测试支持。
         * 交易说明： 1）以后台通知或交易状态查询交易（Form_6_5_Query）确定交易成功，建议发起查询交易的机制：可查询N次（不超过6次），每次时间间隔2N秒发起,即间隔1，2，4，8，16，32S查询（查询到03，04，05继续查询，否则终止查询）
         *        2）退货金额不超过总金额，可以进行多次退货
         *        3）退货能对11个月内的消费做（包括当清算日），支持部分退货或全额退货，到账时间较长，一般1-10个清算日（多数发卡行5天内，但工行可能会10天），所有银行都支持
         */

        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',		      //版本号
            'encoding' => 'utf-8',		      //编码方式
            'signMethod' => 01,		      //签名方法
            'txnType' => '04',		          //交易类型
            'txnSubType' => '00',		      //交易子类
            'bizType' => '000201',		      //业务类型
            'accessType' => '0',		      //接入类型
            'channelType' => '08',		      //渠道类型
            'backUrl' => SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'unionpay')),	 //后台通知地址

            //TODO 以下信息需要填写
            'orderId' => $data["orderId"],	    //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' => $data["merId"],	        //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
            'origQryId' => $data["origQryId"], //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
            'txnTime' => $data["txnTime"],	    //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'txnAmt' => $data["txnAmt"],       //交易金额，退货总金额需要小于等于原消费

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
        );
        //建立请求
        \AcpService::sign ( $params );
        $uri = SDK_BACK_TRANS_URL;
        $html_form = \AcpService::createAutoFormHtml( $params, $uri );
        return $html_form;
    }


}
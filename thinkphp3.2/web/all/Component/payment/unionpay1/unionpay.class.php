<?php
namespace  web\all\Component\payment\unionpay;
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
        $this->unionpay_config['unionpay_mid']           =  802440072770520;// 商户号
        $this->unionpay_config['unionpay_cer_password']  = 171212;// 商户私钥证书密码
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    function get_code($order, $config_value)
    {
        header ( 'Content-type:text/html;charset=utf-8' );
        require_once(dirname(__FILE__) .'/sdk/acp_service.php');

        /**
         * 重要：联调测试时请仔细阅读注释！
         *
         * 产品：跳转网关支付产品<br>
         * 交易：消费：前台跳转，有前台通知应答和后台通知应答<br>
         * 日期： 2015-09<br>
         * 版本： 1.0.0
         * 版权： 中国银联<br>
         * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考，不提供编码性能规范性等方面的保障<br>
         * 提示：该接口参考文档位置：open.unionpay.com帮助中心 下载  产品接口规范  《网关支付产品接口规范》，<br>
         *              《平台接入接口规范-第5部分-附录》（内包含应答码接口规范，全渠道平台银行名称-简码对照表)<br>
         *              《全渠道平台接入接口规范 第3部分 文件接口》（对账文件格式说明）<br>
         * 测试过程中的如果遇到疑问或问题您可以：1）优先在open平台中查找答案：
         * 							        调试过程中的问题或其他问题请在 https://open.unionpay.com/ajweb/help/faq/list 帮助中心 FAQ 搜索解决方案
         *                             测试过程中产生的7位应答码问题疑问请在https://open.unionpay.com/ajweb/help/respCode/respCodeList 输入应答码搜索解决方案
         *                          2） 咨询在线人工支持： open.unionpay.com注册一个用户并登陆在右上角点击“在线客服”，咨询人工QQ测试支持。
         * 交易说明:1）以后台通知或交易状态查询交易确定交易成功,前台通知不能作为判断成功的标准.
         *       2）交易状态查询交易（Form_6_5_Query）建议调用机制：前台类交易建议间隔（5分、10分、30分、60分、120分）发起交易查询，如果查询到结果成功，则不用再查询。（失败，处理中，查询不到订单均可能为中间状态）。也可以建议商户使用payTimeout（支付超时时间），过了这个时间点查询，得到的结果为最终结果。
         */

        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => \web\all\Component\payment\unionpay1\acp\sdk\SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',				  //编码方式
            'txnType' => '01',				      //交易类型
            'txnSubType' => '01',				  //交易子类
            'bizType' => '000201',				  //业务类型
            'frontUrl' => \web\all\Component\payment\unionpay1\acp\sdk\SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
            'backUrl' => \web\all\Component\payment\unionpay1\acp\sdk\SDKConfig::getSDKConfig()->backUrl,	  //后台通知地址
            'signMethod' => \web\all\Component\payment\unionpay1\acp\sdk\SDKConfig::getSDKConfig()->signMethod,	              //签名方法
            'channelType' => '08',	              //渠道类型，07-PC，08-手机
            'accessType' => '0',		          //接入类型
            'currencyCode' => '156',	          //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $_POST["merId"],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $_POST["orderId"],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $_POST["txnTime"],	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $_POST["txnAmt"],	//交易金额，单位分，此处默认取demo演示页面传递的参数

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

        \web\all\Component\payment\unionpay1\acp\sdk\AcpService::sign ( $params );
        $uri = \web\all\Component\payment\unionpay1\acp\sdk\SDKConfig::getSDKConfig()->frontTransUrl;
        $html_form = \web\all\Component\payment\unionpay1\acp\sdk\AcpService::createAutoFormHtml( $params, $uri );
        echo $html_form;
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
            'signMethod' => '01',		      //签名方法
            'txnType' => '04',		          //交易类型
            'txnSubType' => '00',		      //交易子类
            'bizType' => '000201',		      //业务类型
            'accessType' => '0',		      //接入类型
            'channelType' => '08',		      //渠道类型
            'backUrl' => SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'unionpay')),	 //后台通知地址

            //TODO 以下信息需要填写
            'orderId' => $data["orderId"],	    //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' =>  $this->unionpay_config['unionpay_mid'],	    //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
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
        $url = SDK_BACK_TRANS_URL;
        $result_arr = \AcpService::post( $params, $url );
        if(count($result_arr)<=0) { //没收到200应答的情况
           $this->printResult ( $url, $params, "" );
            return;
        }

        $this->printResult ($url, $params, $result_arr ); //页面打印请求应答数据

        if (!\AcpService::validate ($result_arr) ){
            echo "应答报文验签失败<br>\n";
            return;
        }

        echo "应答报文验签成功<br>\n";
        if ($result_arr["respCode"] == "00"){
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            echo "受理成功。<br>\n";
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05" ){
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            echo "处理超时，请稍微查询。<br>\n";
        } else {
            //其他应答码做以失败处理
            //TODO
            echo "失败：" . $result_arr["respMsg"] . "。<br>\n";
        }
    }


    function printResult($url, $req, $resp) {
        echo "=============<br>\n";
        echo "地址：" . $url . "<br>\n";
        echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities (createLinkString ( $req, false, true ) ) ) . "<br>\n";
        echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities (createLinkString ( $resp , false, false )) ) . "<br>\n";
        echo "=============<br>\n";
    }


}
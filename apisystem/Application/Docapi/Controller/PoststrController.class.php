<?php
// +----------------------------------------------------------------------
// | ApiSystem接口文档管理系统 让沟通更简单
// | Copyright (c) 2015 http://www.apisystem.cn
// | Author: Texren  QQ: 174463651
// |         Smith77 QQ: 3246932472
// | 交流QQ群 577693968 交流QQ群2 460098419
// +----------------------------------------------------------------------

namespace Docapi\Controller;
use Common\Model\TreeViewModel;
use Think\Controller;
use Think\Model;
class PoststrController extends ApisystemController {

    /**
     * Created by PhpStorm.
     * User: Texren  QQ: 174463651
     * Date: 5/16/2004
     * Time: 9:51 PM
     */


//各个公司和项目API安全算法都不同，大家可以根据实际情况修改代码保证startRun()方法返回值就可以了，
//startRun()的返回值实际就是接口的返回值。


//APi appid token秘钥
private $appid = 'xxxx';
private $token = 'xxxx';



function startRun(){
    $params=array();
    if(IS_POST) {
        //获取接口页面提交的字段和测试值
        $key_name=I('key_name');
        $key_value=I('key_value');
        $url=I('url');  //接口全地址
        foreach($key_name as $k =>$v){
            $params[$key_name[$k]]= $key_value[$k];
        }
    }
    //var_dump($params);
    //var_dump($url);
    //appid
    $params['timestamp'] =  time();
    $params['appid'] =  $this->appid;
    //签名加载 //根据情况修改
    $params['sign'] =  $this->get_sign($params,$this->token);


    //var_dump($params);


    // 使用POST方法将上述$params提交至$url即可获取返回值
    $response=$this->send_post($url, $params);
    //$response=$this->post_Curl( $url,$params);
    //var_dump($response);
    //json解析
    $response = json_decode($response, true);
    header("Content-type:text/html;charset=utf-8");
    //echo $response;

    $data=jsonFormat($response);
    $rs=array(
        'status'=>'1',
        'data'=>$data
    );
    echo json_encode($rs);
}



//签名算法-----begin---------------------------------------------------------------------------
    /**
     * 签名算法
     * @param $params
     * @param $token
     * @return string
     */
    function get_sign( $params, $token ) {
        if ( isset( $params['sign'] ) ) unset( $params['sign'] );
        return md5( $token );
    }





//签名算法------end--------------------------------------------------------------------------



    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
    function send_post($url, $post_data) {

        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }


    /**
     * curl发送post请求
     * @param $url
     * @param $post_data
     * @return bool|mixed
     */
    function post_Curl($url,$post_data)
    {
        //初始化curl
        $ch = curl_init();
        //设置超时
        //curl_setopt($ch, CURLOP_TIMEOUT, 30);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        //运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        //返回结果
        if($data)
        {
            return $data;
        }
        else
        {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }





}
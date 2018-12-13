<?php
namespace app\store\controller;

class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){
//        header("Content-type: text/html; charset=gb2312");
//        $client = stream_socket_client('tcp://127.0.0.1:1238', $errno, $errmsg, 5);
//        print_r($errno);
//        print_r($errmsg);
//        exit;
        return $this->fetch();
    }
}
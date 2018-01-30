<?php
namespace Home\Controller;
use Think\Controller;
use web\all\Lib\Express;
class IndexController extends Controller {
    public function index(){
        echo '首页';
    }

    public function a(){
        if(!empty($_POST['code'])){
            //初始化一些必要的数据
            $lCode = addslashes(trim($_POST['code']));
            $prepare = array();
            $prepare['OrderCode'] = 123456;    //订单号
            $prepare['ShipperCode'] = 'EMS';        //商家决定发的快递号编码,默认圆通
            $prepare['LogisticCode'] = $lCode;//查询时的快递单号
            $requestData = json_encode($prepare);  //以json数据接受,该数据作为函数的参数使用
            $express = new Express();
            $logisticResult= $express->getOrderTracesByJson($requestData);
            $result = json_decode($logisticResult,true);
            var_dump($result);
        }else{
            $this ->display();
        }
    }
}
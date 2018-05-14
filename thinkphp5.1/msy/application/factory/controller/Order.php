<?php
/**
 * @author :  Mr.wei
 * @date : 2018-05-07
 * @effect : 厂商的订单管理
 */
namespace app\factory\controller;

class Order extends FactoryBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *订单首页
     *
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 测试合成图片功能
     * @return string
     *
     */
    public function  test()
    {
        return $this->compose();
    }

    /**
     * 售前
     *
     */
    public function beforeSale()
    {
        return $this->fetch('index');
    }

    /**
     * 出仓
     *
     */
    public function out()
    {
        return $this->fetch();
    }

    /**
     * 发货/完成
     *
     */
    public function delivery()
    {
        return $this->fetch('index');
    }

    /**
     * 填单
     *
     */
    public function bill()
    {
        return $this->fetch();
    }

    /**
     *售后
     *
     */
    public function afterSale()
    {
        return $this->fetch('index');
    }

    /**
     *根据订单号查订单详情
     *@param number|string $order_id
     *@param boolean $json
     *@return array|json
     */
    public function  detail($order_id, $json=false)
    {
        $order_id = '201805141234';
        if($json){
            return json(['order_id'=>$order_id, 'detail'=>['订单详情']]);
        }
        return ['order_id'=>$order_id, 'detail'=>['订单详情']];
    }
    

}
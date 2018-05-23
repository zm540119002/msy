<?php
/**
 * @author :  Mr.wei
 * @date : 2018-05-07
 * @effect : 厂商的订单管理
 */
namespace app\factory\controller;

class Order extends StoreBase
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
    public function  getDetail($order_id, $json=false)
    {
        $order_id = '201805141234';
        if($json){
            return json(['order_id'=>$order_id, 'detail'=>['订单详情']]);
        }
        return ['order_id'=>$order_id, 'detail'=>['订单详情']];
    }
    
    /**
     * 填写物流单号
     * @param number|string $order_id 订单号
     * @param number|string $express_id  物流单号
     * @param number $name_id 物流公司代号
     * @return boolean
     */
    public function setExpress($order_id, $express_id, $name_id)
    {
        return true;
    }

    /**
     * 更改订单状态
     * @param number|string $order_id 订单号
     * @param number $status 订单状态
     * @return boolean
     */
    public function setStatus($order_id, $status)
    {
        return true;
    }

    /**
     * 订单商品数据与扫描数据比对
     * @param json $scan 扫描到的商品数据
     * @param number|string $order_id 订单号
     * @return boolean|json|array 比对完全一致或返回其差异数据
     */
    public function compareGoods($order_id, $scan){
        return true;
    }

}
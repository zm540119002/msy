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
     *
     *
     */
    public function index()
    {
        return $this->fetch();
    }
}
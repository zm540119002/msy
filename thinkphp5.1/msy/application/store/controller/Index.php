<?php
namespace app\store\controller;


class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //return dump(config());
        $this->assign('goodsList', $this->store->getGoodsList($this->store_id));
        $this->assign('store_id', $this->store_id);
        return $this->fetch();
    }

    /**
     * 商品购买
     *
     *
     */
    public function buy($goods_id)
    {
        $this->assign('goods', $this->store->getGoods($goods_id));
        $this->assign('store_id', $this->store_id);
        return $this->fetch();
    }

    public  function  hello()
    {
        return $this->fetch();
    }

}

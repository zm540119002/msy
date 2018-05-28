<?php
namespace app\store\controller;

use think\Controller;
use app\store\model\Goods;

class Index extends Controller
{
    private $goods, $store_id;

    public function __construct()
    {
        parent::__construct();
        $this->store_id = input('?store_id')?input('store_id/d', -1, 'intval'):-1;
        if( $this->store_id==-1 ){
            $this->redirect('Nostore/index');
            exit();
        }
        if(!is_object($this->goods)){
            $this->goods = new Goods;
        }
    }
    //店铺首页
    public function index()
    {
        $this->assign( 'goodsList', $this->goods->getList($this->store_id) );
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
        $this->assign('goods', $this->goods->getDetail($this->store_id, $goods_id));
        $this->assign('store_id', $this->store_id);
        return $this->fetch();
    }

    public  function  hello()
    {
        return $this->fetch();
    }

}

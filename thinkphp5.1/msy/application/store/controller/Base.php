<?php
namespace app\store\controller;

use think\Controller;

class Base extends Controller
{
    protected $store_id; //店铺ID
    protected $store; //店铺模型对象

    public function __construct()
    {
        parent::__construct();
        $this->store_id = input('?store_id')?input('store_id/d', -1, 'intval'):-1;
        if(!is_object($this->store)){
            $this->store =new \app\store\model\Store();
        }
        if( $this->store_id==-1 || !$this->store->hasStore($this->store_id) ){
            $this->redirect('Nostore/index');
            exit();
        }
    }

}

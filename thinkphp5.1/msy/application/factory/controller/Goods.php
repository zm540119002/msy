<?php
namespace app\factory\controller;
use app\factory\model\Goods as M;
use common\controller\Base;
class Goods extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        if(request()->isPost()){
            $model = new M();
            return $model -> add();
        }
        return $this->fetch();
    }

}
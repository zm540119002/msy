<?php
namespace app\factory\controller;
use app\factory\model\Factory as M;
use common\controller\UserBase;

class Index extends UserBase
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**入驻登记
     */
    public function register()
    {
        if(request()->isPost()){
            $model = new M();
            return $model -> add();
        }
        return $this->fetch();
    }
}
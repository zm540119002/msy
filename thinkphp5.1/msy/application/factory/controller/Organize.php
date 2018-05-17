<?php
namespace app\factory\controller;

class Organize extends FactoryBase
{
    /**首页
     */
    public function index()
    {
        $modelOrganize = new \app\factory\model\Organize();
        if(request()->isAjax()){
            return $modelOrganize->edit($this->factory['id']);
        }else{
            return $this->fetch();
        }
    }

    /**
     */
    public function  test()
    {
        if(request()->isAjax()){
            return input('post.');
        }else{
            return $this->fetch();
        }
    }
}
<?php
namespace app\factory\controller;

use think\Controller;

class Index extends Controller
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
        if(request()->isAjax()){
            $model = new \app\factory\model\Factory();
            return $model -> add();
        }else{
            return $this->fetch();
        }
    }
}
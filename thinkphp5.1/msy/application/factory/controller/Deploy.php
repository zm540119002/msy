<?php
namespace app\factory\controller;

use common\controller\UserBase;

class Deploy extends UserBase
{
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
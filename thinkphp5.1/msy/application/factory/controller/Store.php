<?php
namespace app\factory\controller;

use common\controller\UserBase;

class Store extends UserBase
{
    /**
     * 店铺管理
     */
    public function manage()
    {
        $model = new \app\factory\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this -> user['id']);
        }else{
            return $this->fetch();
        }
    }

    /**
     * 店铺管理
     */
    public function edit()
    {
        $model = new \app\factory\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this -> user['id']);
        }else{
            return $this->fetch();
        }
    }

    //设置默认产商
    public function setDefaultFactory(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Store();
            return $model->setDefaultFactory($this->user['id']);
        }
    }
}
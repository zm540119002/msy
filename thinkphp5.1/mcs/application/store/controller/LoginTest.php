<?php
namespace app\store\controller;

class LoginTest extends \common\controller\FactoryStoreBase
{
    public function idnex(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}
<?php
namespace app\store\controller;

class PageLogin extends \common\controller\Base
{
    public function idnex(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}
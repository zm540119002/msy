<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
class Deploy extends UserBase
{
    /**入驻登记
     */
    public function register()
    {
        $model = new \app\factory\model\Factory();
        if(request()->isAjax()){
            return $model -> edit($this -> user['id']);
        }else{
            $mobilePhone = $this -> user['mobile_phone'];
            $this->assign('mobilePhone',$mobilePhone);
            if(input('?factory_id')){
                $factoryId = input('factory_id');
                $where = [
                    ['id','=',$factoryId],
                ];
                $factoryInfo =  $model -> getFactory($where);
                $this -> assign('factoryInfo',$factoryInfo);
            }
            return $this->fetch();
        }
    }

    //设置默认产商
    public function setDefaultFactory(){
        if(request()->isAjax()){
            $model = new \app\factory\model\FactoryUser();
            return $model->setDefaultFactory($this->user['id']);
        }
    }
}
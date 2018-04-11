<?php
namespace app\factory\controller;

use common\controller\UserBase;
//use common\controller\Base;

class Deploy extends UserBase
{
    /**入驻登记
     */
    public function register()
    {
        $model = new \app\factory\model\Factory();
        $uid = $this -> user['id'];
        if(request()->isAjax()){
            if(input('?post.factory_id')){
                return $model -> edit($uid);
            }else{
                return $model -> add($uid);
            }
        }else{
            $mobilePhone = $this->user['mobile_phone'];
            $this->assign('mobilePhone',$mobilePhone);
            if(input('?factory_id')){
                $factoryId = input('factory_id');
                $where = array(
                    'id' => $factoryId,
                );
                $factoryInfo =  $model -> getFactory($where);
                $this -> assign('factoryInfo',$factoryInfo);
            }
            return $this->fetch();
        }
    }

    //产商档案编辑
    public function recordEdit(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Factory();
            return $model -> add();
        }else{
            return $this->fetch();
        }
    }
}
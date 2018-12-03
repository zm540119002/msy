<?php
namespace app\store\controller;
use common\controller\UserBase;
use think\facade\Session;
class Deploy extends UserBase
{
    /**入驻登记
     */
    public function register()
    {
        $model = new \common\model\Factory();
        if(request()->isAjax()){
            return $model -> edit($this -> user['id'],config('custom.type'));
        }else{
            $mobilePhone = $this -> user['mobile_phone'];
            $this->assign('mobilePhone',$mobilePhone);
            if(input('?factory_id')){
                $factoryId = input('factory_id');
                $config = [
                    'where' => [
                        ['id','=',$factoryId],
                        ['type','=',2],//美容店家类型
                    ],
                ];
                $factoryInfo =  $model -> getInfo($config);
                $this -> assign('factoryInfo',$factoryInfo);
            }
            return $this->fetch();
        }
    }
}
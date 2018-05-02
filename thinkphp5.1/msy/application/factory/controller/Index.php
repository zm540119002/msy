<?php
namespace app\factory\controller;
use common\controller\UserBase;

class Index extends UserBase
{
    /**首页
     */
    public function index()
    {
        $model = new \app\factory\model\FactoryUser();
        $uid = $this -> user['id'];
        $where = [ ['user_id','=',$uid] ];
        $factoryCount = $model -> where($where)->count('id');
        $this -> assign('factoryCount',$factoryCount);
        $file = [
            'u.id,u.factory_id,f.name'
        ];
        $join =[
            ['factory f','f.id = u.factory_id'],
        ];
        if($factoryCount > 1){
            $_where = [
              ['user_id','=',$uid],
              ['is_default','=',1],
            ];
            $factoryInfo = $model -> getFactoryUser($_where,$file,$join);
            $factoryList = $model -> selectFactoryUser($where,$file,$join);
            $this -> assign('factoryList',$factoryList);
            if(!$factoryInfo){
                $this -> assign('notDefaultFactory',1);
            }
            $this -> assign('factoryInfo',$factoryInfo);
        }elseif ($factoryCount == 1){
            $factoryInfo = $model -> getFactoryUser($where,$file,$join);
            $this -> assign('factoryInfo',$factoryInfo);
        }
        return $this->fetch();
    }
}
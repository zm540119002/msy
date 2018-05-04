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
        $where_new = [ ['u.user_id','=',$uid] ];
        if($factoryCount > 1){
            $_where = [
              ['u.user_id','=',$uid],
              ['u.is_default','=',1],
            ];
            $factoryInfo = $model -> getFactoryUser($_where,$file,$join);

            $factoryList = $model -> selectFactoryUser($where_new,$file,$join);
            $this -> assign('factoryList',$factoryList);
            if(!$factoryInfo){
                $this -> assign('notDefaultFactory',1);
            }
            $this -> assign('factoryInfo',$factoryInfo);
        }elseif ($factoryCount == 1){
            $factoryInfo = $model -> getFactoryUser($where_new,$file,$join);
            $this -> assign('factoryInfo',$factoryInfo);
        }
        return $this->fetch();
    }
}
<?php
namespace app\factory\controller;
use common\controller\UserBase;

class Index extends UserBase
{
    /**首页
     */
    public function index()
    {
        $model = new \app\factory\model\Factory();
        $uid = $this -> user['id'];
        $where = array(
            'user_id' => $uid,
        );
        $file = array(
            'id','name',
        );
        $factoryCount = $model -> where($where)->count('id');
        $this -> assign('factoryCount',$factoryCount);
        if($factoryCount > 1){
            $_where = [
              ['user_id','=',$uid],
              ['is_default','=',1],
            ];
            $factoryInfo = $model -> getFactory($_where,$file);
            $factoryList = $model -> selectFactory($where,$file);
            $this -> assign('factoryList',$factoryList);
            if(!$factoryInfo){
                $this -> assign('notDefaultFactory',1);
            }
            $this -> assign('factoryInfo',$factoryInfo);
        }elseif ($factoryCount == 1){
            $factoryInfo = $model -> getFactory($where,$file);
            $this -> assign('factoryInfo',$factoryInfo);
        }

        return $this->fetch();
    }
}
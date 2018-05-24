<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;
    
    public function __construct(){
        parent::__construct();
        $factoryInfo = Session::get('factory');
        if(empty($factoryInfo)){
            $factoryInfo = $this ->_getFactory();
        }
        $this -> factory =  $factoryInfo;
    }

    private function _getFactory(){
        $model = new \app\factory\model\UserFactory();
        $uid = $this -> user['id'];
        $where = [
            ['user_id','=',$uid],
        ];
        $factoryCount = $model -> where($where)->count('id');
        $file = [
            'u.id,u.factory_id,u.is_default,f.name'
        ];
        $join =[
            ['factory f','f.id = u.factory_id'],
        ];
        if($factoryCount > 1){
            $_where = [
                ['u.user_id','=',$uid],
                ['u.is_default','=',1],
            ];
            $factoryInfo = $model -> getUserFactory($_where,$file,$join);
            if(!$factoryInfo){
                $this->success('你有多家厂商入住，请选择一家', 'Index/index');
            }
        }elseif ($factoryCount == 1){
            $where_new = [
                ['u.user_id','=',$uid],
            ];
            $factoryInfo = $model -> getUserFactory($where_new,$file,$join);
        }elseif (!$factoryCount){
            $this->success('没有产商入住，请入住', 'Deploy/register');
        }
        Session::set('factory',$factoryInfo);
        return  Session::get('factory');
    }
}
<?php
namespace app\factory\controller;
use common\controller\UserBase;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;

    public function __construct(){
        parent::__construct();
        $model = new \app\factory\model\Factory();
        $uid = $this -> user['id'];
        $where = [
            ['user_id','=',$uid],
        ];

        $factoryCount = $model -> where($where)->count('id');
        if($factoryCount > 1){
            $_where = [
                ['user_id','=',$uid],
                ['is_default','=',1],
            ];
            $factoryInfo = $model -> getFactory($_where);
            if(!$factoryInfo){
                $this->success('请选择多家产商入住，请选择一家', 'Index/index');;
            }
        }elseif ($factoryCount == 1){
            $factoryInfo = $model -> getFactory($where);
        }elseif (!$factoryCount){
            $this->success('没有产商入住，请入住', 'Deploy/register');

        }
        $this->factory =  $factoryInfo;
    }
}
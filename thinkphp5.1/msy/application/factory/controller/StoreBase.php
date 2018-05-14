<?php
namespace app\factory\controller;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class StoreBase extends FactoryBase{
    protected $store = null;
    public function __construct(){
        parent::__construct();
        $factoryInfo = Session::get('factory');;
        if(empty($factoryInfo)){
            $factoryInfo = $this ->getStore();
        }
        $this->factory =  $factoryInfo;
    }

    public function getStore(){
        $model = new \app\factory\model\Store();
        $where = [
            ['factory_id','=',$this->factory['factory_id']],
        ];
        $factoryCount = $model -> where($where)->count('id');
        $file = [
            'u.id,u.factory_id,u.is_default,f.name'
        ];
        if($factoryCount > 1){
            $_where = [
                ['factory_id','=',$this->factory['factory_id']],
                ['u.is_default','=',1],
            ];
            $factoryInfo = $model -> getStore($_where,$file);
            if(!$factoryInfo){
                $this->success('你有多家厂商入住，请选择一家', 'Index/index');;
            }
        }elseif ($factoryCount == 1){
            $where_new = [
                ['factory_id','=',$this->factory['factory_id']],
            ];
            $factoryInfo = $model -> getStore($where_new,$file);
        }elseif (!$factoryCount){
            $this->success('没有产商入住，请入住', 'Deploy/register');
        }
        $factoryInfo = array_merge($factoryInfo,array('rand' => create_random_str(10, 0),));
        Session::set('factory',$factoryInfo);
        return  Session::get('factory');
    }
}
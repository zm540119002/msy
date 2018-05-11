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
            $factoryInfo = $this ->_getStore();
        }
        $this->factory =  $factoryInfo;
    }

    private function _getStore(){
        $model = new \app\factory\model\Store();
        $where = [
            ['factory_id','=',$this -> factory['factory_id']],
        ];
        $factoryCount = $model -> where($where)->count('id');
        $file = [
            's.id,s.factory_id,s.is_default,s.name'
        ];

        Session::set('factory',$factoryInfo);
        return  Session::get('factory');
    }
}
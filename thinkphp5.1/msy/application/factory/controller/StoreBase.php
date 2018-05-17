<?php
namespace app\factory\controller;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class StoreBase extends FactoryBase{
    protected $store = null;
    public function __construct(){
        parent::__construct();
        $storeInfo = Session::get('store');;
        if(empty($storeInfo)){
            $storeInfo = $this ->getStore();
        }
        $this->store =  $storeInfo;
    }

    public function getStore(){
        $model = new \app\factory\model\Store();
        $where = [
            ['factory_id','=',$this->factory['factory_id']],
        ];
        $storeCount = $model -> where($where)->count('id');
        $file = [
            's.id,s.factory_id,s.is_default,s.store_type,run_type,auth_status'
        ];
        if($storeCount > 1){
            $_where = [
                ['s.factory_id','=',$this->factory['factory_id']],
                ['s.is_default','=',1],
            ];
            $storeInfo = $model -> getStore($_where,$file);
            if(!$storeInfo){
                $this->success('你有多家店，请选择一家', 'Store/operaManageIndex');;
            }
        }elseif ($storeCount == 1){
            $where_new = [
                ['factory_id','=',$this->factory['factory_id']],
            ];
            $storeInfo = $model -> getStore($where_new,$file);

        }elseif (!$storeCount){
            $this->success('没有店铺，请申请', 'Store/deployIndex');
        }
        $storeInfo = array_merge($storeInfo,array('rand' => create_random_str(10, 0),));
        Session::set('store',$storeInfo);
        return  Session::get('store');
    }
}
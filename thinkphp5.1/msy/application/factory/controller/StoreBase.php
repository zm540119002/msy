<?php
namespace app\factory\controller;
/**
 * Class StoreBase
 * @package app\factory\controller
 * 店铺基础类
 */
class StoreBase extends FactoryBase
{
    protected $store = null;
    public function __construct()
    {
        parent::__construct();
        $model = new \app\factory\model\Store();
        //获取厂商店铺详情列表
        \common\cache\Store::removeList($this->factory['id']);
        $storeList = \common\cache\Store::getList($this->factory['id']);
        $this -> assign('storeList', $storeList);
        $where = [
            ['factory_id', '=', $this->factory['id']],
        ];
<<<<<<< HEAD
        $list = $model->getList($where);
        $count = count($list);
        if ($count > 1) {
            //多家店判断是否有默认店铺
            $where[] = ['is_default', '=', 1];
            $list = $model -> getList($where);
            $countDefault = count($list);
            if (!$countDefault) {

                $this->success('你有多家店，请选择一家', 'Store/operaManageIndex');;
=======
        $storeCount = $model -> where($where)->count('id');
        $file = [
            's.id,s.factory_id,s.is_default,s.store_type,run_type,auth_status'
        ];
        if($storeCount > 1){
            $_where = [
                ['s.factory_id','=',$this->factory['factory_id']],
                ['s.is_default','=',1],
            ];
            $storeInfo = $model -> getInfo($_where,$file);
            if(!$storeInfo){
                $this -> assign('notDefaultStore',1);
                $this->success('你有多家店，请选择一家', 'Store/operaManageIndex');
>>>>>>> 46f893db6dd9beb061cfce55dab45953de4df793
            }
        } elseif (!$count) {
            $this->success('没有店铺，请申请', 'Store/deployIndex');
        }
        $info = $list[0];
        \common\cache\Store::remove($info['id']);
        $this->store = \common\cache\Store::get($info['id']);
    }
}
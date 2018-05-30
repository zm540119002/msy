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
        $list = $model -> getList($where);
        $count = count($list);
        if ($count > 1) {
            //多家店判断是否有默认店铺
            $where[] = ['is_default', '=', 1];
            $list = $model -> getList($where);
            $countDefault = count($list);
            if (!$countDefault) {
                $this -> assign('notDefaultStore', 1);
                $this -> success('你有多家店，请选择一家', 'Store/operaManageIndex');;
            }
        } elseif (!$count) {
            $this -> success('没有店铺，请申请', 'Store/deployIndex');
        }
        $info = $list[0];
        \common\cache\Store::remove($info['id']);
        $this -> store = \common\cache\Store::get($info['id']);
    }
}
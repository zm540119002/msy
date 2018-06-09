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
        if($this->factory){
            //获取厂商店铺详情列表
            \common\cache\Store::removeList($this->factory['id']);
            $list = \common\cache\Store::getList($this->factory['id']);
            $count = count($list);
            if ($count > 1) {
                //多家店判断是否有默认店铺
                $info = [];
                foreach ($list as $val){
                    if($val['is_default']){
                        $info = $val;
                    }
                }
                if (empty($info)) {
                    $this -> assign('notDefaultStore', 1);
                }
            } elseif ($count == 1){
                $info = $list[0];
            }elseif (!$count) {
                $this -> success('没有店铺，请申请', 'Store/edit');
            }
            $this -> assign('storeList', $list);
            \common\cache\Store::remove($info['id']);
            $this -> store = \common\cache\Store::get($info['id']);
        }
    }

    //设置默认产商
    public function setDefaultStore(){
        $model = new \app\factory\model\Store();
        return $model->setDefaultStore($this->factory['id']);
    }

    //获取店铺信息
    public function getStoreInfo(){
        $modelStore = new \app\factory\model\Store;
        return $storeInfo = $modelStore -> getStoreInfo($this->store);
    }
}
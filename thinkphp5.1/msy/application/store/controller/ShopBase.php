<?php
namespace app\store\controller;
/**
 * Class StoreBase
 * @package app\store\controller
 * 店铺基础类
 */
class ShopBase extends StoreBase
{
    protected $shop = null;
    public function __construct()
    {
        parent::__construct();
        if($this->store){
            //获取厂商店铺详情列表
            \common\cache\Store::removeList($this->store['id']);
            $list = \common\cache\Store::getList($this->store['id']);
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
            $this -> shop = \common\cache\Store::get($info['id']);
        }
    }

    //设置默认产商
    public function setDefaultStore(){
        $model = new \app\store\model\Shop();
        return $model->setDefaultStore($this->store['id']);
    }

    //获取店铺信息
    public function getStoreInfo(){
        $modelStore = new \app\store\model\Store;
        return $shopInfo = $modelStore -> getStoreInfo($this->store);
    }
}
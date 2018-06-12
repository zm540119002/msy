<?php
namespace app\store\controller;

/**供应商验证控制器基类
 */
class StoreBase extends \common\controller\UserBase{
    protected $store = null;

    public function __construct(){
        parent::__construct();
        \common\cache\Store::remove($this->user['id']);
        $storeList = \common\cache\Store::get($this->user['id']);
        $this->assign('storeList',$storeList);
        $storeCount = count($storeList);
        if ($storeCount==0){//没有入住供应商
            $this->error('没有入驻店家，请入住', 'Deploy/register');
        }elseif($storeCount==1){//入住一家供应商
            $this->store = $storeList[0];
        }elseif($storeCount>1){//入住多家供应商
            foreach ($storeList as $store){
                if($store['is_default']){//默认供应商
                    $this->store = $store;
                    break;
                }
            }
        }
        if(!$this->store){
            //获取用户-工厂-角色-权限节点
            $nodeList = getUserStoreRoleNode($this->user['id'],$this->store['id']);
            $this->assign('nodeIds',array_column($nodeList,'node_id'));
        }
        $this->assign('store',$this->store);
    }

    //设置默认供应商
    public function setDefaultStore(){
        $modelUserStore = new \app\store\model\UserStore();
        return $modelUserStore->setDefaultStore($this->user['id']);
    }

    //获取厂家详细信息
    public function getStoreInfo(){
        $model = new \app\store\model\Store();
        $where = [
            ['s.id','=',$this->store['id']],
        ];
        $file = [
            's.id,s.name,r.logo_img'
        ];
        $join =[
            ['record r','r.store_id = s.id',],
        ];
        return $model->getInfo($where,$file,$join);
    }
}
<?php
namespace app\shop\controller;

/**供应商验证控制器基类
 */
class ShopBase extends \common\controller\UserBase{
    protected $shop = null;

    public function __construct(){
        parent::__construct();
        \common\cache\Shop::remove($this->user['id']);
        $shopList = \common\cache\Shop::get($this->user['id']);
        $this->assign('shopList',$shopList);
        $shopCount = count($shopList);
        if ($shopCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($shopCount==1){//入住一家供应商
            $this->shop = $shopList[0];
        }elseif($shopCount>1){//入住多家供应商
            foreach ($shopList as $shop){
                if($shop['is_default']){//默认供应商
                    $this->shop = $shop;
                    break;
                }
            }
        }
        if(!$this->shop){
            //获取用户-工厂-角色-权限节点
            $nodeList = getUserShopRoleNode($this->user['id'],$this->shop['id']);
            $this->assign('nodeIds',array_column($nodeList,'node_id'));
        }
        $this->assign('shop',$this->shop);
    }

    //设置默认供应商
    public function setDefaultShop(){
        $modelUserShop = new \app\shop\model\UserShop();
        return $modelUserShop->setDefaultShop($this->user['id']);
    }

    //获取厂家详细信息
    public function getShopInfo(){
        $model = new \app\shop\model\Shop();
        $where = [
            ['f.id','=',$this->shop['id']],
        ];
        $file = [
            'f.id,f.name,r.logo_img'
        ];
        $join =[
            ['record r','r.shop_id = f.id',],
        ];
        return $model->getInfo($where,$file,$join);
    }
}
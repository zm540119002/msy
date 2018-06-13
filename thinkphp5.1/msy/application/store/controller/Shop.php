<?php
namespace app\store\controller;
class Shop extends StoreBase
{

    //开店部署首页
    public function index(){
        return $this->fetch('index');
    }
    /**
     * 店铺管理
     */
    public function manage()
    {
        $model = new \app\store\model\Shop();
        $shopList =  $model -> getShopList($this -> store['id']);
        $this -> assign('shopList',$shopList);
        return $this->fetch();
    }
    /**
     * 店铺管理
     */
    public function edit()
    {
        $model = new \app\store\model\Shop();
        if(request()->isAjax()){
            return $model -> edit($this -> store['id']);
        }else{
            // 企业旗舰店
            $modelStore= new \app\store\model\Store();
            $where = [
                ['s.id','=',$this->store['id']]
            ];
            $file = ['s.id,s.name,r.logo_img as img'];
            $join =[
                ['record r','s.id = r.store_id'],
            ];
            $storeShop =  $modelStore -> getInfo($where,$file,$join);
            $this -> assign('storeShop',$storeShop);
            //企业品牌旗舰店名
            $modelShop = new \app\store\model\Brand();
            $where = [
                ['b.store_id','=',$this->store['id']]
            ];
            $file = ['b.id,b.name,b.brand_img as img'];
            $brandShops =  $modelShop -> getList($where,$file);
            $this -> assign('brandShops',$brandShops);
            //查看已申请的店铺
            $modeShop = new \app\store\model\Shop();
            $file = ['sh.shop_type, sh.run_type, sh.foreign_id'];
            $where = [
                ['sh.store_id','=',$this->store['id']]
            ];
            $shopsApplied = $modeShop->getList($where,$file);
            $this -> assign('shopsApplied',$shopsApplied);
            return $this->fetch();
        }
    }
    //设置店铺运营状态
    public function setShopStatus(){
        if(request()->isAjax()){
            $model = new \app\store\model\Shop();
            return $model->edit($this -> store['id']);
        }
    }
    
}
<?php
namespace app\store\controller;
class Store extends storeBase
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
        $ShopList =  $model -> getShopList($this -> shop['id']);
        $this -> assign('ShopList',$ShopList);
        return $this->fetch();
    }
    /**
     * 店铺管理
     */
    public function edit()
    {
        $model = new \app\store\model\Shop();
        if(request()->isAjax()){
            return $model -> edit($this -> shop['id']);
        }else{
            // 企业旗舰店
            $modelShop = new \app\store\model\Shop();
            $where = [
                ['f.id','=',$this->shop['id']]
            ];
            $file = ['f.id,f.name,r.logo_img as img'];
            $join =[
                ['record r','f.id = r.Shop_id'],
            ];
            $ShopShop =  $modelShop -> getInfo($where,$file,$join);
            $this -> assign('ShopShop',$ShopShop);
            //企业品牌旗舰店名
            $modelShop = new \app\store\model\Brand();
            $where = [['b.Shop_id','=',$this->shop['id']]];
            $file = ['b.id,b.name,b.brand_img as img'];
            $brandShops =  $modelShop -> getList($where,$file);
            $this -> assign('brandShops',$brandShops);
            //查看已申请的店铺
            $modeShop = new \app\store\model\Shop();
            $where = [
                ['s.Shop_id','=',$this->shop['id']]
            ];
            $ShopsApplied = $modeShop->getList($where);
            $this -> assign('ShopsApplied',$ShopsApplied);
            return $this->fetch();
        }
    }
    //设置店铺运营状态
    public function setShopStatus(){
        if(request()->isAjax()){
            $model = new \app\store\model\Shop();
            return $model->edit($this -> shop['id']);
        }
    }
    
}
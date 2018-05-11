<?php
namespace app\factory\controller;
use think\facade\Session;
class Store extends FactoryBase
{
    /**
     * 店铺管理
     */
    public function manage()
    {
        $model = new \app\factory\model\Store();
        //企业旗舰店
        $where = [
            ['s.factory_id','=',$this->factory['factory_id']],
            ['s.store_type','=',1],
        ];
        $file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,f.name,r.logo_img as img'];
        $join =[
            ['factory f','f.id = s.foreign_id'],
            ['record r','s.foreign_id = r.factory_id'],
        ];
        $factoryStore = $model -> selectStore($where,$file,$join);
        $this -> assign('factoryStore',$factoryStore);
        //品牌旗舰店
        $where = [
            ['s.factory_id','=',$this->factory['factory_id']],
            ['s.store_type','=',2],
        ];
        $file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,b.name,b.brand_img as img'];
        $join =[
            ['brand b','b.id = s.foreign_id'],
        ];
        $brandStores = $model->selectStore($where,$file,$join);
        $storeList = array_merge($factoryStore,$brandStores);
        $this -> assign('storeList',$storeList);
        return $this->fetch();
    }
    /**
     * 店铺管理
     */
    public function edit()
    {
        $model = new \app\factory\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this -> factory['factory_id']);
        }else{
            // 企业旗舰店
            $modelFactory = new \app\factory\model\Factory();
            $where = [['f.id','=',$this->factory['factory_id']]];
            $file = ['f.id,f.name,r.logo_img as img'];
            $join =[
                ['record r','f.id = r.factory_id'],
            ];
            $factoryStore =  $modelFactory -> getFactory($where,$file,$join);
            $this -> assign('factoryStore',$factoryStore);
            //企业品牌旗舰店名
            $modelFactory = new \app\factory\model\Brand();
            $where = [['b.factory_id','=',$this->factory['factory_id']]];
            $file = ['b.id,b.name,b.brand_img as img'];
            $brandStores =  $modelFactory -> selectBrand($where,$file);
            $this -> assign('brandStores',$brandStores);
            //查看已申请的店铺
            $modeStore = new \app\factory\model\Store();
            $where = [['s.factory_id','=',$this->factory['factory_id']]];
            $storesApplied = $modeStore->selectStore($where);
            $this -> assign('storesApplied',$storesApplied);
            return $this->fetch();
        }
    }
    //设置店铺运营状态
    public function setStoreStatus(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Store();
            return $model->edit($this -> factory['factory_id']);
        }
    }
    //设置默认产商
    public function setDefaultStore(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Store();
            return $model->setDefaultStore($this->factory['factory_id']);
        }
    }

    //运营管理首页
    public function operaManageIndex(){
        $model = new \app\factory\model\Store();
        $where = [ ['s.factory_id','=',$this->factory['factory_id']] ];
        $storeCount = $model -> where('factory_id','=',$this->factory['factory_id'])->count('id');
        $this -> assign('storeCount',$storeCount);
        if($storeCount > 1){
            $_where = [
                ['u.factory_id','=',$this->factory['factory_id']],
                ['u.is_default','=',1],
            ];
            $storeInfo = $model -> getStore($_where);
            $storeList = $model -> selectStore($where);
            $this -> assign('factoryList',$storeList);
            if(!$storeInfo){
                $this -> assign('notDefaultStore',1);
            }
            $this -> assign('storeInfo',$storeInfo);
        }elseif ($storeCount == 1){
            $storeInfo = $model -> getStore($where);
            $this -> assign('storeInfo',$storeInfo);
        }else{
            
        }
        Session::set('store',$storeInfo);
        return $this->fetch();
    }
}
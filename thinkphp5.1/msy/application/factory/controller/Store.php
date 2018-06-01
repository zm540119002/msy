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
        $storeList =  $model -> getStoreList($this -> factory['id']);
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
            return $model -> edit($this -> factory['id']);
        }else{
            // 企业旗舰店
            $modelFactory = new \app\factory\model\Factory();
            $where = [['f.id','=',$this->factory['id']]];
            $file = ['f.id,f.name,r.logo_img as img'];
            $join =[
                ['record r','f.id = r.factory_id'],
            ];
            $factoryStore =  $modelFactory -> getInfo($where,$file,$join);
            $this -> assign('factoryStore',$factoryStore);
            //企业品牌旗舰店名
            $modelFactory = new \app\factory\model\Brand();
            $where = [['b.factory_id','=',$this->factory['id']]];
            $file = ['b.id,b.name,b.brand_img as img'];
            $brandStores =  $modelFactory -> getList($where,$file);
            $this -> assign('brandStores',$brandStores);
            //查看已申请的店铺
            $modeStore = new \app\factory\model\Store();
            $where = [['s.factory_id','=',$this->factory['id']]];
            $storesApplied = $modeStore->getList($where);
            $this -> assign('storesApplied',$storesApplied);
            return $this->fetch();
        }
    }
    //设置店铺运营状态
    public function setStoreStatus(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Store();
            return $model->edit($this -> factory['id']);
        }
    }

   

    //开店部署首页
    public function deployIndex(){
        $this ->assign('factoryId', $this->factory['id']);
        return $this->fetch('deploy/index');
    }

    //运营管理首页
    public function operaManageIndex(){
        $model = new \app\factory\model\Store();
        $storeCount = $model -> where('factory_id','=',$this -> factory['id']) -> count('id');
        $this -> assign('storeCount',$storeCount);
        if($storeCount > 1){
            $_where = [
                ['s.factory_id','=',$this->factory['id']],
                ['s.is_default','=',1],
            ];
            $storeInfo = $model -> getInfo($_where);
            $storeList =  $model -> getStoreList($this -> factory['id']);
            $this -> assign('storeList',$storeList);
            if(!$storeInfo){
                $this -> assign('notDefaultStore',1);
            }
            $this -> assign('storeInfo',$storeInfo);
        }elseif ($storeCount == 1){
            $where = [ ['s.factory_id','=',$this->factory['id']] ];
            $storeInfo = $model -> getInfo($where);
            $this -> assign('storeInfo',$storeInfo);
        }else{
            $this -> assign('noStore',1);
        }
        Session::set('store',$storeInfo);
        return $this->fetch();
    }
}
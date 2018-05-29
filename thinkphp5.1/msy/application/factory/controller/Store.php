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
        $storeList =  $model -> getStoreList($this -> factory['factory_id']);
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
            $factoryStore =  $modelFactory -> getInfo($where,$file,$join);
            $this -> assign('factoryStore',$factoryStore);
            //企业品牌旗舰店名
            $modelFactory = new \app\factory\model\Brand();
            $where = [['b.factory_id','=',$this->factory['factory_id']]];
            $file = ['b.id,b.name,b.brand_img as img'];
            $brandStores =  $modelFactory -> getList($where,$file);
            $this -> assign('brandStores',$brandStores);
            //查看已申请的店铺
            $modeStore = new \app\factory\model\Store();
            $where = [['s.factory_id','=',$this->factory['factory_id']]];
            $storesApplied = $modeStore->getList($where);
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

    //开店部署首页
    public function deployIndex(){
        $this ->assign('factoryId', $this->factory['factory_id']);
        return $this->fetch('deploy/index');
    }

    //运营管理首页
    public function operaManageIndex(){
        $model = new \app\factory\model\Store();
        $storeCount = $model -> where('factory_id','=',$this -> factory['factory_id']) -> count('id');
        $this -> assign('storeCount',$storeCount);
        if($storeCount > 1){
            $_where = [
                ['s.factory_id','=',$this->factory['factory_id']],
                ['s.is_default','=',1],
            ];
            $storeInfo = $model -> getInfo($_where);
            $storeList =  $model -> getStoreList($this -> factory['factory_id']);
            $this -> assign('storeList',$storeList);
            if(!$storeInfo){
                $this -> assign('notDefaultStore',1);
            }
            $this -> assign('storeInfo',$storeInfo);
        }elseif ($storeCount == 1){
            $where = [ ['s.factory_id','=',$this->factory['factory_id']] ];
            $storeInfo = $model -> getInfo($where);
            $this -> assign('storeInfo',$storeInfo);
        }else{
            $this -> assign('noStore',1);
        }
        Session::set('store',$storeInfo);
        return $this->fetch();
    }
}
<?php
namespace app\factory\controller;

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
        $file = ['f.id,f.name,r.logo_img as img'];
        $join =[
            ['record r','f.id = r.factory_id'],
        ];
        $factoryStore = $model -> selectStore($where);
        //品牌旗舰店
        $where = [
            ['s.factory_id','=',$this->factory['factory_id']],
            ['s.store_type','=',2],
        ];
        $file = ['f.id,f.name,r.logo_img as img'];
        $join =[
            ['record r','f.id = r.factory_id'],
        ];
        $brandStore = $model->selectStore($where);
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
            return $this->fetch();
        }
    }

    //设置默认产商
    public function setDefaultStore(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Store();
            return $model->setDefaultFactory($this->user['id']);
        }
    }
}
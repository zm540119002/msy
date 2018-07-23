<?php
namespace app\factory\controller;
class Store extends FactoryBase
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
        $model = new \app\factory\model\Store();

        $config = [
            'where' => [
                ['factory_id','=',$this -> factory['id']],
            ]
        ];
        $storeList =  $model -> getList($config);
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
            $config = [
                'where' => [
                    ['f.id','=',$this->factory['id']]
                ],'join' => [
                    ['record r','f.id = r.factory_id'],
                ],'field' =>  ['f.id,f.name,r.logo_img as img']
            ];
            $factoryStore =  $modelFactory -> getInfo($config);
            if(empty($factoryStore)){
                $this -> error('请完善档案资料,再申请开店',url('Record/edit'));
            }
            $this -> assign('factoryStore',$factoryStore);
            //企业品牌旗舰店名
            $modelFactory = new \app\factory\model\Brand();
            $config = [
                'where' => [
                    ['b.factory_id','=',$this->factory['id']]
                ],'field' => [
                    'b.id,b.name,b.brand_img as img',
                ],
            ];
            $brandStores =  $modelFactory -> getList($config);
            $this -> assign('brandStores',$brandStores);
            //查看已申请的店铺
            $modeStore = new \app\factory\model\Store();
            $config = [
                'where' => [
                    ['s.factory_id','=',$this->factory['id']]
                ],
            ];
            $storesApplied = $modeStore->getList($config);
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
    
}
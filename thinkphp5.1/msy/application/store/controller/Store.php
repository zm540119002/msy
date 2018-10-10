<?php
namespace app\store\controller;
class Store extends \common\controller\FactoryBase{
    //开店部署首页
    public function index(){
        return $this->fetch('index');
    }

    /**店铺管理
     */
    public function manage(){
        $model = new \common\model\Store();
        
        $config = [
            'where' => [
                ['s.factory_id','=',$this->factory['id']],
            ],'join' => [
                ['record r','r.id = s.foreign_id','left'],
                ['brand b','b.id = s.foreign_id','left']
            ],'field' => [
                's.id','s.store_type','s.run_type','s.is_default','case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as name','s.auth_status'
            ],
        ];
        $storeList =  $model -> getList($config);
        $this -> assign('storeList',$storeList);
        return $this->fetch();
    }

    /**店铺管理
     */
    public function edit(){
        $model = new \common\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this->factory['id']);
        }else{
            // 企业旗舰店
            $modelFactory = new \common\model\Record();
            $config = [
                'where' => [
                    ['r.factory_id','=',$this->factory['id']]
                ],'field' =>  ['r.id,r.short_name as name,r.logo_img as img']
            ];
            $factoryStore =  $modelFactory -> getInfo($config);
            if(empty($factoryStore)){
                $this -> error('请完善档案资料,再申请开店',url('Record/edit'));
            }
            $this -> assign('factoryStore',$factoryStore);
            //企业品牌旗舰店名
            $modelFactory = new \common\model\Brand();
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
            $modeStore = new \common\model\Store();
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
            $model = new \common\model\Store();
            return $model->edit($this->factory['id']);
        }
    }

    //获取店铺列表
    public function getList(){
        if(request()->isAjax()){
            $modelStore = new \common\model\Store();
            $config = [
                'field' => [
                    's.id','s.store_type','s.run_type',
                    'f.name factory_name','us.id user_store_id','u.nickname','u.mobile_phone',
                ],'leftJoin' => [
                    ['factory f','f.id = s.factory_id'],
                    ['user_store us','s.id = us.store_id'],
                    ['user u','u.id = us.user_id'],
                ],'where' => [
                    ['s.status','=',0],
                ],
            ];
            $list = $modelStore->getList($config);
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    //设置店铺店长
    public function setManager(){
        if(request()->isAjax()){
            $modelStore = new \common\model\Store();
            return $modelStore->setManager($this->factory['id']);
        }
    }
}
<?php
namespace app\store\controller;
class Store extends \think\Controller{
    //开店部署首页
    public function index(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            return $this->fetch();
        }

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
                's.id','s.store_type','s.run_type','s.is_default',
                'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as name','s.auth_status'
            ],
        ];
        $storeList =  $model -> getList($config);
        $this -> assign('storeList',$storeList);
        return $this->fetch();
    }

    /**店铺编辑
     */
    public function edit(){
        $model = new \common\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this->factory['id'],$this->user['id']);
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
                    's.id','s.store_type','s.run_type','s.is_default','s.operational_model',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                    'u.mobile_phone','us.id user_store_id','us.factory_id','us.type','us.user_name name',
                ],'join' => [
                    ['factory f','f.id = s.factory_id','left'],
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                    ['user_store us','s.id = us.store_id','left'],
                    ['user u','u.id = us.user_id','left'],
                ],'where' => [
                    ['s.status','=',0],
                    ['s.factory_id','=',$this->factory['id']],
                    ['f.status','=',0],
                    ['f.type','=',2],
                    ['us.status','=',0],
                    ['us.type','in',[1,3]],
                ],
            ];
            $list = $modelStore->getList($config);
            $storeList = [];
            $userList = [];
            if(!empty($list)){
                foreach ($list as $item){
                    if($item['type'] == 1){
                        $item['name'] = '';
                        $item['mobile_phone'] = '';
                        array_push($storeList,$item);
                    }
                    if($item['type'] == 3){
                        array_push($userList,$item);
                    }
                }
            }
            if(!empty($storeList) && !empty($userList)){
                foreach ($storeList as &$store){
                    foreach ($userList as $user){
                        if($store['id'] == $user['id'] && $store['factory_id'] == $user['factory_id'] ){
                            $store['name'] = $user['name'];
                            $store['mobile_phone'] = $user['mobile_phone'];
                        }
                    }
                }
            }
            $this->assign('list',$storeList);
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
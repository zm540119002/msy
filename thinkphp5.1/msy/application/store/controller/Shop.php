<?php
namespace app\store\controller;

class Shop extends \common\controller\StoreBase
{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelShop = new \app\store\model\Shop();
            $config = [
                'field' => [
                    's.id','s.name shop_name',
                    'u.name','u.mobile_phone',
                    'us.id user_shop_id','us.type',
                ],'join' => [
                    ['user_shop us','s.id = us.shop_id','left'],
                    ['common.user u','u.id = us.user_id','left'],
                ],'where' => [
                    ['s.status','=',0],
                    ['us.status','=',0],
                    ['s.user_id','=',$this->user['id']],
                    ['s.factory_id','=',$this->factory['id']],
                    ['s.store_id','=',$this->store['id']],
                    ['us.type','in',[1,3]],
                ],
            ];
            $list = $modelShop->getList($config);
            $shopList = [];
            $userList = [];
            if(!empty($list)){
                foreach ($list as $item){
                    if($item['type'] == 1){
                        $item['name'] = '';
                        $item['mobile_phone'] = '';
                        array_push($shopList,$item);
                    }
                    if($item['type'] == 3){
                        array_push($userList,$item);
                    }
                }
            }
            if(!empty($shopList) && !empty($userList)){
                foreach ($shopList as &$shop){
                    foreach ($userList as $user){
                        if($shop['id'] == $user['id'] && $shop['factory_id'] == $user['factory_id'] && $shop['store_id'] == $user['store_id']){
                            $shop['name'] = $user['name'];
                            $shop['mobile_phone'] = $user['mobile_phone'];
                        }
                    }
                }
            }
            $this->assign('list',$shopList);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $modelShop = new \app\store\model\Shop();
            $info = $modelShop->edit($this->user['id'],$this->factory['id'],$this->store['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }
    }

    /**删除
     */
    public function del(){
        if(request()->isAjax()){
            $modelUserShop = new \app\store\model\UserShop();
            $condition = [
                ['factory_id','=',$this->factory['id'],],
                ['store_id' ,'=', $this->store['id'],],
                ['id' ,'=', $_POST['userShopId'],],
                ['shop_id' ,'=', $_POST['id'],],
                ['type' ,'=', 3,],
                ['status' ,'=', 0,],
            ];
            return $modelUserShop->del($condition);
        }
    }
}
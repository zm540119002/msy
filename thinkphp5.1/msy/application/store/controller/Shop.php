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
                    's.id','s.name store_name',
                    'u.nickname name','u.mobile_phone',
                ],'leftJoin' => [
                    ['user_shop us','s.id = us.shop_id'],
                    ['common.user u','u.id = us.user_id'],
                ],'where' => [
                    ['s.status','=',0],
                    ['s.user_id','=',$this->user['id']],
                    ['s.factory_id','=',$this->factory['id']],
                    ['s.store_id','=',$this->store['id']],
                ],
            ];
            $list = $modelShop->getList($config);
            $this->assign('list',$list);
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
            $modelShop = new \app\store\model\Shop();
            return $modelShop->del($this->factory['id']);
        }
    }
}
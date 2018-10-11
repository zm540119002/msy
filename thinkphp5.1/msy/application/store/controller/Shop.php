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
                    's.id','s.name',
                    'u.nickname','u.mobile_phone',
                ],'leftJoin' => [
                    ['common.user u','u.id = s.user_id'],
                ],'where' => [
                    ['s.status','=',0],
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
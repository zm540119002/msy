<?php
namespace app\store\controller;

class Shop extends \common\controller\StoreBase{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelShop = new \app\store\model\Shop();
            $config = [
                'field' => [
                    's.id shop_id','s.name shop_name',
                    'u.mobile_phone',
                    'us.id user_shop_id','us.type','us.user_name name',
                ],'join' => [
                    ['user_shop us','s.id = us.shop_id','left'],
                    ['common.user u','u.id = us.user_id','left'],
                ],'where' => [
                    ['s.status','=',0],
                    ['us.status','=',0],
                    ['s.user_id','=',$this->user['id']],
                    ['s.factory_id','=',$this->factory['id']],
                    ['s.store_id','=',$this->store['id']],
                    ['us.type','=',3],
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
            $modelShop->startTrans();//事务开启
            $condition = [
                ['user_id','=',$this->user['id'],],
                ['factory_id','=',$this->factory['id'],],
                ['store_id' ,'=', $this->store['id'],],
                ['id' ,'=', $_POST['shopId'],],
                ['status' ,'<>', 2,],
            ];
            $res = $modelShop->del($condition,false);
            if($res['status']==0){
                $modelShop->rollback();//事务回滚
                return errorMsg('失败',$res['info']);
            }
            $modelUserShop = new \app\store\model\UserShop();
            $condition = [
                ['factory_id','=',$this->factory['id'],],
                ['store_id' ,'=', $this->store['id'],],
                ['shop_id' ,'=', $_POST['shopId'],],
                ['status' ,'<>', 2,],
            ];
            $res = $modelUserShop->del($condition,false);
            if($res['status']==0){
                $modelShop->rollback();//事务回滚
                return errorMsg('失败',$res['info']);
            }
            $modelShop->commit();//事务提交
            return successMsg('成功');
        }
    }
}
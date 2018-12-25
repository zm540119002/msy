<?php
namespace app\store\controller;

class CustomerClient extends \common\controller\UserBase{
    /**售前
     */
    public function beforeSale(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.from_id','cm.to_id','cm.content',
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
            $list = $modelChatMessage->getList($config);
            $this->assign('list',$list);
            return view('list_tpl');
            return $this->fetch();
        }
    }

    /**售后
     */
    public function afterSale(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            return $this->fetch();
        }
    }
}
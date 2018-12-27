<?php
namespace app\index\controller;

class CustomerClient extends \common\controller\UserBase{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.id','cm.from_id','cm.to_id','cm.content','cm.create_time',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => 'u.status = 0 and cm.status = 0 and cm.type = 1 ' .
                    'and (cm.from_id = ' .$this->user['id'] . ' and cm.to_id = 17) ' .
                    'or ( cm.from_id = 17' . ' and cm.to_id = ' .$this->user['id'] . ')'
                ,'order' => [
                    'cm.create_time'=>'asc',
                ],'limit' => 20,
            ];
            $list = $modelChatMessage->getList($config);
            foreach ($list as &$message){
                if($this->user['id']==$message['from_id']){
                    $message['who'] = 'me';
                }else{
                    $message['who'] = 'others';
                }
            }
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }
}
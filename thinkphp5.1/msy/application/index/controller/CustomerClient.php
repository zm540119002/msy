<?php
namespace app\index\controller;

class CustomerClient extends \common\controller\UserBase{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.id','cm.from_id','cm.to_id','cm.content','cm.from_read','cm.create_time',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' =>
                    'u.status = 0 and cm.status = 0 and cm.type = 1 ' .
                    'and ((cm.from_id = ' .$this->user['id'] . ' and cm.to_id = 17) ' .
                    'or ( cm.from_id = 17' . ' and cm.to_id = ' .$this->user['id'] . '))'
                ,'order' => [
                    'cm.create_time'=>'desc',
                ],'limit' => config('custom.chat_page_size'),
            ];
            $messages = $modelChatMessage->getList($config);
            $messages = array_reverse($messages);
            foreach ($messages as &$message){
                if($this->user['id']==$message['from_id']){
                    $message['who'] = 'me';
                }else{
                    $message['who'] = 'others';
                }
            }
            $this->assign('list',$messages);
            return view('list_tpl');
        }else{
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'count(id) unreadCount',
                ],'where' =>
                    'status = 0 and type = 1 and to_read = 0 ' .
                    'and (from_id = 17 and cm.to_id = ' . $this->user['id'] . ') '
                ,
            ];
            $unreadCount = $modelChatMessage->getList($config);
            print_r($unreadCount['unreadCount']);exit;
            $this->assign('unreadCount',$unreadCount['unreadCount']);
            return $this->fetch();
        }
    }
}
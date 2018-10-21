<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase{
    public function __construct(){
        parent::__construct();
        if($this->user){
            //获取厂商店铺详情列表
            \common\cache\Store::removeManagerStore($this->user['id']);
            $storeList = \common\cache\Store::getManagerStore($this->user['id']);
            $list = [];
            foreach ($storeList as $item) {
                if(!array_key_exists($item['factory_id'],$list)){
                    $list[] = [
                        'factory_id' => $item['factory_id'],
                        'name' => $item['name'],
                        'type' => $item['type'],
                        'store_list' => [
                            'id' => $item['id'],
                            'store_type' => $item['store_type'],
                            'run_type' => $item['run_type'],
                            'is_default' => $item['is_default'],
                        ],
                    ];
                }
            }
            $this->assign('managerStoreList', $list);
            print_r($list);exit;
        }
    }

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $list = $modelManagerManage->getList($this->user['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->edit($this->user['id'],$this->user['type']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }
    }

    /**删除管理员
     */
    public function del(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->del($this->user['id']);
        }
    }
}
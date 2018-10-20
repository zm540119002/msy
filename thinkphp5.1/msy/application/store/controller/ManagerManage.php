<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase{
    public function __construct(){
        parent::__construct();
        if($this->user){
            //获取厂商店铺详情列表
            \common\cache\Store::removeManagerStore($this->user['id']);
            $list = \common\cache\Store::getManagerStore($this->user['id']);
            $this->assign('managerStoreList', $list);
            print_r($list);
            exit;
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
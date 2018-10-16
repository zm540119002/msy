<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase
{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $list = $modelManagerManage->getList($this->factory['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            exit;
            return $this->fetch();
        }
    }

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->edit($this->factory['id'],$this->factory['type']);
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
            return $modelManagerManage->del($this->factory['id']);
        }
    }
}
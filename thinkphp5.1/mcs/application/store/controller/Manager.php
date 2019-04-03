<?php
namespace app\store\controller;

class Manager extends \common\controller\FactoryBase{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManager = new \app\store\model\Manager();
            $list = $modelManager->getList($this->factory['id']);
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
            $modelManager = new \app\store\model\Manager();
            $info = $modelManager->edit($this->factory['id'],$this->factory['type']);
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
            $modelManager = new \app\store\model\Manager();
            return $modelManager->del($this->factory['id']);
        }
    }
}
<?php
namespace app\store\controller;

class Manager extends \common\controller\FactoryBase
{
    /**首页
     */
    public function index(){
        $modelManager = new \app\store\model\Manager();
        $list = $modelManager->getList($this->factory['id']);
        return $list;
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**新增管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManager = new \app\store\model\Manager();
            $info = $modelManager->edit($this->user['id'],$this->factory['id']);
            if($info['id']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }
    }
}
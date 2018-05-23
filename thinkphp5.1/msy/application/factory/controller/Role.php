<?php
namespace app\factory\controller;

class Role extends FactoryBase
{
    /**首页
     */
    public function index(){
        $modelRole = new \app\factory\model\Role();
        if(request()->isAjax()){
            $info = $modelRole->edit($this->factory['id']);
            $this->assign('info',$info);
            return view('info_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**获取组织列表
     */
    public function  getRoleList(){
        if(!request()->isGet()){
            return config('custom.not_get');
        }
        $modelRole = new \app\factory\model\Role();
        $list = $modelRole->getRoleList($this->factory['id']);
        return $list;
    }

    /**删除
     */
    public function  del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $modelRole = new \app\factory\model\Role();
        return $modelRole->del($this->factory['id'],true);
    }
}
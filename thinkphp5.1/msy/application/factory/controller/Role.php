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
            if($info['status']==0){
                return $info;
            }
            $this->assign('info',$info);
            return view('info_tpl');
        }else{
            //获取所有权限节点
            $node = new \common\lib\Node();
            $this->assign('allNode',$node->getAllNode());
            return $this->fetch();
        }
    }

    /**获取组织列表
     */
    public function  getList(){
        $modelRole = new \app\factory\model\Role();
        $list = $modelRole->getList($this->factory['id']);
        $this->assign('list',$list);
        return view('list_tpl');
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

    /**获取角色权限列表
     */
    public function getRoleNode(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $modelRoleNode = new \app\factory\model\RoleNode();
        $list = $modelRoleNode->getList();
        $list = array_values($list);
        var_dump($list);
        return array_column($list,'node_id');
    }

    /**保存角色权限
     */
    public function saveRoleNode(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $modelRoleNode = new \app\factory\model\RoleNode();
        $res = $modelRoleNode->edit();
        return $res;
    }
}
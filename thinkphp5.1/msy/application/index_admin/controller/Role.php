<?php
namespace app\index_admin\controller;

class Role extends \common\controller\UserBase
{
    /**角色-管理
     */
    public function manage(){
        if(!request()->isGet()){
            return config('not_get');
        }
        return $this->fetch();
    }
    /**角色-编辑
     */
    public function edit(){
        $modelRole = new \common\model\Role();
        if(request()->isPost()){
            return $modelRole->edit();
        }else{
            $id = input('id',0);
            if($id){
                $where = [
                    'status' => 0,
                    'id' => $id,
                ];
                $info = $modelRole->where($where)->find();
                $this->assign('info',$info);
            }
            return $this->fetch();
        }
    }
    /**角色-赋权
     */
    public function empower(){
        $modelRoleNode = new \common\model\RoleNode();
        if(request()->isPost()){
            return $modelRoleNode->edit();
        }else{
            $roleId = input('id',0);
            $this->assign('roleId',$roleId);
            $response = $modelRoleNode->where('role_id','=',$roleId)->select();
            $response = $response->toArray();
            $nodeIds = array_column($response,'node_id');
            $this->assign('nodeIds',$nodeIds?:[]);
            $menu = new \common\lib\Menu();
            $menuList = $menu->getAllMenu();
            $this->assign('menuList',$menuList);
            return $this->fetch();
        }
    }
    /**角色-列表
     */
    public function getList(){
        if(!request()->isGet()){
            return config('not_get');
        }
        $modelRole = new \common\model\Role();
        $list = $modelRole->pageQuery();
        $this->assign('list',$list);
        return $this->fetch('role_list');
    }
    /**角色-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('not_post');
        }
        $modelRole = new \common\model\Role();
        return $modelRole->del();
    }
}
<?php
namespace app\index_admin\controller;

class User extends \common\controller\UserBase
{
    /**用户-管理
     */
    public function manage(){
        if(!request()->isGet()){
            return config('not_get');
        }
        return $this->fetch();
    }
    /**用户-编辑
     */
    public function edit(){
        $modelUser = new \common\model\User();
        if(request()->isPost()){
            return $modelUser->edit();
        }else{
            $id = input('id',0);
            if($id){
                $where = [
                    'status' => 0,
                    'id' => $id,
                ];
                $info = $modelUser->where($where)->find();
                $this->assign('info',$info);
            }
            return $this->fetch();
        }
    }
    /**用户-列表
     */
    public function getList(){
        if(!request()->isGet()){
            return config('not_get');
        }
        $modelUser = new \common\model\User();
        $list = $modelUser->pageQuery();
        $this->assign('list',$list);
        return $this->fetch('user_list');
    }
    /**用户-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('not_post');
        }
        $modelUser = new \common\model\User();
        return $modelUser->del();
    }
}
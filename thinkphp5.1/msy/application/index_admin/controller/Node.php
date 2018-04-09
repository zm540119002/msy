<?php
namespace app\index_admin\controller;

class Node extends \common\controller\Base {
    /**节点-管理
     */
    public function manage(){
        if(request()->isPost()){
        }else{
            return $this->fetch();
        }
    }
    /**节点-编辑
     */
    public function edit(){
        $modelNode = new \common\model\Node();
        if(request()->isPost()){
            return $modelNode->edit();
        }else{
            $id = input('id',0);
            if($id){
                $where = [
                    'status' => 0,
                    'id' => $id,
                ];
                $info = $modelNode->where($where)->find();
                $this->assign('info',$info);
            }
            return $this->fetch();
        }
    }
    /**节点-列表
     */
    public function nodeList(){
        if(!request()->isGet()){
            return config('not_get');
        }
        $modelNode = new \common\model\Node();
        $list = $modelNode->pageQuery();
        $this->assign('list',$list);
        return $this->fetch('node_list');
    }
    /**节点-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('not_post');
        }
        $modelNode = '';
        $res = $modelNode->del();
        return $res;
    }
}
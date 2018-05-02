<?php
namespace app\index_admin\controller;

class GoodsCategory extends \common\controller\Base
{
    /**商品分类-管理
     */
    public function manage(){
        if(!request()->isGet()){
            return config('not_get');
        }
        return $this->fetch();
    }

    /**商品分类-编辑
     */
    public function edit(){
        $modelGoodsCategory = new \common\model\GoodsCategory();
        if(request()->isPost()){
            return $modelGoodsCategory->edit();
        }else{
            $id = input('id',0);
            if($id){
                $where = [
                    'status' => 0,
                    'id' => $id,
                ];
                $info = $modelGoodsCategory->where($where)->find();
                $this->assign('info',$info);
            }
            return $this->fetch();
        }
    }

    /**商品-列表
     */
    public function getList(){
        if(!request()->isGet()){
            return config('not_get');
        }
        $modelGoodsCategory = new \common\model\GoodsCategory();
        $list = $modelGoodsCategory->pageQuery();
        $this->assign('list',$list);
        return $this->fetch('list_tpl');
    }

    /**商品分类-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('not_post');
        }
        $modelGoodsCategory = new \common\model\GoodsCategory();
        return $modelGoodsCategory->del();
    }
}
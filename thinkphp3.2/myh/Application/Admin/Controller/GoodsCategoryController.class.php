<?php
namespace Admin\Controller;

use Admin\Controller\BaseAuthUserController;

class GoodsCategoryController extends BaseAuthUserController {
    /**商品分类-管理
     */
    public function goodsCategoryManage(){
        $model = D('GoodsCategory');
        if(IS_POST){
            $level = I('post.level',0,'int');
            $where = array();
            if($level == 1){
                $where['gc.parent_id_1'] = I('post.goodsCategoryId',0,'int');
                $where['gc.level'] = 2;
            }elseif($level == 2){
                $where['gc.parent_id_1'] = I('post.parent_id_1',0,'int');
                $where['gc.parent_id_2'] = I('post.goodsCategoryId',0,'int');
                $where['gc.level'] = 3;
            }
            $this->goodsCategoryList = $model->selectGoodsCategory($where);
            $this->display('goodsCategoryList');
        }else{
            $where = array(
                'gc.level' => 1,
            );
            $this->goodsCategoryList = $model->selectGoodsCategory($where);
            $this->display();
        }
    }

    /**商品分类-编辑
     */
    public function goodsCategoryEdit(){
        $model = D('GoodsCategory');
        if(IS_POST){
            if( isset($_POST['img']) && $_POST['img'] ){
                $_POST['img'] = $this->moveImgFromTemp(C('GOODS_CATEGORY_IMG'),basename($_POST['img']));
            }
            if(isset($_POST['goodsCategoryId']) && intval($_POST['goodsCategoryId'])){
                $goodsCategoryId =I('post.goodsCategoryId',0,'int');
                $where = array(
                    'gc.id' => $goodsCategoryId ,
                );
                $goodsCategoryInfo = $model->selectGoodsCategory($where);
                $goodsCategoryInfo = $goodsCategoryInfo[0];
                //删除项目分类图
                if($goodsCategoryInfo['img']){
                    $this->delImgFromPaths($goodsCategoryInfo['img'],$_POST['img']);
                }
                $res = $model->saveGoodsCategory();
            }else{
                $res = $model->addGoodsCategory();
            }
            $this->ajaxReturn($res);
        }else{
            $this->operate = I('get.operate','','string');

            if (isset($_GET['goodsCategoryId']) && intval($_GET['goodsCategoryId'])){
                $goodsCategoryId = I('get.goodsCategoryId', 0, 'int');
                $where = array(
                    'gc.id' => $goodsCategoryId,
                );
                $editGoodsCategory = $model->selectGoodsCategory($where);
                $this->editGoodsCategory = $editGoodsCategory[0];
            }
            $this->allCategoryList = $model->selectGoodsCategory();

            $this->display();
        }
    }

    /**商品分类-删除
     */
    public function delGoodsCategory(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('GoodsCategory');
        $res = $model->delGoodsCategory();
        $this->ajaxReturn($res);
    }
}
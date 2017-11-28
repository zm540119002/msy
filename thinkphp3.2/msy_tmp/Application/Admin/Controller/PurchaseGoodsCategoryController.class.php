<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\PurchaseGoodsCategory;
class PurchaseGoodsCategoryController extends Controller {

    public function goodsCategoryList(){
        if(IS_POST){
            $model = D('PurchaseGoodsCategory');
            $level = I('post.level',0,'int');

            $where = array();
            if($level == 1){
                $where['gc.parent_id'] = I('post.goodsCategoryId',0,'int');
                $where['gc.level'] = 2;
            }elseif($level == 2){
                $where['gc.parent_id'] = I('post.goodsCategoryId',0,'int');
                $where['gc.level'] = 3;
            }
            $this->goodCategory = $model->selectGoodsCategory($where);
            $this->display('ajaxGoodsCategoryList');
        }else{
            $model = D('PurchaseGoodsCategory');
            $where = array(
                'gc.level' => 1,
            );
            $this->goodCategory = $model->selectGoodsCategory($where);

            $this->display();
        }
    }


    /**
     * 商品分类添加和修改页面
     */
    public function editGoodsCategory()
    {
        $model = D('PurchaseGoodsCategory');
        if(IS_POST){
            if(isset($_POST['goodsCategoryId']) && intval($_POST['goodsCategoryId'])){
                $res = $model->saveGoodsCategory();
            }else{
                $res = $model->addGoodsCategory();
            }
            $this->ajaxReturn($res);
        }else{
            //修改 分类信息
            if(isset($_GET['categoryId']) && $_GET['categoryId'] ){
                $where['gc.id'] = I('get.categoryId',0,'int');
                $goods_category_info = $model -> selectGoodsCategory($where);
                $this -> goods_category_info = $goods_category_info[0];
            }
            //增加下一级分类所属分类的Id 和它所属的分类ID
            if(isset($_GET['id']) && $_GET['id'] ){
                $where['gc.id'] = I('get.id',0,'int');
                $goods_category_ids = $model -> selectGoodsCategory($where);
                $this -> goods_category_ids = $goods_category_ids[0];
            }
            //一级分类
            $_where['gc.parent_id'] = 0;
            $this -> cat_list =  $model -> selectGoodsCategory($_where);
            $this -> display();
        }
    }




    /**
     * 删除分类
     */
    public function delGoodsCategory(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $categoryIds = I('post.categoryIds/a');
        if(!$categoryIds){
            return errorMsg('确少参数goodsCategoryIds');
        }
        $model = D('PurchaseGoodsCategory');
        $result= $model->delGoodsCategory($categoryIds);
        $this->ajaxReturn($result);
    }


    /**
     *  获取下级分类
     */
    function getCategory(){
        $parent_id = I('get.parent_id/d'); // 商品分类 父id
        if( $parent_id  ){
            $where = array('parent_id' => $parent_id);
            $list =  $Model = M('goods_category','','DB_CONFIG2')->where($where)->select();
           
            foreach($list as $k => $v)
                $html .= "<option value='{$v['id']}'>{$v['name']}</option>";
            exit($html);
        }else{
           exit();
        }

    }
    
}
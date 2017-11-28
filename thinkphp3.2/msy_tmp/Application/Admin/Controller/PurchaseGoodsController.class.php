<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class PurchaseGoodsController extends BaseController {
    public function index(){
        $this->display();
    }
    //商品列表
    public function goodsList(){
        //一级分类
        $_where['gc.parent_id'] = 0;
        $this -> catList =  D('PurchaseGoodsCategory') -> selectGoodsCategory($_where);
        $this ->display();
    }
   //商品列表
    public function ajaxGoodsList(){
        $model = D('PurchaseGoods');
        $category_id_1 = intval($_GET['parent_id_1']);
        $category_id_2 = intval($_GET['parent_id_2']);
        $category_id_3 = intval($_GET['parent_id_3']);
        if ($category_id_1  && $category_id_2 && $category_id_3) {
            $where['g.goods_category_id']   = array($category_id_3);
        }
        if($category_id_1  && $category_id_2 && $category_id_3==0){
            $where['g.goods_category_id']   = array($category_id_2);
            $where['g.goods_category_id_2'] = array($category_id_2);
            $where['_logic'] = 'OR';
        }
        if($category_id_1  && $category_id_2==0 && $category_id_3==0){
            $where['g.goods_category_id']   = array($category_id_1);
            $where['g.goods_category_id_1'] = array($category_id_1);
            $where['_logic'] = 'OR';
        }
        if (trim($_GET['keyword']) != '') {
            $where['g.name'] = array('like', '%' . trim($_GET['keyword']) . '%');
        }
        if(isset($where)){
            $map['_complex'] = $where;
        }

        $field = array(
            'g.id','g.name','g.goods_category_id','g.goods_category_id_1','g.goods_category_id_2','g.goods_category_id_3',
            'g.price','g.promotion_price','g.sort','g.storage','g.state',
            'gc.id category_id','gc.name goods_category_name',
        );
        $join = array(
            ' left join goods_category gc on gc.id = g.goods_category_id ',
        );

        $order = 'g.sort';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        if($_GET['ajax'] == 'list'){
            $map['state']  = array('eq',1);
            $goodsList = page_query($model,$map,$field,$order,$join,$group,$pageSize,$alias='g');
            $this->goodsList = $goodsList['data'];
            $this->pageList = $goodsList['pageList'];
            $this ->display('ajaxGoodsList');
        }
        if($_GET['ajax'] == 'upDown'){
            $goodsList = page_query($model,$map,$field,$order,$join,$group,$pageSize,$alias='g');
            $this->goodsList = $goodsList['data'];
            $this->pageList  = $goodsList['pageList'];
            $this ->display('ajaxUpDownGoodsList');
        }
    }


    /**
     * 添加商品显示页面
     */
    public function addGoods(){
        $model = D('PurchaseGoods');
        if(IS_POST){
            $res = $model->create();
            if(!$res){
                $this->ajaxReturn(errorMsg($model->getError())) ;
            }
            $newRelativePath = C('GOODS_FIRST_IMG_PATH');
            if( isset($_POST['first_img']) && $_POST['first_img'] ){
                $_POST['first_img'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['first_img']));
            }
            $newRelativePath = C('GOODS_DETAIL_IMG_PATH');
            if(isset($_POST['detail_imgs']) && $_POST['detail_imgs'] ){
                $post_detail_imgs = array();
                foreach ($_POST['detail_imgs'] as $val){
                    $url = $this->moveImgFromTemp($newRelativePath,basename($val));
                    $post_detail_imgs[] = $url;
                }
            }

            if(isset($_POST['goodsId']) && $_POST['goodsId']){//修改
                //删除已被修改的图片
                $where['id'] = $_POST['goodsId'];
                $goodsInfo = $model->selectGoods($where);
                if($_POST['first_img'] !== $goodsInfo[0]['first_img']){
                    unlink('./Uploads/'.$goodsInfo[0]['first_img']);
                }
                $detail_imgs = explode(",", $goodsInfo[0]['detail_imgs']);
                $diff_detail_imgs = array_diff($detail_imgs,$post_detail_imgs);
                foreach ($diff_detail_imgs as $k => $v){
                    unlink('./Uploads/'.$v);
                }

                $_POST['update_time'] = time();
                $_POST['detail_imgs'] = implode(',',$post_detail_imgs);
                $res = $model -> saveGoods($_POST);
                $this ->ajaxReturn($res);

            }else{
                $_POST['detail_imgs'] = implode(',',$post_detail_imgs);
                $_POST['create_time'] = time();
                $res = $model -> addGoods($_POST);
                $this ->ajaxReturn($res);
            }

        }else{
            if(isset($_GET['goodsId']) && $_GET['goodsId']){
                $goodsId   = intval($_GET['goodsId']);
                $where = array('id' => $goodsId);
                $goodsInfo = $model->selectGoods($where);
                $pieces = explode(",", $goodsInfo[0]['detail_imgs']);
                $connt = array();
                foreach ($pieces as $v){
                    $connt[] = "&lt;img src=&quot;/Uploads/$v &quot; alt=&quot;&quot; /&gt;";
                }
                $connt = implode('',$connt);
                $this -> connt = $connt;
                $this -> goodsInfo = $goodsInfo[0];
            }
            //一级分类
            $_where['gc.parent_id'] = 0;
            $this -> catList =  D('PurchaseGoodsCategory') -> selectGoodsCategory($_where);
            $this ->display();
        }
    }


    /**
     * 商品上下架操作
     */
    public function upDownGoods(){
        $goods = M('goods','','DB_CONFIG2');
        if(IS_POST){
            if(intval($_POST['online']) !=0 && intval($_POST['online']) !=1 ){
                show(0,'参数有误');
            }
            //单独修改商品上下架
            if(isset($_POST['goods_id']) && $_POST['goods_id']){
                $where = array( 'id'=>intval($_POST['goods_id']));
                $data  = array( 'state' => intval( $_POST['online']));
                $rst   = $goods -> where( $where ) -> save( $data );
            }
            //批量修改商品上下架
            if( isset( $_POST['goods_ids']) && $_POST['goods_ids'] ){
                $data = array( 'state' => intval( $_POST['online'] ));
                $rst  = $goods -> where( array('id' => array('in', $_POST['goods_ids']))) -> save( $data );
            }

            if(false === $rst){
                show(0,'修改状态失败');
            }else{
                if(intval($_POST['online']) == 0){
                    show(1,'修改状态成功','已下架');
                }else{
                    show(1,'修改状态成功','已上架');
                }

            }
        }
        $_where['gc.parent_id'] = 0;
        $this -> catList =  D('PurchaseGoodsCategory') -> selectGoodsCategory($_where);
        $this ->display();
    }

    /**
     * 删除商品
     */
    public function delGoods(){
        if(IS_POST){
            $goods = M('goods','','DB_CONFIG2');
            //批量删除商品
            if(is_array(I('post.goods_ids')) && !empty(I('post.goods_ids'))){
                $rst = $goods -> where(array('id' => array('in', $_POST['goods_ids']))) -> delete();
            }
            //单独删除商品
            if(isset($_POST['goods_id']) && $_POST['goods_id']){
                $rst = $goods -> where(array('id' => intval($_POST['goods_id']))) -> delete();

            }
            if(!$rst){
                show(0,'删除操作失败！');
            }else{
                show(1,'删除成功！');
            }
        }
    }
    
}

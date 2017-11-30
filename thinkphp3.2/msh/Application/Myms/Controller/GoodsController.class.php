<?php
namespace Myms\Controller;
use  web\all\Controller\BaseController;
use  web\all\Lib\AuthUser;
class GoodsController extends BaseController {
    //产品列表
    public function goodsIndex(){
        if(IS_POST){
        }else{
            if(isset($_GET['categoryId']) && intval($_GET['categoryId'])){
                $this->categoryId = $_GET['categoryId'];
            }
            $where['parent_id_1'] = 0;
            $catList = D('goods_category')->selectGoodsCategory($where);
            $this -> catList = $catList;
            //购物车信息
            $this-> cartInfo = D('cart') -> getAllCartInfo();
            $this->cartList = D('cart') -> cartList();
            $this->display();
        }
    }
    //工作室特惠列表
    public function studioSpeList(){
        $where['status']  = array('eq',0);
        $where['special_price'] = array('gt',0);
        $this->speGoodsList =  M('goods')->where($where)->order('id desc')->select();
        //购物车信息
        $this-> cartInfo = D('cart') -> getAllCartInfo();
        $this->cartList = D('cart') -> cartList();
        $this->display();
    }

    //微团列表
    public function goodsGroup(){
        $where['status']  = array('eq',0);
        $where['group_price'] = array('gt',0);
        $this->groupGoodsList =  M('goods')->where($where)->order('id desc')->select();
        //购物车信息
        $this-> cartInfo = D('cart') -> getAllCartInfo();
        $this->cartList = D('cart') -> cartList();
        $this->display();
    }


    //商品详情页
    public function goodsInfo(){
        if(IS_GET){
            $user = AuthUser::getSession();
            $this -> user = $user;
            $goodsId   = intval($_GET['goodsId']);
            $type = $_GET['buyType'];
            $goodsInfo = D('goods') -> getGoodsInfoByGoodsId($goodsId,$type);
            $tag                      = explode(',',$goodsInfo['tag']);
            $detailImg                 = explode(',',$goodsInfo['detail_img']);
            $goodsInfo['tag']         = $tag;
            $this->detailImg = $detailImg;
            $commonImg = M('common_images')->limit(1)->select();
            $commonImg = explode(',',$commonImg[0]['common_img']);
            $this->commonImg = $commonImg;
            $this -> goodsInfo     = $goodsInfo;
            $this->cartList = D('cart') -> cartList();
            $this -> cartInfo = D('cart')->getAllCartInfo();
            $this -> display();
        }
    }

    //商品列表
    public function goodsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $page = I('get.p',0,'int');
        $this->page = $page;
        //分页函数的参数
        $model = D('goods');
        $_where['g.status'] =0;
        $_where['g.on_off_line'] =1;
        $field = array(
            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3',
            'g.on_off_line','g.inventory','g.sort','g.main_img','g.price','g.discount_price','g.special_price',
            'g.group_price',
        );
        $join = '';
        $order = 'g.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $alias='g';
        $position = I('get.position');

        //初始化首页
        if($position === 'index' && $page == 1){
            $where['parent_id_1'] = 0;
            $gCategoryList = D('goods_category')->selectGoodsCategory($where);
            $catGoodsList = [];
            foreach ($gCategoryList as $v){
                $catWhere['g.category_id_1'] = $v['id'];
                $catWhere = array_merge($_where,$catWhere);
                $catInfo = '['.$v['name'].']'.$v['explain'];
                $goodsList = page_query($model,$catWhere,$field,$order,$join,$group,$pageSize,$alias);
                if( !empty($goodsList['data']) ){
                    $catGoodsList[$catInfo] = $goodsList;
                }
            }
            $this -> catGoodsList = $catGoodsList;

            //产品工作室特惠
            $speWhere['special_price'] = array('gt',0);
            $speWhere = array_merge($_where,$speWhere);
            $this->speGoodsList = page_query($model,$speWhere,$field,$order,$join,$group,$pageSize,$alias);

            //微团购
            $groupWhere['group_price'] = array('gt',0);
            $groupWhere = array_merge($_where,$groupWhere);
            $this->groupGoodsList = page_query($model,$groupWhere,$field,$order,$join,$group,$pageSize,$alias);

        }elseif($position ==='goods_index' && $page == 1){
            $where['parent_id_1'] = 0;
            $gCategoryList = D('goods_category')->selectGoodsCategory($where);
            $catGoodsList = [];
            foreach ($gCategoryList as $v){
                $catWhere['g.category_id_1'] = $v['id'];
                $catWhere = array_merge($_where,$catWhere);
                $catInfo = '['.$v['name'].']'.$v['explain'];
                $goodsList = page_query($model,$catWhere,$field,$order,$join,$group,$pageSize,$alias);
                if( !empty($goodsList['data']) ){
                    $catGoodsList[$catInfo] = $goodsList;
                }
            }
            $this -> catGoodsList = $catGoodsList;
        }else{
            if($page>1){
                $buyType = I('get.buyType',0,'int');
                if($buyType == 1){//正常价产品
                    $where['g.category_id_1'] = I('get.categoryId',0,'int');
                }
                if($buyType == 2){//工作室特惠产品
                    $where['special_price'] = array('gt',0);
                }
                if($buyType == 3){//微团
                    $where['group_price'] = array('gt',0);
                }
                $where = array_merge($_where,$where);
                $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);
                if(empty($goodsList['data'])){
                    $this->ajaxReturn(errorMsg('没有更多'));
                }else{
                    $this->goodsListLoad = $goodsList;
                }
            }

        }

        //商品分类切换到每个单独分类
        if(isset($_GET['catId']) && intval($_GET['catId'])){
            $where['category_id_1'] = $_GET['catId'];
            $where = array_merge($_where,$where);
            $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);
            if($page == 1){
                $this->goodsList = $goodsList['data'];
            }else{
                if(empty($goodsList['data'])){
                    $this->ajaxReturn(errorMsg('没有更多'));
                }else{
                    $this->goodsListLoad = $goodsList;
                }
            }
        }
        $this->display('goodsList');
    }
}
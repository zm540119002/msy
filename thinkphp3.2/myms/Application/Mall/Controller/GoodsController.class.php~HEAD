<?php
namespace Mall\Controller;
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
            $catList = D('GoodCategory')->selectGoodsCategory($where);
            $this -> catList = $catList;
            //购物车信息
            $this-> cartInfo = D('Cart') -> getAllCartInfo();
            $this->cartList = D('Cart') -> cartList();
            $this->display();
        }
    }
    //工作室特惠列表
    public function studioSpeList(){
        $where['status']  = array('eq',0);
        $where['buy_type'] = array('eq',2);
        $this->speGoodsList =  M('goods')->where($where)->order('id desc')->select();
        //购物车信息
        $this-> cartInfo = D('Cart') -> getAllCartInfo();
        $this->cartList = D('Cart') -> cartList();
        $this->display();
    }

    //微团列表
    public function goodsGroup(){
        $where['status']  = array('eq',0);
        $where['buy_type'] = array('eq',3);
        $this->groupGoodsList =  M('goods')->where($where)->order('id desc')->select();
        //购物车信息
        $this-> cartInfo = D('Cart') -> getAllCartInfo();
        $this->cartList = D('Cart') -> cartList();
        $this->display();
    }

    /**
     * 购买商品详情
     */
    public function getGoodsInfo(){
       if(!IS_POST){
           $this -> ajaxReturn(errorMsg('非异步请求'));
       }
        $id = I('post.id',0,'int');
        $this -> type  = 'goods';
        $goodsInfo = D('Goods') -> getGoodsInfoByGoodsId($id);
        $this->assign('goodsInfo',$goodsInfo);
        $this -> display('Cart/purchaseDetails');

    }

    //商品详情页
    public function goodsInfo(){
        if(IS_GET){
            $this->user = AuthUser::check();
            $goodsId   = intval($_GET['goodsId']);
            $goodsInfo = D('Goods') -> getGoodsInfoByGoodsId($goodsId);
            $tag                      = explode(',',$goodsInfo['tag']);
            $detailImg                 = explode(',',$goodsInfo['detail_img']);
            $goodsInfo['tag']         = $tag;
            $this->detailImg = $detailImg;
            $commonImg = M('common_images')->limit(1)->select();
            $commonImg = explode(',',$commonImg[0]['common_img']);
            $this->commonImg = $commonImg;
            $this -> goodsInfo   = $goodsInfo;
            $this->cartList = D('Cart') -> cartList();
            $this->groupBuySn = $_GET['groupBuySn'];
            $this->shareType = $_GET['shareType'];
            $this -> cartInfo = D('Cart') -> getAllCartInfo();
            //获取用户基本资料
//            $this->userInfo = $this -> getWeiXinUserInfo();
            //微信分享
            if(intval($goodsInfo['buy_type']) == 3){
                $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
                    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
                $currentLink = (is_ssl()?'https://':'http://').$host.$_SERVER['REQUEST_URI'];
                $backLink = substr($currentLink,0,strrpos($currentLink,'.html'));
                if(isset($_GET['groupBuySn']) && !empty($_GET['groupBuySn'])){
                    $baseShareUrl = substr($currentLink,0,strrpos($currentLink,'.groupBuySn'));
                }else{
                    $baseShareUrl = $backLink;
                }
                if(empty($backLink)){
                    $backLink = $currentLink;
                }
                session('baseShareUrl',$baseShareUrl);
                $this -> shareInfo = $this -> weiXinShareInfo('微团购',$backLink,$goodsInfo['main_img'],'好友邀请你参加美妍美社微团购，三人成团即享特惠....');
                $this -> signPackage = $this -> weiXinShareInit();
            }
//
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
        $model = D('Goods');
        $_where['g.status'] =0;
        $_where['g.on_off_line'] =1;
        $field = array(
            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3',
            'g.on_off_line','g.inventory','g.sort','g.main_img','g.price','g.discount_price','g.special_price',
            'g.group_price','g.buy_type'
        );
        $join = '';
        $order = 'g.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $alias='g';
        $position = I('get.position');

        //初始化首页
        if($position === 'index' && $page == 1){
            $where['parent_id_1'] = 0;
            $gCategoryList = D('GoodCategory')->selectGoodsCategory($where);
            $catGoodsList = [];
            foreach ($gCategoryList as $v){
                $catWhere['g.category_id_1'] = $v['id'];
                $catWhere['g.buy_type'] = 1;
                $catWhere = array_merge($_where,$catWhere);
                $catInfo = '['.$v['name'].']'.$v['explain'];
                $goodsList = page_query($model,$catWhere,$field,$order,$join,$group,$pageSize,$alias);
                if( !empty($goodsList['data']) ){
                    $catGoodsList[$catInfo] = $goodsList;
                }
            }
            $this -> catGoodsList = $catGoodsList;
            

            //产品工作室特惠
            $speWhere['buy_type'] = array('eq',2);
            $speWhere = array_merge($_where,$speWhere);
            $this->speGoodsList = page_query($model,$speWhere,$field,$order,$join,$group,$pageSize,$alias);

            //微团购
            $groupWhere['buy_type'] = array('eq',3);
            $groupWhere = array_merge($_where,$groupWhere);
            $this->groupGoodsList = page_query($model,$groupWhere,$field,$order,$join,$group,$pageSize,$alias);

        }else{
            if($page>1){
                $buyType = I('get.buyType',0,'int');
                if($buyType == 1){//正常价产品
                    $where['g.category_id_1'] = I('get.categoryId',0,'int');
                    $where['g.buy_type'] = array('eq',1);
                }
                if($buyType == 2){//工作室特惠产品
                    $where['g.buy_type'] = array('eq',2);
                }
                if($buyType == 3){//微团
                    $where['g.buy_type'] = array('eq',3);
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

    //分类产品
    public function categoryGoods(){
        if(IS_GET){
            $this -> categoryId = $_GET['categoryId'];
            $this->display();
;        }
    }
}
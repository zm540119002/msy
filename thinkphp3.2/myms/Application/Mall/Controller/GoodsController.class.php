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
            $this->display();
        }
    }
    //工作室特惠列表
    public function studioSpeList(){
        $where['status']  = array('eq',0);
        $where['buy_type'] = array('eq',2);
        $this->speGoodsList =  D('Goods')->where($where)->order('id desc')->select();
        $this->display();
    }

    //微团列表
    public function goodsGroup(){
        $where['status']  = array('eq',0);
        $where['buy_type'] = array('eq',3);
        $this->groupGoodsList =  D('Goods')->where($where)->order('id desc')->select();
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
        $goodsInfo = D('Goods') -> getGoodsInfoByGoodsId($id);
        $this->assign('goodsInfo',$goodsInfo);
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,3,4));
        if($goodsInfo['buy_type'] == 3){
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,8));
        }
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
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,3,4));
            //购物车配置开启的项
            if($this->goodsInfo['buy_type'] == 3){
                $conf = array(2,8);
            }else{
                $conf = array(2,3,4);
            }
            //微信分享
            $shareInfo = [];
            //获取当前url
            $currentLink = (is_ssl()?'https://':'http://').$this->host.$_SERVER['REQUEST_URI'];
            //分享的内容
            $shareInfo['title'] = $this->goodsInfo['name'];//分享的标题
            $shareInfo['shareImgUrl'] = $this->goodsInfo['main_img'];//分享的图片
            if(isset($_GET['shareType'])&&!empty($_GET['shareType'])){
                $shareType = $_GET['shareType'];
                if($shareType == 'referrer'){//推客分享
                    $conf = array(9,10,11);
                }
                if($shareType == 'groupBuy'){//团购分享
                    $conf = array(12);
                }
                if($shareType == 'referrer'){//推客分享
                    $shareInfo['desc'] = $this->goodsInfo['share_intro'];//分享的简介
                    $shLinkBase = substr($currentLink,0,strrpos($currentLink,'/shareType'));
                    $shareInfo['shareLink'] = $shLinkBase.'/userId/'.$this->user['id'];//分享url
                    $shareInfo['backUrl'] = $currentLink;//分享完跳转的url
                }
                if($shareType == 'groupBuy'){//团购分享
//                    $shareInfo['desc'] = $this->goodsInfo['group_share'];//分享的简介
                    $shareInfo['desc'] = '好友邀请你参加美妍美社微团购，三人成团即享特惠....';//分享的简介
                    $shLinkBase = substr($currentLink,0,strrpos($currentLink,'/shareType'));
                    $shareInfo['shareLink'] = $shLinkBase;//分享url
                    $shareInfo['backUrl'] = $currentLink;//分享完跳转的url
                }
                $this -> shareInfo = $this -> weiXinShare($shareInfo);
            }
            if(isset($_GET['groupBuyId'])&&!empty($_GET['groupBuyId'])){
                $this -> groupBuyId = $_GET['groupBuyId'];
                $model = D('GroupBuyDetail');
                $_where['gbd.group_buy_id'] =  $this -> groupBuyId ;
                $_where['gbd.pay_status'] = 2;
                $field=['wxu.id','wxu.openid','wxu.nickname','wxu.sex','wxu.country','wxu.province',
                    'wxu.city','wxu.latitude','wxu.longitude','wxu.longitude','wxu.headimgurl','wxu.subscribe',
                    'gb.overdue_time'
                ];
                $join=[ 'left join wx_user wxu on wxu.openid = gbd.openid ',
                    'left join group_buy gb on gb.id = gbd.group_buy_id ',
                ];
                $groupBuyDetail = $model->selectGroupBuyDetail($_where,$field,$join);
                print_r($groupBuyDetail);exit;
                $this->groupBuyDetail = $groupBuyDetail[0];
                //判断团购是否已过期
                if($this->groupBuyDetail['overdue_time'] - time() < 0){
                    $conf = array(20);
                    $this->unlockingFooterCart = unlockingFooterCartConfig($conf);
                    $this -> groupBuyEnd = 1;//团购结束标识位
                }
            }
            $this->unlockingFooterCart = unlockingFooterCartConfig($conf);
            $wxUser = D('WeiXin') -> wxLogin();
            session('openid',$wxUser['openid']);
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
        $_where['g.status'] = 0;
        $_where['g.on_off_line'] =1;
        $field = array(
            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3',
            'g.on_off_line','g.inventory','g.sort','g.main_img','g.price','g.discount_price','g.special_price',
            'g.buy_type','g.cash_back'
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
            $speGoodsList = page_query($model,$speWhere,$field,$order,$join,$group,$pageSize,$alias);
            $this -> speGoodsList = $speGoodsList['data'];
            //微团购
            $groupWhere['buy_type'] = array('eq',3);
            $groupWhere = array_merge($_where,$groupWhere);
            $groupGoodsList = page_query($model,$groupWhere,$field,$order,$join,$group,$pageSize,$alias);
            $this -> groupGoodsList = $groupGoodsList['data'];

        }else if($position === 'groupGoods'){
            //微团购
            $groupWhere['buy_type'] = array('eq',3);
            $groupWhere = array_merge($_where,$groupWhere);
            $groupGoodsList = page_query($model,$groupWhere,$field,$order,$join,$group,$pageSize,$alias);
                $this -> groupGoodsListMore = $groupGoodsList['data'];
        }else if($position === 'speGoods' && $page == 1){
            //产品工作室特惠
            $speWhere['buy_type'] = array('eq',2);
            $speWhere = array_merge($_where,$speWhere);
            $speGoodsList = page_query($model,$speWhere,$field,$order,$join,$group,$pageSize,$alias);
            $this -> speGoodsListMore = $speGoodsList['data'];
        }
        else{
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
                    $this->goodsListLoad = $goodsList['data'];
                }
            }

        }

        //商品分类切换到每个单独分类
        if(isset($_GET['catId']) && intval($_GET['catId'])){
            $where['category_id_1'] = $_GET['catId'];
            $where['buy_type'] = 1;
            $where = array_merge($_where,$where);
            $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);
            if($page == 1){
                $this->goodsList = $goodsList['data'];
            }else{
                if(empty($goodsList['data'])){
                    $this->ajaxReturn(errorMsg('没有更多'));
                }else{
                    $this->goodsListLoad = $goodsList['data'];
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
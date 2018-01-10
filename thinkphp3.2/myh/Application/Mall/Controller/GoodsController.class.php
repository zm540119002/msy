<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;
use  web\all\Lib\AuthUser;
use  web\all\Component\WxpayAPI\Wechat;
class GoodsController extends BaseController {
    //商品信息
    public function goodsInfo(){
        $modelGoods = D('Goods');
        $where = array(
            'g.status' => 0,
            'gb.on_off_line' => 1,
        );
        if(IS_POST){
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){
                $where['g.id'] = I('post.goodsId',0,'int');
            }
        }else{
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where['g.id'] = I('get.goodsId',0,'int');
            }
        }
        $field = array(
            'g.id','g.buy_type','g.sale_price','g.commission',
            'gb.no','gb.name','gb.single_specification','gb.package_num','gb.package_unit',
            'gb.purchase_unit','gb.price','gb.main_img',
        );
        $join = array(
            ' left join goods_base gb on g.goods_base_id = gb.id ',
        );
        $goodsInfo = $modelGoods->selectGoods($where,$field,$join);
        $this->goodsInfo = $goodsInfo[0];
        $this->display('goodsInfoTpl');
    }

    //商品列表
    public function goodsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelGoods = D('Goods');
        $where = array(
            'g.status' => 0,
            'gb.on_off_line' => 1,
        );
        if(isset($_GET['buyType']) && intval($_GET['buyType'])){
            $where['g.buy_type'] = I('get.buyType',0,'int');
        }
        $field = array(
            'g.id','g.buy_type','g.sale_price','g.commission','gb.name','gb.price','g.cash_back',
            'gb.main_img','gb.thumb_img','gb.single_specification','gb.headlines',
        );
        $join = array(
            ' left join goods_base gb on g.goods_base_id = gb.id ',
        );
        $group = "";
        $order = 'gb.sort';
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $this->currentPage = (isset($_GET['p']) && intval($_GET['p'])) ? I('get.p',0,'int') : 1;
        $goodsList = page_query($modelGoods,$where,$field,$order,$join,$group,$pageSize,$alias='g');
        $this->goodsList = $goodsList['data'];
        $templateType = I('get.templateType','','string');
        if($templateType=='photo'){
            $this ->display('goodsPhotoListTpl');
        }else if($templateType=='list'){
            $this ->display('goodsListTpl');
        }else if($templateType=='share'){
            $this ->display('goodsShareListTpl');
        }
    }

    //商品详情页
    public function goodsDetail(){
        $user = Authuser::check();
        if(IS_POST){
        }else{
            $modelGoods = D('Goods');
            $where = array(
                'g.status' => 0,
                'gb.on_off_line' => 1,
            );
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where['g.id'] = I('get.goodsId',0,'int');
            }
            $field = array(
                'g.id','g.buy_type','g.sale_price','g.commission','g.cash_back',
                'gb.no','gb.name','gb.price','gb.main_img','gb.single_specification','gb.param','gb.intro',
                'gb.usage','gb.notices','gb.detail_img','gb.share_intro','gb.package_unit','gb.headlines',
                'gb.group_share',
            );
            $join = array(
                ' left join goods_base gb on g.goods_base_id = gb.id ',
            );
            $goodsInfo = $modelGoods->selectGoods($where,$field,$join);
            $this->goodsInfo = $goodsInfo[0];
            //公共图片
            $modelCommonImg = D('CommonImages');
            $commonImg = $modelCommonImg->selectCommonImages();
            $this->commonImg = $commonImg[0]['common_img'];
            //购物车配置开启的项
            if($this->goodsInfo['buy_type'] == 2){
                $conf = array(2,8);
            }else{
                $conf = array(2,3,4);
            }
            if(isset($_GET['shareType'])&&!empty($_GET['shareType'])){
                $shareType = $_GET['shareType'];
                if($shareType == 'referrer'){//推客分享
                    $conf = array(9,10,11);
                }
                if($shareType == 'groupBuy'){//团购分享
                    $conf = array(12);
                }
            }
            $this->unlockingFooterCart = unlockingFooterCartConfig($conf);
            //微信分享
            $shareInfo = [];
            //获取当前url
            $currentLink = (is_ssl()?'https://':'http://').$this->host.$_SERVER['REQUEST_URI'];
            //分享的内容
            $shareInfo['title'] = $this->goodsInfo['name'];//分享的标题
            $shareInfo['shareImgUrl'] = $this->goodsInfo['main_img'];//分享的图片
            if(isset($_GET['shareType'])&&!empty($_GET['shareType'])){
                if($shareType == 'referrer'){//推客分享
                    $shareInfo['desc'] = $this->goodsInfo['share_intro'];//分享的简介
                    $shLinkBase = substr($currentLink,0,strrpos($currentLink,'/shareType'));
                    $shareInfo['shareLink'] = $shLinkBase.'/userId/'.$user['id'];//分享url
                    $shareInfo['backUrl'] = $currentLink;//分享完跳转的url
                }
                if($shareType == 'groupBuy'){//团购分享
                    $shareInfo['desc'] = $this->goodsInfo['group_share'];//分享的简介
                    $shLinkBase = substr($currentLink,0,strrpos($currentLink,'/shareType'));
                    $shareInfo['shareLink'] = $shLinkBase;//分享url
                    $shareInfo['backUrl'] = $currentLink;//分享完跳转的url
                }
            }
            if(isset($_GET['groupBuyId']) && !empty($_GET['groupBuyId'])){
                $this -> groupBuyId = $_GET['groupBuyId'];
            }
            $this -> shareInfo = $this -> weiXinShare($shareInfo);
            $modelComment = D('Comment');
            $this -> aveScore = round($modelComment -> avg('score'),1);//平均分数
            $this -> userCommentNum = $modelComment -> count();//多少用户评价
            //授权获取微信信息
            $wxUser = $this -> getOAuthWeiXinUserInfo();
            $this -> display();
        }
    }

    public function a(){
        $options = array(
 			'token'=>'tokenaccesskey', //填写你设定的key
    		'appid'=>'wx9eee7ee8c2ae57dc', //填写高级调用功能的app id
 			'appsecret'=>'00e0c9275fb24d6ca2a8dfe2a24cc2f6', //填写高级调用功能的密钥
 			'partnerid'=>'1234887902', //财付通商户身份标识
 			'partnerkey'=>'Pq8YLYz7llOp09v9KdeFZ373cey37Iub', //财付通商户权限密钥Key
 			'paysignkey'=>'' //商户签名密钥Key
        		);
 	     $weObj = new Wechat($options);
     
        $code = isset($_GET['code'])?$_GET['code']:'';
        $scope = 'snsapi_userinfo';
        if($code){
            $josn = $weObj -> getOauthAccessToken();
            if(!$josn){
                unset($_SESSION['wx_redirect']);
                return false;
            }
            $userInfo = $weObj -> getOauthUserinfo($josn);
            if(!$userInfo){
                return false;
            }else{
                return $userInfo;
            }
        }else{
            //开始获取
            if($scope == 'snsapi_userinfo'){
                $url = $this->host . $_SERVER['REQUEST_URI'];
                $_SESSION['wx_redirect'] = $url;
                print_r($url);exit;
            }else{
                $url = $_SESSION['wx_redirect'];
            }
            if(!$url){
                unset($_SESSION['wx_redirect']);
                return false;
            }
            $oauto_url = $weObj -> getOauthRedirect($url,"wxbase");
            print_r($oauto_url);exit;
            $this -> redirect($oauto_url);
        }
    }
}
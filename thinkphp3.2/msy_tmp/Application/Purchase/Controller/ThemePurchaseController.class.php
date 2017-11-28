<?php
namespace Purchase\Controller;
use Think\Controller;
use  Common\Lib\AuthUser;
class ThemePurchaseController extends BaseBaseController {
    //前台首页
    public function index(){
        $this->display();
    }
    /**
     *  主题采购列表页
     */
   public function themePurchaseList(){
      if(IS_GET){
          if(intval($_GET['categoryId']) ==0 ){
              $this -> error('分类数据有误');
          }
          if(intval($_GET['categoryId']) && isset($_GET['categoryId']) ){
              $where['category_id'] = intval($_GET['categoryId']);
              $firstThemeInfo = M('firstTheme') -> where($where)->find();
              $firstThemeInfo['intro']  = html_entity_decode($firstThemeInfo['intro']);
              $secondThemeList = M('secondTheme') -> where($where)->select();
              $this->firstThemeInfo =$firstThemeInfo;
              $this->secondThemeList =$secondThemeList;
          }
      }
      $this->display();
   }

    /**
     *  主题采购详情页
     */
    public function themePurchaseInfo(){
        if(IS_GET){
            $user = AuthUser::getSession();
            $this -> user = $user;
            if(isset($_GET['themeId']) && !intval($_GET['themeId'])){
               $this -> error('主题采购的ID有误');
            }
            $where['id'] = intval($_GET['themeId']);
            $secondThemeInfo = M('secondTheme') -> where($where)->find();
            $this -> secondThemeInfo =$secondThemeInfo;
            // 相关商品
            $goodsList = array();
            $goods = json_decode($secondThemeInfo['goods'],true);
            foreach ($goods as $k => $v){
                $goodsId = intval($v['goodsId']);
                $where['id'] = $goodsId;
                $goodsInfo = M('goods','','DB_CONFIG2') -> where($where) -> field('id,first_img,price,name') -> find();
                $goodsInfo['sale_price'] = $v['salePrice'];
                $goodsList[] = $goodsInfo;
            }
            $this -> goodsList =$goodsList;
            // 相关仪器
            $instrumentList = array();
            $instrument = json_decode($secondThemeInfo['instrument'],true);
            foreach ($instrument as $k => $v){
                $goodsId = intval($v['goodsId']);
                $where['id'] = $goodsId;
                $goodsInfo = M('goods','','DB_CONFIG2') -> where($where) -> field('id,first_img,price,name') -> find();
                $goodsInfo['sale_price'] = $v['salePrice'];
                $instrumentList[] = $goodsInfo;
            }
            $this -> instrumentList = $instrumentList;
            //图片
            $theoryImg1 = explode(',',$secondThemeInfo['theory_img_1']) ;
            $theoryImg2 = explode(',',$secondThemeInfo['theory_img_2']) ;
            $this -> theoryImg1 =$theoryImg1;
            $this -> theoryImg2 =$theoryImg2;
        }
       //购物车信息
        $cartList = D('cart') -> getCartList($user['id']);
        $cartInfo = D('cart') -> getCartInfo($cartList);
        if($cartInfo['count']){
            $this -> cartInfo = $cartInfo;
        }
        $cartIds = D('cart') -> getCartIds($user['id']);
        $this -> cartIds = $cartIds;
        $this->display();
    }
}
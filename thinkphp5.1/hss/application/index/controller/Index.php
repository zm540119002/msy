<?php
namespace app\index\controller;

// 前台首页
use think\Console;

class Index extends \common\controller\Base{
    /**
     * 促销列表，场景列表，商品列表 -ajax
     */
    public function index(){

        // 促销列表 7个
        $modelPromotion = new \app\index\model\Promotion();
        $condition =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['thumb_img','<>',''],
            ],
            'field'=>['id','name','thumb_img'],
            'order'=>['sort'=>'desc', 'id'=>'desc',],
            'limit'=>'7'
        ];
        $promotionList  = $modelPromotion->getList($condition);
        $this ->assign('promotionList',$promotionList);

        // 场景列表 11个
        $modelScene = new \app\index\model\Scene();
        $condition =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
            ],
            'field'=>['id','name','thumb_img','display_type'],
            'order'=>['sort'=>'desc', 'id'=>'asc',],
            'limit'=>'11'

        ];
        $sceneList  = $modelScene->getList($condition);
        $this ->assign('sceneList',$sceneList);

        // 众创空间用户
        $modelUser = new \app\index\model\UserTest();

        $condition = [
            'where' => [
                ['id','>',20]
            ],
            'limit' => 5,
        ];

        $list = $modelUser->getList($condition);
        $this->assign('spaceList',$list);

        // 广告
        $ads = [];
        $modelAdPositions = new \app\index\model\AdPositions();
        $ads['top'] = reset($modelAdPositions->getAds('index_top'));
        $ads['carousel'] = $modelAdPositions->getAds('index_carousel');
        $this->assign('ads',$ads);

        // 底部菜单，见配置文件custom.footer_menu
        $this->assign('currentPage',request()->controller().'/'.request()->action());

        return $this->fetch();
    }

    /**
     * 加盟店的首页
     */
    public function franchiseIndex()
    {
        if(request()->isAjax()){
        }else{
            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());
            return $this->fetch('franchise/index');
        }

    }

    // 购物车管理页面
    public function cartIndex(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            $unlockingFooterCart = unlockingFooterCartConfigTest([10,0,9]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn20');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn50');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
            $type = input('type');
            $this->assign('type',$type);
            if($type){
                // 底部菜单，见配置文件custom.footer_menu
                $this->assign('currentPage',request()->controller().'/'.request()->action());
            }
            return $this->fetch('cart/index');
        }
    }

    // 项目优势页
    public function superiority(){
        return $this->fetch();
    }

    // 开放日活动
    public function activity(){
        return $this->fetch();
    }
}
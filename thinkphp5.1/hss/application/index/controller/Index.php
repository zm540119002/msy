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

        $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
        array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
        array_push($unlockingFooterCart['menu'][1]['class'],'group_btn20');
        array_push($unlockingFooterCart['menu'][2]['class'],'group_btn25');
        array_push($unlockingFooterCart['menu'][3]['class'],'group_btn25');
        $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
        Cart::getCartTotalNum();
        return $this->fetch();
    }

    /**
     * 加盟店的首页
     */
    public function franchiseIndex()
    {
        if(request()->isAjax()){
        }else{
            $user = checkLogin();
            $this->assign('user',$user);
            if($user){
                $memberMode =  new \app\index\model\Member();
                $config =[
                    'where' => [
                        ['m.status', '=', 0],
                        ['m.user_id', '=', $user['id']],
                    ],'field'=>[
                        'm.id ','m.type',
                    ],
                ];
                $member = $memberMode->getInfo($config);
                $this->assign('member',$member);
                //自己提交的申请
                $modelCityPartner = new \app\index\model\CityPartner();
                $condition=[
                    'where'=>[
                        ['cp.status', '=', 0],
                        ['cp.user_id','=',$this->user['id']]
                    ], 'field'=>[
                        'cp.id','cp.province','cp.city','cp.company_name','cp.applicant',
                        'cp.mobile','cp.city_level','cp.earnest','cp.amount','cp.apply_status'
                    ]
                ];
                $selfApplyList = $modelCityPartner -> getList($condition);
                //申请中
                $apply = [];
                //已申请
                $applied = [];
                if($selfApplyList){
                    foreach ($selfApplyList as $selfapply){
                        if ($selfapply['apply_status']<6){
                            $apply[] = $selfapply;
                        }else{
                            $applied[] = $selfapply;
                        }
                    }
                }
                $this->assign('apply',$apply);
                $this->assign('applied',$applied);
            }
            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());
            return $this->fetch('franchise/index');
        }

    }

    /**
     * 首页
     */
    public function cityPartnerIndex(){
        if(request()->isAjax()){
        }else{
            $user = checkLogin();
            $this->assign('user',$user);
            if($user){
                $memberMode =  new \app\index\model\Member();
                $config =[
                    'where' => [
                        ['m.status', '=', 0],
                        ['m.user_id', '=', $user['id']],
                    ],'field'=>[
                        'm.id ','m.type',
                    ],
                ];
                $member = $memberMode->getInfo($config);
                $this->assign('member',$member);
            }
            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());
            return $this->fetch('city_partner/index');
        }
    }

    // 购物车管理页面
    public function cartIndex(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfigTest([10,0,9]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn20');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn40');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn40');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
            $unlockingFooterCart2 = unlockingFooterCartConfigTest([0,2,1,3]);
            array_push($unlockingFooterCart2['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart2['menu'][1]['class'],'group_btn20');
            array_push($unlockingFooterCart2['menu'][2]['class'],'group_btn25');
            array_push($unlockingFooterCart2['menu'][3]['class'],'group_btn25');
            $this->assign('unlockingFooterCart2',json_encode($unlockingFooterCart2));

            $type = input('type');
            $this->assign('type',$type);
            if($type){
                // 底部菜单，见配置文件custom.footer_menu
                $this->assign('currentPage',request()->controller().'/'.request()->action());
            }
            Cart::getCartTotalNum();
            return $this->fetch('cart/index');
        }
    }

    // 项目优势页
    public function superiority(){
        $this->assignStandardBottomButton([21,22]);

        return $this->fetch();
    }

    // 开放日活动
    public function activity(){
        return $this->fetch();
    }

    // 页面
    public function staticPage(){
        $html_name = input('page/s');

        $this->assignStandardBottomButton([21,22]);
        if($html_name){
            $view = strtolower($html_name);

        }else{
            $view = 'activity';
        }

        return $this->fetch($view);
    }

    // 项目优势页
    public function invited(){
        $this->assignStandardBottomButton([21]);

        return $this->fetch();
    }

    // 项目优势页
    public function recruit(){
        $this->assignStandardBottomButton([22]);

        return $this->fetch();
    }



}
<?php
namespace Myms\Controller;
use Common\Controller\BaseController;
use Common\Lib\AuthUser;
class ProjectController extends BaseController {
    //产品列表
    public function projectIndex(){
        if(IS_POST){

        }else{
            if(isset($_GET['categoryId']) && intval($_GET['categoryId'])){
                $this->categoryId = $_GET['categoryId'];
            }
            $where['parent_id_1'] = 0;
            $pCatList = M('project_category')->where($where)->select();
            $this -> pCatList = $pCatList;
            //购物车信息
            $this-> cartInfo = D('cart') -> getAllCartInfo();
            C('HTTP_CACHE_CONTROL',"no-cache, no-store, must-revalidate");
            $this->display();
        }
    }

    //闺蜜行列表
    public function projectGroup(){
        $where['status']  = array('eq',0);
        $where['group_price'] = array('gt',0);
        $this->groupGoodsList =  M('project')->where($where)->order('id desc')->select();
        C('HTTP_CACHE_CONTROL',"no-cache, no-store, must-revalidate");
        $this->display();
    }

    //商品详情页
    public function projectInfo(){
        if(IS_GET){
            $user = AuthUser::getSession();
            $this -> user = $user;
            $projectId   = intval($_GET['projectId']);
            if(!$projectId){
                $this->error('项目参数有误');
            }
            $buyType = $_GET['buyType'];
            $projectInfo = D('project') -> getProjectInfoByProjectId($projectId,$buyType);
            $projectInfo['explain_img']   = explode(',',$projectInfo['explain_img']);
            $projectInfo['flow_img']      = explode(',',$projectInfo['flow_img']);
            $projectInfo['detail_img']    = explode(',',$projectInfo['detail_img']);
            $this -> projectInfo     = $projectInfo;
            //项目产品
            $where['project_id'] = $projectId;
            $field = array(
                'pg.project_id','pg.goods_id','pg.goods_num','g.id','g.main_img','g.name',
            );
            $join = array(
                ' left join goods g on g.id = pg.goods_id ',
            );
            $projectGoods = M('project_goods')->alias('pg')->where($where)->field($field)->join($join)->select();
            $this -> projectGoods= $projectGoods;
            $this->cartList = D('cart') -> cartList();
            $this -> cartInfo = D('cart')->getAllCartInfo();
            C('HTTP_CACHE_CONTROL',"no-cache, no-store, must-revalidate");
            $this -> display();
        }
    }



    //商品列表
    public function projectList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $page = I('get.p',0,'int');
        $this->page = $page;
        //分页函数的参数
        $model = D('project');
        $_where['p.status'] =0;
        $_where['p.on_off_line'] =1;
        $field = array(
            'p.id','p.name','p.category_id_1','p.category_id_2','p.category_id_3',
            'p.on_off_line','p.inventory','p.sort','p.main_img','p.price','p.discount_price',
            'p.group_price',
        );
        $join = '';
        $order = 'p.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $alias='p';
        $position = I('get.position');

        //初始化首页
        if($position === 'index' && $page == 1){
            $where['parent_id_1'] = 0;
            $pCategoryList = D('project_category')->selectProjectCategory($where);
            $catProjectList = [];
            foreach ($pCategoryList as $v){
                $catWhere['p.category_id_1'] = $v['id'];
                $catWhere = array_merge($_where,$catWhere);
                $catInfo = '['.$v['name'].']'.$v['explain'];
                $projectList = page_query($model,$catWhere,$field,$order,$join,$group,$pageSize,$alias);
                if( !empty($projectList['data']) ){
                    $catProjectList[$catInfo] = $projectList;
                }
            }
            $this -> catProjectList = $catProjectList;

            //闺蜜行
            $groupWhere['p.group_price'] = array('gt',0);
            $groupWhere = array_merge($_where,$groupWhere);
            $this->groupProjectList = page_query($model,$groupWhere,$field,$order,$join,$group,$pageSize,$alias);


        }elseif($position ==='project_index' && $page == 1){
            $where['parent_id_1'] = 0;
            $pCategoryList = D('project_category')->selectProjectCategory($where);
            $catProjectList = [];
            foreach ($pCategoryList as $v){
                $catWhere['p.category_id_1'] = $v['id'];
                $catWhere = array_merge($_where,$catWhere);
                $catInfo = '['.$v['name'].']'.$v['explain'];
                $projectList = page_query($model,$catWhere,$field,$order,$join,$group,$pageSize,$alias);
                if( !empty($projectList['data']) ){
                    $catProjectList[$catInfo] = $projectList;
                }
            }
            $this -> catProjectList = $catProjectList;
        }else{
            if($page>1){
                $buyType = I('get.buyType',0,'int');
                if($buyType == 1){//正常价产品
                    $where['category_id_1'] = I('get.categoryId',0,'int');
                }
                if($buyType == 2){//工作室特惠产品
                    $where['special_price'] = array('gt',0);
                }
                if($buyType == 3){//微团
                    $where['group_price'] = array('gt',0);
                }
                $where = array_merge($_where,$where);
                $projectList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);
                if(empty($projectList['data'])){
                    $this->ajaxReturn(errorMsg('没有更多'));
                }else{
                    $this->projectListLoad = $projectList;
                }
            }

        }
        

        //商品分类切换到每个单独分类
        if(isset($_GET['catId']) && intval($_GET['catId'])){
            $where['category_id_1'] = $_GET['catId'];
            $projectList = $model->selectProject($where);
            $this->projectList = $projectList;
        }
        $this->display('projectList');
    }
    
}
<?php
namespace app\index\controller;

/**
 * 场景控制器
 */
class Scene extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new\app\index\model\Scene();
        $config=[
            'where'=>[
            ],
            'field'=>[
                'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort,g.trait'
            ],
            'order'=>[
                'sort'=>'desc',
                'line_num'=>'asc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.storeId') && (int)input('?get.storeId')){
            $config['where'][] = ['g.store_id', '=', input('get.storeId')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = $model -> pageQuery($config);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'store' ){//店铺产品列表
                return $this->fetch('list_tpl');
            }
        }
    }

    /**
     * 二级场景页 -场景(默认)
     * 需要同组的各场景的名，场景信息，场景下的商品，场景下的方案
     * 没有图片 暂时隐藏 后期待确定后再删除 code=1， detail_img.html 文件 sql 三处
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id) $this->error('此项目已下架');

            // 场景信息
            $model = new\app\index\model\Scene();
            $condition =[
                'field' => [
                    'ss.id','ss.name','ss.main_img','ss.intro','ss.tag_category','ss.display_type'
                ], 'where' => [
                    ['s.status', '=', 0],
                    ['s.shelf_status', '=', 3],
                    ['s.id', '=', $id],
                ], 'join' => [
                    ['scene ss ','s.tag_category=ss.tag_category','left']
                ],'order' => ['ss.sort desc'],

            ];

            $sceneList = $model->getList($condition);

            if(empty($sceneList)){
                $this->error('此场景已下架');
            }
            // 当前的场景
            $scene = [];
            foreach($sceneList as $v){
                if($v['id']==$id){
                    $scene = $v;
                    break;
                }
            }

            $scene['main_img'] = explode(',',(string)$scene['main_img']);
            //$scene['tag'] = explode(',',(string)$scene['tag']);
            $this->assign('sceneList',$sceneList);
            $this->assign('scene',$scene);


            // 获取场景下的方案  aj获取
            $modelSceneScheme = new \app\index\model\SceneScheme();
            $config = [
                'where' => [
                    ['ss.status', '=', 0],
                    ['ss.scene_id', '=', $id],
                    ['s.shelf_status', '=', 3],
                ],'field'=>[
                    's.id','s.name','s.thumb_img','ss.show_name'
                ],'join'=>[
                    ['scheme s','s.id = ss.scheme_id','left']
                ]
            ];

            $schemeList= $modelSceneScheme->getList($config);

            $this->assign('schemeList',$schemeList);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            $this->assign('relation',config('custom.relation_type.scene'));

            return $this->fetch('detail_img');
        }
    }

    /**
     * 二级场景页 -分类
     * 需要场景信息，场景下的商品分类，商品分类下的商品-ajax获取
     * 没有图片 暂时隐藏 后期待确定后再删除 code=1， sort.html 文件 sql 三处
     */
    public function sort(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            $cid= intval(input('cid'));
            if(!$id){
                $this->error('此项目已下架');
            }
            // 场景信息
            $model = new\app\index\model\Scene();
            $config =[
                'where' => [
                    ['status', '=', 0],
                    ['shelf_status', '=', 3],
                    ['id', '=', $id],
                ],
            ];
            $css = (input('css'));
            $this->assign('css',$css);
            $scene = $model->getInfo($config);
            if(empty($scene)){
                $this->error('此项目已下架');
            }
            // code=1
            //$scene['main_img'] = explode(',',(string)$scene['main_img']);
            $scene['tag'] = explode(',',(string)$scene['tag']);
            $this->assign('scene',$scene);

            // 场景下的商品分类
            $modelSceneGoodsCategory = new \app\index\model\SceneGoodsCategory();
            $config = [
                'where'  => [
                    ['gc.status', '=', 0],
                    ['sgc.scene_id', '=', $id],
                ],'field'=> [
                    'gc.id ','gc.name','gc.img',
                ],'join' => [
                    ['goods_category gc','gc.id = sgc.goods_category_id','left']
                ],'order'=> [
                    'gc.sort'=>'desc'
                ]
            ];
            $categoryList = $modelSceneGoodsCategory->getList($config);
            $this->assign('categoryList',$categoryList);

            // 选中的分类
            if(!$cid){
                $categoryInfo = reset($categoryList);
                $cid = $categoryInfo['id'];
            }
            $this->assign('cid',$cid);
            $this->assign('id',$id);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            return $this->fetch('sort_img');
        }
    }

    /**
     * 二级场景页 -只作为入口不关联场景
     * 项目信息4个暂定(名称，logo,商品-ajax获取，介绍，视频)
     */
    public function project(){
        if(request()->isAjax()){
        }else{
            // 项目信息 -4个
            $modelProject = new \app\index\model\Project();
            $config = [
                'where'  => [
                    ['status', '=', 0],
                    ['shelf_status', '=', 3],
                    ['audit', '=', 1],
                ],'field'=> [
                    'id ','name','thumb_img','main_img','intro','detail_img','create_time','update_time','video'
                ],'order'=> [
                    'sort'=>'desc'
                ],'limit' =>4,
            ];

            $projectList = $modelProject->getList($config);

            if (empty($projectList)) {
                $this->error('此项目已下架');
            }

            // 有项目id OR 默认项目id
            $project_id = intval(input('param.pid/d'));

            $project = array();
            if($project_id){
                foreach($projectList as $k => $v){
                    if ($v['id']==$project_id){
                        $project = $v;
                        $projectList[$k]['current'] = 'current';
                        break;
                    }
                }
            }

            if( empty($project) ){
                $project = reset($projectList);
                $projectList[key($projectList)]['current'] = 'current';
            }

            $this->assign('project',$project);
            $this->assign('projectList',$projectList);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            $this->assign('relation',config('custom.relation_type.project'));
            return $this->fetch();
        }
    }

}
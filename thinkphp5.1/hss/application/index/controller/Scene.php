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
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
  /*          $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][3]['class'],'group_btn30');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));*/

            $id = intval(input('id'));
            if(!$id) $this->error('此项目已下架');
            // 场景信息 主要是获取同组的场景信息
            $model = new\app\index\model\Scene();
            $condition =[
                'field' => [
                    'ss.id','ss.name','ss.main_img','ss.tag','ss.intro','ss.tag_category','ss.display_type','ss.title'
                ], 'where' => [
                    ['ss.status', '=', 0],
                    ['ss.shelf_status', '=', 3],
                    ['s.id', '=', $id],
                ], 'join' => [
                    ['scene ss ','s.tag_category=ss.tag_category','left']
                ],'order' => ['ss.sort desc'],

            ];

            $sceneList = $model->getList($condition);
            $this->assign('sceneList',$sceneList);
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

            scene_handle($scene);

            $this->assign('info',$scene);

            Promotion::displayPromotionList($id);

            $this->assign('relation',config('custom.relation_type.scene'));

            return $this->fetch();
        }
    }

    /**
     * 二级场景页 -分类
     * 需要场景信息，场景下的商品分类，商品分类下的商品-ajax获取
     * 没有图片 暂时隐藏 后期待确定后再删除 code=1， sort.html 文件 sql 三处
     */
    public function sort(){
        $id = intval(input('id'));
        if(!$id){
            $this->error('此项目已下架');
        }

        $this->displayScene($id);

        return $this->fetch();
    }

    /**
     * 场景信息页 -项目
     * @return mixed
     */
    public function project(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }

            $this->displayScene($id);

            return $this->fetch();
        }
    }

    // 输出场景信息
    private function displayScene($id){
        $model = new\app\index\model\Scene();
        $config =[
            'field' => [
                'id','name','thumb_img','main_img','intro','tag','tag_category','title'
            ],
            'where' => [
                ['status', '=', 0],
                ['shelf_status', '=', 3],
                ['id', '=', $id],
            ],
        ];
        $scene = $model->getInfo($config);
        if(empty($scene)){
            $this->error('此项目已下架');
        }

        scene_handle($scene);
/*        $scene['tag'] = explode('|',(string)$scene['tag']);
        $scene['main_img'] = explode(',',(string)$scene['main_img']);
        $scene['intro'] = $scene['intro'] ? htmlspecialchars_decode($scene['intro']) : $scene['intro'] ;*/

        $this->assign('info',$scene);
    }




}
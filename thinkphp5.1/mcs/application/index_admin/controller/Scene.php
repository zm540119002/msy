<?php
namespace app\index_admin\controller;

/**
 * 场景控制器基类
 */
class Scene extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Scene();
    }

    public function manage(){

        return $this->fetch('manage');
    }

    /**
     * (查询 :增加 OR 修改) OR 提交 做
     * @return array
     */
    public function edit(){

        $model = $this->obj;
        if(!request()->isPost()){
            if($id = input('param.id/d')){
                $condition = ['where' => [['id','=',$id]]];
                $info = $model->getInfo($condition);
                // 选中的店铺
                $info['belong_to'] = strrev(decbin($info['belong_to']));
                $this->assign('info',$info);
            }
            return $this->fetch();

        }
        else{
            // 基础处理
            if(!input('param.name/s')) return errorMsg('失败');

            if( isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.scene'),$_POST['thumb_img']);
            }
            if( isset($_POST['logo_img']) && $_POST['logo_img'] ){
                $_POST['logo_img'] = moveImgFromTemp(config('upload_dir.scene'),$_POST['logo_img']);
            }
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.scene'),$item);
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);

            }

            // 后面改进
            if( isset($_POST['type'])&&$_POST['type'] ){
                switch($_POST['type']){
                    case 2:$template = 'sort'   ;break;
                    case 3:$template = 'project';break;
                    default:
                        $template = 'detail';
                }
                $_POST['template'] = $template;
            }
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));
            $data = $_POST;
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定

            if(isset($_POST['id']) && $id=input('post.id/d')){ //修改

                // 编辑
                $condition = ['where' => ['id' => $id,]];

                $info  = $model->getInfo($condition);
                $result= $model->edit($data,$condition['where']);
                if(!$result['status']) return $result;

                //删除旧文件
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }
                if($info['logo_img']){
                    delImgFromPaths($info['logo_img'],$_POST['logo_img']);
                }
                if($info['background_img']){
                    delImgFromPaths($info['background_img'],$_POST['background_img']);
                }
                if($info['main_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$_POST['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }

            }
            else{//新增
                $data['create_time'] = time();
                $result = $model->edit($data);
                if(!$result['status']) return $result;

            }
            return successMsg('成功');
       }
    }

    /**
     * 单字段设置
     */
    public function setInfo(){
        if(!request()->isAjax() && !request()->isPost()){
            return config('custom.not_post');
        }

        if(!$id=input('post.id/d')) return errorMsg('失败');

        $info= array();
        // 上下架
        if ($shelf_status = input('post.shelf_status/d')){
            $shelf_status = $shelf_status==1 ? 3 : 1 ;

            $info = ['shelf_status'=>$shelf_status];
        }

        $rse  = $this->obj->where(['id'=>$id])->setField($info);

        if(!$rse) return errorMsg('失败');

        return successMsg('成功');
    }

    /**
     *  分页查询
     */
    public function getList(){

        $where[] = ['status','=',0];
        // 条件
        if(isset($_GET['type'])&&$type=input('get.type/d'))  $where[] = ['type','=',$type];
        if(isset($_GET['shelf_status'])&&$shelf_status=input('get.shelf_status/d'))  $where[] = ['shelf_status','=',$shelf_status];
        if(isset($_GET['belong_to'])&&$belong_to=input('get.belong_to/d'))  $where[] = ['belong_to','=',$belong_to];

        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];

        $condition = [
            'where'=>$where,
            'field'=>['id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','is_selection','type','belong_to','group'],
            'order'=>['sort'=>'desc', 'id'=>'desc',],
        ];

        $list = $this->obj->pageQuery($condition);

        $this->assign('list',$list);

        return view('list_tpl');

    }

    /**
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        if(!input('?post.id')&&!input('?post.ids'))  return errorMsg('失败');

        // 软删除
        $condition = array();
        $where     = array();
        if($id = input('post.id/d')){
            $condition = [
                'where' => [['id','=',$id]],
                /*'field' => [
                    'thumb_img','main_img','background_img','logo_img'
                ]*/
            ];
            $where = [['scene_id','=',$id]];
        }
        if($ids = input('post.ids/a')){
            $ids = input('post.ids/a');
            $condition = ['where' => [['id','in',$ids]],
                /*'field' => [
                    'thumb_img','main_img','background_img','logo_img'
                ]*/
            ];
            $where = [['scene_id','in',$ids]];
        }
        // 删除关联记录，暂时没有好的方法先这样做.
        $model = new \app\index_admin\model\SceneScheme();

        // 事务
        $model->startTrans();

        try {
            $model->del($where,false);
            $model = new \app\index_admin\model\SceneGoods();
            $model->del($where,false);
            $model = new \app\index_admin\model\SceneGoodsCategory();
            $model->del($where,false);
            $model = new \app\index_admin\model\Scene();
            $result= $model->del($condition['where']);

            $model->commit();
            return $result;

        } catch (\Exception $e) {
            // 回滚事务
            $model->rollback();
            return errorMsg('失败');
        }

        // 删除图片
/*        $list  = $model->getList($condition);
        $result= $model->del($condition['where'],false);
        if($result){
            //删除商品主图
            foreach($list as $k => $v){
                if($v){
                    delImg($v);
                }
            }
        }

        return $result;*/
    }

    // 管理类别下的商品
    public function manageRelationGoods(){

        // 获取所有的分类 -暂时先全部写在商品分类控制器里
        GoodsCategory::getGoodsCategory();
        $this->assign('relation',config('custom.relation_type.scene'));

        return $this->fetch();
    }

    /**
     * 添加场景下相关的项目
     * @return array|mixed
     * @throws \Exception
     */
    public function addSceneProject(){
        if(request()->isPost()){
            $model = new \app\index_admin\model\SceneGoods();
            $data = input('post.selectedIds/a');
            $condition = [
                ['scene_id','=',$data[0]['scene_id']]
            ];
            $model->startTrans();
            $rse = $model -> del($condition,$tag=false);

            if(false === $rse){
                $model->rollback();
                return errorMsg('失败');
            }
            $res = $model->allowField(true)->saveAll($data)->toArray();
            if (!count($res)) {
                $model->rollback();
                return errorMsg('失败');
            }
            $model -> commit();
            return successMsg('成功');

        }else{
            if(!input('?id') || !input('id/d')){
                $this ->error('参数有误',url('manage'));
            }
            // 所有商品分类
            $model = new \app\index_admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $model->getList($config);
            $this->assign('allCategoryList',$allCategoryList);

            $id = input('id/d');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    // 到时看下需不需要整合在一起 start
    /**
     * 场景下的商品分类
     */
    public function manageSceneGoodsCategory(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $sceneModel = new \app\index_admin\model\Scene();

        $condition = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $scene = $sceneModel->getInfo($condition);
        $this->assign('scene',$scene);

        $model = new \app\index_admin\model\SceneGoodsCategory();
        $condition = [
            'where'=>[
                ['gc.status','=',0],
                ['sgc.scene_id','=',$id],
            ],'field' => [
                'sgc.id','gc.name','gc.sort','gc.remark'
            ],'join'  => [
                ['goods_category gc','sgc.goods_category_id=gc.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];

        $sceneCategoryList = $model->getList($condition);
        $this->assign('sceneCategoryList',$sceneCategoryList);

        $this->assign('id',$id);

        return $this->fetch();
    }

    /**
     * 修改场景下的商品分类
     *
     */
    public function editSceneSort(){

        if(request()->isPost()){

            $cat_ids  = input('post.ids/a');
            $scene_id = input('post.scene_id/d');

            if (!$scene_id){
                $this ->error('参数有误',url('manage'));
            }

            if ($cat_ids){
                foreach($cat_ids as $k => $v){
                    if ((int)$v){
                        $data = ['scene_id'=>$scene_id,'goods_category_id'=>$v];

                        // 先删后增 -保证唯一
                        $model = new \app\index_admin\model\SceneGoodsCategory();
                        $model -> where($data)->delete();
                        $model -> allowField(true) -> save($data);
                    }
                }
            }
            return successMsg('成功');

        }else{

            // 后面的页面需要场景id
            if(!$id = input('param.id/d')){
                $this ->error('参数有误',url('manage'));
            }

            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 取消场景的商品分类
     */
    public function delSceneGoodsCategory(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id = input('post.id/d');

        if (!$id){
            return errorMsg('失败');
        }

        $model = new \app\index_admin\model\SceneGoodsCategory();

        $condition = [
            ['id','=',$id],
        ];

        return $model->del($condition,false);

    }

    /**
     * 场景下的方案
     */
    public function manageSceneScheme(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $sceneModel = new \app\index_admin\model\Scene();

        $condition = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $scene = $sceneModel->getInfo($condition);
        $this->assign('scene',$scene);

        $model = new \app\index_admin\model\SceneScheme();
        $condition = [
            'where'=>[
                ['ss.status','=',0],
                ['ss.scene_id','=',$id],
            ],'field' => [
                's.id scheme_id','s.name','s.thumb_img','s.sort','s.shelf_status','ss.id ','ss.show_name'
            ],'join'  => [
                ['scheme s','ss.scheme_id=s.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];
        $list = $model->pageQuery($condition);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 修改场景下的方案
     */
    public function editSceneScheme(){

        if(request()->isPost()){

            $ids  = input('post.ids/a');
            $scene_id = input('post.scene_id/d');

            if (!$scene_id){
                $this ->error('参数有误',url('manage'));
            }

            if ($ids){
                foreach($ids as $k => $v){
                    if ((int)$v){
                        $data = ['scene_id'=>$scene_id,'scheme_id'=>$v];

                        // 先删后增 -保证唯一
                        $model = new \app\index_admin\model\SceneScheme();
                        $model -> where($data)->delete();
                        $model -> allowField(true) -> save($data);
                    }
                }
            }
            return successMsg('成功');

        }else{

            // 后面的页面需要场景id
            if(!$id = input('param.id/d')){
                $this ->error('参数有误',url('manage'));
            }

            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 取消关联的方案
     */
    public function delSceneScheme(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id = input('post.id/d');

        if (!$id){
            return errorMsg('失败');
        }

        $model = new \app\index_admin\model\SceneScheme();

        $condition = [
            ['id','=',$id],
        ];

        return $model->del($condition,false);
    }

    // 到时看下需不需要整合在一起 end

    /**
     * 获取所有一级OR该分类的子级分类 && 所有已选择的分类
     */
    public function getCategory(){

        $scene_id= input('param.id/d');
        $cat_id  = input('param.cat_id/d');

        if (!$scene_id){
            $this ->error('参数有误',url('manage'));
        }

        if ($cat_id){
            $where = ['gc.parent_id_1', '=', $cat_id];
            $view  = 'list_child_tpl';

        }else{
            $where = ['gc.level','=',1];
            $view  = 'list_layer_tpl';

        }

        $goodsCategoryModel = new \app\index_admin\model\GoodsCategory();
        $config = [
            'where' => [
                ['status','=', 0],
            ],'field'=> [
                'id','name','level','parent_id_1','parent_id_2','remark','sort','img'
            ],'order'=> [
                'sort' => 'desc'
            ]
        ];
        $config['where'][] = $where;
        $list = $goodsCategoryModel->getlist($config);

        $sceneGoodsCategoryModel = new \app\index_admin\model\SceneGoodsCategory();
        $config = [
            'where' => [
                ['scene_id','=', $scene_id],
            ],'field'=> [
                'goods_category_id'
            ]
        ];
        $sceneCategoryList = $sceneGoodsCategoryModel->getlist($config);

        if ($sceneCategoryList){
            $sceneCategoryList = array_column($sceneCategoryList,'goods_category_id');
            // 取出交差值的数组
            foreach($list as $k => $v){
                if ( in_array($v['id'],$sceneCategoryList) ){
                    $list[$k]['scene'] = 1;
                }

            }
        }

        $this->assign('list',$list);
        $this->assign('sceneCategoryList',$sceneCategoryList);

        return view($view);
    }





}
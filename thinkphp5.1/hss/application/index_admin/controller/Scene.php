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
     * // 没有图片 暂时隐藏 后期待确定后再删除 code=1， edit_img.html 文件 sql 三处
     *
     * @return array
     */
    public function edit(){
        $model = $this->obj;

        if(!request()->isPost()){
            if($id = input('param.id/d')){
                $condition = [
                    'field' => [
                        'id','name','shelf_status','sort','thumb_img','main_img','intro','tag','display_type','type','tag_category','title','background_img'
                    ], 'where' => [
                        ['id','=',$id]
                    ],
                ];
                $info = $model->getInfo($condition);
                $info['intro'] = htmlspecialchars_decode($info['intro']);

                $this->assign('info',$info);
            }

            return $this->fetch();

        }
        else{

            // 基础处理
            if(!input('param.name/s')) return errorMsg('失败');

            if( isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.scheme'),$_POST['thumb_img']);
            }
/*            if( isset($_POST['background_img']) && $_POST['background_img'] ){
                $_POST['background_img'] = moveImgFromTemp(config('upload_dir.scheme'),$_POST['background_img']);
            }*/
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.scheme'),$item);
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);

            }

            $data = $_POST;
            $data['intro'] = htmlspecialchars(addslashes(input('intro/s')));
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定

            if(isset($_POST['id']) && $id=input('post.id/d')){ //修改

                // 编辑
                $condition = [
                    'where' => ['id' => $id,],
                    'field' => ['thumb_img,logo_img,background_img,main_img','intro']
                ];

                $info = $model->getinfo($condition);

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

        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];

        $condition = [
            'where'=>$where,
            'field'=>['id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','type','display_type'],
            'order'=>['id'=>'asc',],
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
     * 展示选中的促销方案
     */
    public function getScenePromotion(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $modelScene = new \app\index_admin\model\Scene();
        $condition = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $scene = $modelScene->getInfo($condition);
        $this->assign('scene',$scene);

        $model = new \app\index_admin\model\ScenePromotion();
        $condition = [
            'where'=>[
                ['sp.status','=',0],
                ['sp.scene_id','=',$id],
            ],'field' => [
                'p.id promotion_id','p.name','p.thumb_img','p.sort','p.shelf_status','sp.id '
            ],'join'  => [
                ['promotion p','sp.promotion_id=p.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];
        $list = $model->pageQuery($condition);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 关联促销方案
     */
    public function editScenePromotion(){

        if(request()->isPost()){

            $scene_id = input('scene_id/d');
            $ids  = input('ids/a');

            if (!$scene_id){
                $this ->error('参数有误',url('manage'));
            }

            if ($ids){
                foreach($ids as $k => $v){
                    if ((int)$v){
                        $data = ['scene_id'=>$scene_id,'promotion_id'=>$v];

                        // 先删后增 -保证唯一
                        $model = new \app\index_admin\model\ScenePromotion();
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
     * 获取所有促销方案&&已选中的
     * @return \think\response\View
     */
    public function getPromotionList(){
        $list = Promotion::getListData();

        // 其它业务 -标记已选中的
        if($scene_id = input('param.id/d')){
            $ModelScenePromotion = new \app\index_admin\model\ScenePromotion();
            $condition = [
                'where' => [
                    ['scene_id','=', $scene_id],
                ],'field'=> [
                    'promotion_id'
                ]
            ];
            $scenePromotion = $ModelScenePromotion->getlist($condition);

            if ($scenePromotion){
                $promotionIds = array_column($scenePromotion,'promotion_id');
                // 取出交差值的数组
                foreach($list as $k => $v){
                    if ( in_array($v['id'],$promotionIds) ){
                        $list[$k]['scene'] = 1;
                    }
                }
            }
        }

        $this->assign('list',$list);

        return view('/promotion/list_promotion_tpl');

    }

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

    /***************************** 下面的方法没有用到 **************************************/

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
     * 单字段设置 scene_scheme 表 暂时先放在这里
     */
    public function setSceneSchemeInfo(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id  = input('post.id/d');
        if (!$id){
            return errorMsg('失败');
        }

        $info= array();

        if ($show_name = input('post.show_name/d')){
            $show_name = $show_name==1 ? 2 : 1 ;

            $info = ['show_name'=>$show_name];
        }

        $model = new \app\index_admin\model\SceneScheme();
        $rse = $model->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    // 到时看下需不需要整合在一起 end







}
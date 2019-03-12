<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Scene extends Base {

    public function manage(){

        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
    public function edit(){

        $model = new \app\index_admin\model\Scene();
        if(request()->isPost()){
            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.weiya_scene'),basename($_POST['thumb_img']));
            }
            if(  isset($_POST['background_img']) && $_POST['background_img'] ){
                $_POST['background_img'] = moveImgFromTemp(config('upload_dir.weiya_scene'),basename($_POST['background_img']));
            }
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_scene'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);

            }
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));

            // 后面改进
            if(isset($_POST['type']) &&$_POST['type']){
                switch($_POST['type']){
                    case 2:$template = 'sort'   ;break;
                    case 3:$template = 'project';break;
                    default:
                        $template = 'detail';
                }
                $_POST['template'] = $template;
            }

            $data = $_POST;

            if(isset($_POST['id']) && intval($_POST['id'])){//修改

                $config = [
                    'where' => [
                        'id' => input('post.id',0,'int'),
                        'status' => 0,
                    ],
                ];
                $info = $model->getInfo($config);
                //删除就图片
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
                $where = [
                    'id'=>input('post.id',0,'int')
                ];
                $data['update_time'] = time();
                $result = $model -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }
            }else{//新增
                $data['create_time'] = time();
                $result = $model -> allowField(true) -> save($data);
                if(!$result){
                    $model ->rollback();
                    return errorMsg('失败');
                }

            }
            return successMsg('成功');
        }else{
            //要修改的商品
            if(input('?id') && (int)input('id')){
                $config = [
                    'where' => [
                        'status' => 0,
                        'id'=>input('id',0,'int'),
                    ],
                ];
                $info = $model->getInfo($config);

                // 选中的店铺
                $info['belong_to'] = strrev(decbin($info['belong_to']));

                $this->assign('info',$info);
            }

            return $this->fetch();
       }
    }

    /**
     *  分页查询
     */
    public function getList(){
        $model = new \app\index_admin\model\Scene();
        $where = [];
        $where[] = ['s.status','=',0];
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where[] = ['s.category_id_1','=',input('get.category_id_1',0,'int')];
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where[] = ['s.category_id_2','=',input('get.category_id_2',0,'int')];
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where[] = ['s.category_id_3','=',input('get.category_id_3',0,'int')];
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['s.name','like', '%' . trim($keyword) . '%'];
        }
        $config = [
            'where'=>$where,
            'field'=>[
                's.id','s.name','s.thumb_img','s.main_img','s.intro','s.shelf_status','s.sort','s.create_time','s.is_selection','s.type','s.belong_to'
            ],
            'order'=>[
                's.sort'=>'desc',
                's.id'=>'desc',
            ],
        ];

        $list = $model ->pageQuery($config);

        $info['belong_to'] = strrev(decbin($list['belong_to']));
        $this->assign('list',$list);
        if($_GET['pageType'] == 'manage'){
            return view('list_tpl');
        }
    }
    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Scene();
        $id = input('post.id/d');
        if(input('?post.id') && $id){
            $condition = [
                ['id','=',$id]
            ];
        }
        if(input('?post.ids')){
            $ids = input('post.ids/a');
            $condition = [
                ['id','in',$ids]
            ];
        }
        return $model->del($condition);
    }

    /**
     * 上下架
     */
    public function setShelfStatus(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Scene();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
            return errorMsg('失败');
        }
        $rse = $model->where(['id'=>input('post.id/d')])->setField(['shelf_status'=>input('post.shelf_status/d')]);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * 设置精选
     */
    public function setSelection(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Scene();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
            return errorMsg('失败');
        }
        $rse = $model->where(['id'=>input('post.id/d')])->setField(['is_selection'=>input('post.is_selection/d')]);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * 添加场景下相关的商品分类
     * 功能：查询 OR 提交
     * 查询：选择添加的分类 && 已有的分类
     * @return array|mixed
     * @throws \Exception
     */
    public function addSceneSort(){

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
            // 查询
       /*     if(!$id = input('id/d')){
                $this ->error('参数有误',url('manage'));
            }*/
       $id = 1;

            // 所有商品分类
            $model = new \app\index_admin\model\SceneGoodsCategory();
            $config = [
                'where'=>[
                    'gc.status'=>0
                ],'field' => [
                    'gc.name','gc.sort','gc.remark'
                ],'join' => [
                    ['goods_category gc','sgc.goods_category_id=gc.id','left']
                ]
            ];
            $sceneCategoryList = $model->getList($config);





            // 所有商品分类
/*            $model = new \app\index_admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $model->getList($config);

            $this->assign('allCategoryList',$allCategoryList);*/
            $this->assign('sceneCategoryList',$sceneCategoryList);

            $this->assign('id',$id);
            return $this->fetch();
        }
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

    /**
     * 添加场景下相关的商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addSceneGoods(){
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


    /**
     * 场景下的商品分类
     */
    public function manageSceneGoodsCategory(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $sceneModel = new \app\index_admin\model\Scene();

        $config = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $scene = $sceneModel->getInfo($config);
        $this->assign('scene',$scene);

        $model = new \app\index_admin\model\SceneGoodsCategory();
        $config = [
            'where'=>[
                ['gc.status','=',0],
            ],'field' => [
                'sgc.id','gc.name','gc.sort','gc.remark'
            ],'join'  => [
                ['goods_category gc','sgc.goods_category_id=gc.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];
        $sceneCategoryList = $model->getList($config);
        $this->assign('sceneCategoryList',$sceneCategoryList);

        $this->assign('id',$id);

        return $this->fetch();
    }

    /**
     * 获取所有一级OR该分类的子级分类-并勾选中已选择的分类
     */
    public function getCategory(){

        //$level= input('post.level/d');
        $id   = input('post.id/d');

        if ($id){
            $where = ['gc.parent_id_1', '=', $id];
            $view  = 'list_child_tpl';

        }else{
            $where = ['gc.level','=',1];
            $view  = 'list_layer_tpl';

        }

        $model = new \app\index_admin\model\GoodsCategory();
        $config = [
            'where' => [
                ['gc.status','=', 0],
            ],'field'=> [
                'gc.id','gc.name','gc.level','gc.parent_id_1','gc.parent_id_2','gc.remark','gc.sort','gc.img','sgc.id pid'
            ],'join' => [
                ['scene_goods_category sgc','gc.id=sgc.goods_category_id','left']
            ],'order'=> [
                'gc.sort' => 'desc'
            ]
        ];
        $config['where'][] = $where;

        $list = $model->getlist($config);
        $this->assign('list',$list);

        return view($view);
    }


    /**
     * 删除场景的商品分类
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

}
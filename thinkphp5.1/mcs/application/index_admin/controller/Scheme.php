<?php
namespace app\index_admin\controller;

/**
 * 方案类
 */
class Scheme extends Base {

    /*
     *审核首页
     */
    public function manage(){
        return $this->fetch('manage');
    }

    /**
     * (查询 :增加 OR 修改) OR 提交
     * @return array
     */
    public function edit(){

        if(!request()->isPost()){
            //要修改的商品
            if($id = input('param.id/d')){
                $model = new \app\index_admin\model\Scheme();
                $config = [
                    'where' => [
                        ['id','=',$id]
                    ],
                ];
                $info = $model->getInfo($config);

                // 选中的店铺
                $this->assign('info',$info);
            }
            return $this->fetch();

        }else{

            if(!input('param.name/s')){
                return errorMsg('失败');
            }

            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['thumb_img']));
            }
            $model = new \app\index_admin\model\Scheme();

            $where = array();
            $data  = $_POST;

            if($id = input('param.id/d')){
                $config = [
                    'where' => [
                        'id' => input('post.id',0,'int'),
                    ],
                ];
                $info = $model->getInfo($config);
                //删除商品主图
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }
                $where = ['id'=>$id];
            }

            $result = $model -> allowField(true) -> save($data,$where);

            if($result){
                return successMsg('成功');

            }else{
                $model ->rollback();
                return errorMsg('失败');
            }
        }
    }

    /**
     *  分页查询 -ajax
     */
    public function getList(){

        $modelProject = new \app\index_admin\model\Scheme();
        $where = [];
        //$where[] = ['audit','=',0];
/*        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where[] = ['p.category_id_1','=',input('get.category_id_1',0,'int')];
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where[] = ['p.category_id_2','=',input('get.category_id_2',0,'int')];
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where[] = ['p.category_id_3','=',input('get.category_id_3',0,'int')];
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['p.name','like', '%' . trim($keyword) . '%'];
        }*/
        $config = [
            //'where'=>$where,
            'field'=>[
                'id','name','thumb_img','sort','shelf_status'
            ],
            'order'=>[
                'sort'=>'desc',
            ],
        ];
        $list = $modelProject ->pageQuery($config);
        $this->assign('list',$list);
        //return $list;
        return view('list_tpl');

    }


    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Project();
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
        $model = new \app\index_admin\model\Project();
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
        $model = new \app\index_admin\model\Project();
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
     * 单值设置
     */
    public function setInfo(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id  = input('post.id/d');
        if (!$id){
            return errorMsg('失败');
        }

        $info= array();
        // 上下架
        if ($shelf_status = input('post.shelf_status/d')){
            $shelf_status = $shelf_status==1 ? 3 : 1 ;

            $info = ['shelf_status'=>$shelf_status];
        }

        $model = new \app\index_admin\model\Scheme();
        $rse = $model->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * 添加项目相关商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addProjectGoods(){
        if(request()->isPost()){
            $model = new \app\index_admin\model\ProjectGoods();
            $data = input('post.selectedIds/a');
            $condition = [
                ['project_id','=',$data[0]['project_id']]
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

}
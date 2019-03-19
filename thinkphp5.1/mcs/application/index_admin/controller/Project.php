<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Project extends Base {

    /*
     *审核首页
     */
    public function manage(){
        // 所有项目分类
//        $modelProjectCategory = new \app\index_admin\model\ProjectCategory();
//        $config = [
//            'where'=>[
//                'status'=>0
//            ]
//        ];
//        $allCategoryList = $modelProjectCategory->getList($config);
//        $this->assign('allCategoryList',$allCategoryList);

        //return $this->fetch('manage');
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
/*    public function edit(){
        $model = new \app\index_admin\model\Project();
        if(request()->isPost()){
            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['thumb_img']));
            }
            if(  isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['main_img']));
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',input('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            // 选中的店铺类型 十进制
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));

            $data = $_POST;
            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'where' => [
                        'id' => input('post.id',0,'int'),
                        'status' => 0,
                    ],
                ];
                $info = $model->getInfo($config);
                //删除商品主图
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }
                if($info['main_img']){
                    delImgFromPaths($info['main_img'],$_POST['main_img']);
                }
                if($info['detail_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$info['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
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
            if(input('?id') && $id = input('id/d')){
                $config = [
                    'where' => [
                        'status' => 0,
                        'id'=>$id,
                    ],
                ];
                $projectInfo = $model->getInfo($config);
                // 选中的店铺
                $projectInfo['belong_to'] = strrev(decbin($projectInfo['belong_to']));
                $this->assign('info',$projectInfo);
            }
            return $this->fetch();
       }
    }*/
    public function edit(){

        if(!request()->isPost()){

            //要修改的商品
            if(input('?id') && $id = input('id/d')){
                $config = [
                    'where' => [
                        'status' => 0,
                        'id'=>$id,
                    ],
                ];
                $model = new \app\index_admin\model\Project();
                $projectInfo = $model->getInfo($config);
                // 选中的店铺
                $projectInfo['belong_to'] = strrev(decbin($projectInfo['belong_to']));
                $this->assign('info',$projectInfo);
            }
            return $this->fetch();

        }else{
            // 增加 OR 编辑
            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['thumb_img']));
            }
            if(  isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['main_img']));
            }

            if( isset($_POST['video']) && $_POST['video'] ){
                $_POST['video'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['video']));
            }
            /*            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                            $detailArr = explode(',',input('post.detail_img','','string'));
                            $tempArr = array();
                            foreach ($detailArr as $item) {
                                if($item){
                                    $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($item));
                                }
                            }
                            $_POST['detail_img'] = implode(',',$tempArr);
                        }*/
            // 选中的店铺类型 十进制
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));

            $data = $_POST;
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定
            $model = new \app\index_admin\model\Project();

            if(isset($_POST['id']) && $id = input('post.id/d')){//修改
                $config = [
                    'where' => [
                        'id' => $id,
                        'status' => 0,
                    ],
                ];
                $info = $model->getInfo($config);

                $where = ['id'=>$id];
                $result = $model -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }
                //删除旧文件
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }
                if($info['main_img']){
                    delImgFromPaths($info['main_img'],$_POST['main_img']);
                }
                if($info['video']){
                    delImgFromPaths($info['video'],$_POST['video']);
                }
//                if($info['detail_img']){
//                    //删除商品详情图
//                    $oldImgArr = explode(',',$info['detail_img']);
//                    $newImgArr = explode(',',$_POST['detail_img']);
//                    delImgFromPaths($oldImgArr,$newImgArr);
//                }

            } else{
                //新增
                $data['create_time'] = time();
                $result = $model -> allowField(true) -> save($data);
                if(!$result){
                    return errorMsg('失败');
                }

            }
            return successMsg('成功');
       }
    }

    /**
     *  分页查询
     */
    public function getList(){

        $modelProject = new \app\index_admin\model\Project();
        //$where = [];
        $where[] = ['status','=',0];
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['name','like', '%' . trim($keyword) . '%'];
        }
        $condition = [
            'where'=>$where,
            'field'=>[
                'id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','is_selection','belong_to','video'
            ],
            'order'=>[
                'id'=>'desc',
                'sort'=>'desc',
            ],
        ];
        $list = $modelProject ->pageQuery($condition);
        $this->assign('list',$list);

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
        $condition = array();
        $where     = array();
        $id = input('post.id/d');
        if(input('?post.id') && $id){
            $condition = [
                ['id','=',$id]
            ];
            $where = [
                ['project_id','=',$id]
            ];
        }
        if(input('?post.ids')){
            $ids = input('post.ids/a');
            $condition = [
                ['id','in',$ids]
            ];
            $where = [
                ['project_id','in',$ids]
            ];
        }

        $model = new \app\index_admin\model\Project();

        // 事务
        $model->startTrans();

        try {
            $result= $model->del($condition);
            $model = new \app\index_admin\model\SceneProject();
            $model->del($where,false);

            $model->commit();

        } catch (\Exception $e) {
            // 回滚事务
            $model->rollback();
            return errorMsg('失败');
        }

        return $result;
    }

    /**
     * 单字段设置
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

        $model = new \app\index_admin\model\Project();
        $rse = $model->where(['id'=>$id])->setField($info);

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
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
        return $this->fetch();
    }

    /**
     * (查询 :增加 OR 修改) OR 提交
     * @return array
     */
    public function edit(){

        if(!request()->isPost()){
            //要修改的方案
            if($id = input('param.id/d')){
                $model = new \app\index_admin\model\Scheme();
                $config = [
                    'where' => [
                        ['id','=',$id]
                    ],
                ];
                $info = $model->getInfo($config);

                $this->assign('info',$info);
            }
            return $this->fetch();

        }
        else{

            if(!input('param.name/s')){
                return errorMsg('失败');
            }

            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['thumb_img']));
            }
            $model = new \app\index_admin\model\Scheme();

            $data  = $_POST;
            if($id = input('param.id/d')){
                // 编辑
                $config = [
                    'where' => [
                        'id' => input('post.id',0,'int'),
                    ],
                ];
                $info = $model->getInfo($config);
                $result = $model -> allowField(true) -> save($data,['id'=>$id]);
                if($result===false){
                    return errorMsg('失败');
                }
                //删除商品主图
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }

            }else{ // 增加
                $result = $model -> allowField(true) -> save($data);
                if(!$result){
                    return errorMsg('失败');
                }
            }

            return successMsg('成功');
        }
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

        $model = new \app\index_admin\model\Scheme();
        $rse = $model->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     *  分页查询 -ajax
     */
    public function getList(){

        $modelProject = new \app\index_admin\model\Scheme();
        //$where = [];
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
        $condition = [
            'where'=>[
                ['status','=',0]
            ],
            'field'=>[
                'id','name','thumb_img','sort','shelf_status'
            ],
            'order'=>[
                'sort'=>'desc',
                'id'=>'desc',
            ],
        ];
        $list = $modelProject ->pageQuery($condition);

        // 标记该场景下的方案
        if($scene_id = input('param.id/d')){
            $sceneSchemeModel = new \app\index_admin\model\SceneScheme();
            $condition = [
                'where' => [
                    ['scene_id','=', $scene_id],
                ],'field'=> [
                    'scheme_id'
                ]
            ];
            $sceneScheme = $sceneSchemeModel->getlist($condition);

            if ($sceneScheme){
                $schemeIds = array_column($sceneScheme,'scheme_id');
                // 取出交差值的数组
                foreach($list as $k => $v){
                    if ( in_array($v['id'],$schemeIds) ){
                        $list[$k]['scene'] = 1;
                    }
                }
            }
        }

        $this->assign('list',$list);
  
        $pageType = input('param.pageType/s');
        if( $pageType ){
            $view = $pageType;
        }else{
            $view = 'list_tpl';
        }

        return view($view);

    }

    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index_admin\model\Scheme();

        $condition = array();
        $where     = array();
        if($id=input('post.id/d')){
            $condition = [
                'where' => [
                    ['id','=',$id]
                ],'field' => [
                    'thumb_img'
                ]
            ];
            $where = [
                ['scheme_id','=',$id]
            ];
        }

        if(input('?post.ids')){
            $ids = input('post.ids/a');
            $condition = [
                'where' => [
                    ['id','in',$ids]
                ],'field' => [
                    'thumb_img'
                ]
            ];
            $where = [
                ['scheme_id','in',$ids]
            ];
        }

        $list = $model->getList($condition);

        // 事务
        $model->startTrans();

        try {
            $result= $model->del($condition['where']);
            $model = new \app\index_admin\model\SceneScheme();
            $model->del($where,false);

            $model->commit();

        } catch (\Exception $e) {
            // 回滚事务
            $model->rollback();
            return errorMsg('失败');
        }

        if($result){
            //删除商品的所有的图片
            foreach($list as $k => $v){
                if($v){
                    delImg($v);
                }
            }
        }

        return $result;
    }


}
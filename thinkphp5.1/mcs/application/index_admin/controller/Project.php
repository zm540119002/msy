<?php
namespace app\index_admin\controller;

/**
 * 项目控制器基类
 */
class Project extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Project();
    }

    public function manage(){
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 编辑
     */
    public function edit(){
        $model = $this->obj;

        if(!request()->isPost()){
            //要修改的方案
            if($id = input('param.id/d')){
                $condition = ['where' => [['id','=',$id]]];
                $info = $model->getInfo($condition);
                // 选中的店铺
                $info['belong_to'] = strrev(decbin($info['belong_to']));
                $this->assign('info',$info);
            }
            return $this->fetch();

        }else{
            // 基础处理
            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.project'),basename($_POST['thumb_img']));
            }
            if(  isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.project'),basename($_POST['main_img']));
            }

            if( isset($_POST['video']) && $_POST['video'] ){
                $_POST['video'] = moveImgFromTemp(config('upload_dir.project'),basename($_POST['video']));
            }
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));
            $data = $_POST;
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定

            if(isset($_POST['id']) && $id=input('post.id/d')){//修改
                // 编辑
                $condition = ['where' => ['id' => $id,]];

                $info  = $model->getInfo($condition);
                $result= $model->edit($data,$condition['where']);
                if(!$result['status']) return $result;

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

            } else{
                //新增
                $data['create_time'] = time();

                //$result = $model->allowField(true) -> save($data);

                return $model;
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
        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];

        $condition = [
            'where'=>$where,
            'field'=>['id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','is_selection','belong_to','video'],
            'order'=>['id'=>'desc', 'sort'=>'desc',],
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

        $condition = array();
        $where     = array();
        if($id = input('post.id/d')){
            $condition = [['id','=',$id]];
            $where = [['project_id','=',$id]];
        }
        if($ids = input('post.ids/a')){
            $condition = [['id','in',$ids]];
            $where = [['project_id','in',$ids]];
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

    // 管理类别下的商品
    public function manageRelationGoods(){
        // 暂时先全部写在商品分类里
        GoodsCategory::getGoodsCategory();
        $this->assign('relation',config('custom.relation_type.project'));

        return $this->fetch();
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


}
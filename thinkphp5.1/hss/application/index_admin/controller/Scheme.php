<?php
namespace app\index_admin\controller;

/**
 * 方案控制器
 */
class Scheme extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Scheme();
    }

    public function manage(){
        return $this->fetch();
    }

    /**
     * (查询 :增加 OR 修改) OR 提交
     * @return array
     */
    public function edit(){
        $model = $this->obj;

        if(!request()->isPost()){
            //要修改的方案
            if($id = input('param.id/d')){
                $condition = ['where' => [['id','=',$id]]];
                $info = $model->getInfo($condition);
                $this->assign('info',$info);
            }
            return $this->fetch();

        }
        else{
            // 基础处理
            if(!input('param.name/s')) return errorMsg('失败');

            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.scheme'),basename($_POST['thumb_img']));
            }

            $data  = $_POST;
            if($id = input('param.id/d')){
                // 编辑
                $condition = ['where' => ['id' => $id,]];

                $info  = $model->getInfo($condition);
                $result= $model->edit($data,$condition['where']);
                if(!$result['status']) return $result;

                // 删除旧文件
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$_POST['thumb_img']);
                }

            }else{
                // 增加
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
     *  分页查询 -ajax
     */
    public function getList(){

        $where[] = ['status','=',0];
        // 条件
        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];

        $condition = [
            'where'=>$where,
            'field'=>['id','name','remark','thumb_img','sort','shelf_status'],
            'order'=>['sort'=>'desc', 'id'=>'desc',],
        ];
        $list = $this->obj->pageQuery($condition);

        // 其它业务 -标记场景下的方案
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
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        if(!input('?post.id')&&!input('?post.ids'))  return errorMsg('失败');

        $condition = array();
        $where     = array();
        if($id=input('post.id/d')){
            $condition = [
                'where' => [['id','=',$id]],
                //'field' => ['thumb_img']
            ];
            $where = [['scheme_id','=',$id]];
        }

        if($ids = input('post.ids/a')){
            $condition = [
                'where' => [['id','in',$ids]],
                //'field' => ['thumb_img']
            ];
            $where = [['scheme_id','in',$ids]];
        }

        $model = new \app\index_admin\model\Scheme();
        //$list = $model->getList($condition);

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

/*        if($result){
            //删除商品的所有的图片
            foreach($list as $k => $v){
                if($v){
                    delImg($v);
                }
            }
        }*/

        return $result;
    }


}
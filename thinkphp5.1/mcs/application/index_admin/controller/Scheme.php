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

            if($result===false){
                $model ->rollback();
                return errorMsg('失败');

            }else{
                return successMsg('成功');
            }
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
        $config = [
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
        $list = $modelProject ->pageQuery($config);
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
        if($id=input('post.id/d')){
            $condition = [
                'where' => [
                    ['id','=',$id]
                ],'field' => [
                    'thumb_img'
                ]
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
        }

        $list = $model->getList($condition);
        $result = $model->del($condition['where']);

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
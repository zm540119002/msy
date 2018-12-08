<?php
namespace app\admin\controller;

/**供应商验证控制器基类
 */
class Project extends Base {

    /*
     *审核首页
     */
    public function manage(){
        // 所有项目分类
        $modelProjectCategory = new \app\admin\model\ProjectCategory();
        $config = [
            'where'=>[
                'status'=>0
            ]
        ];
        $allCategoryList = $modelProjectCategory->getList($config);
        $this->assign('allCategoryList',$allCategoryList);
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
    public function edit(){
        $model = new \app\admin\model\Project();
        if(request()->isPost()){
            if( isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($_POST['thumb_img']));
            }
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);
            }
            $data = $_POST;
            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'g.id' => input('post.id',0,'int'),
                    'g.status' => 0,
                ];
                $goodsInfo = $model->getInfo($config);
                //删除商品主图
                if($goodsInfo['thumb_img']){
                    delImgFromPaths($goodsInfo['thumb_img'],$_POST['thumb_img']);
                }
                if($goodsInfo['main_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['main_img']);
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
           // 所有项目分类
            $modelProjectCategory = new \app\admin\model\ProjectCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $modelProjectCategory->getList($config);
            $this->assign('allCategoryList',$allCategoryList);
            //要修改的商品
            if(input('?id') && (int)input('id')){
                $config = [
                    'where' => [
                        'g.status' => 0,
                        'g.id'=>input('id',0,'int'),
                    ],
                ];
                $projectInfo = $model->getInfo($config);
                $this->assign('projectInfo',$projectInfo);
            }
            return $this->fetch();
       }
    }

    /**
     *  分页查询
     */
    public function getList(){
        $modelProject = new \app\admin\model\Project();
        $where = [];
        $where[] = ['p.status','=',0];
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
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
        }
        $config = [
            'where'=>$where,
            'field'=>[
                'p.id','p.name','p.thumb_img','p.main_img','p.intro','p.shelf_status','p.sort','p.create_time','p.category_id_1',
            ],
            'order'=>[
                'p.id'=>'asc',
                'p.sort'=>'desc',
            ],
        ];
        $list = $modelProject ->pageQuery($config);
        $this->assign('list',$list);
        if($_GET['pageType'] == 'manage'){
            return view('project/list_tpl');
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
        $model = new \app\admin\model\Project();
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

}
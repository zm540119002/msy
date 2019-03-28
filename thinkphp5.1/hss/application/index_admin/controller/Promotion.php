<?php
namespace app\index_admin\controller;

/**
 * 促销控制器基类
 */
class Promotion extends Base {

    public function manage(){

        return $this->fetch('manage');
    }

    /**
     * (查询 :增加 OR 修改) OR 提交 做
     * @return array
     */
    public function edit(){

        $model = new \app\index_admin\model\Scene();
        if(!request()->isPost()){
            if($id = input('param.id/d')){
                $model = new \app\index_admin\model\Scene();
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

        }
        else{

            if(!input('param.name/s')){
                return errorMsg('失败');
            }

            if(  isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = moveImgFromTemp(config('upload_dir.scheme'),basename($_POST['thumb_img']));
            }
            if(  isset($_POST['background_img']) && $_POST['background_img'] ){
                $_POST['background_img'] = moveImgFromTemp(config('upload_dir.scheme'),basename($_POST['background_img']));
            }
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.scheme'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);

            }

            $data = $_POST;
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定

            if(isset($_POST['id']) && $id=input('post.id/d')){ //修改

                $config = [
                    'where' => ['id' => $id,],
                ];

                $where = [
                    'id'=>$id
                ];
                $result = $model -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }

                $info = $model->getInfo($config);
                //删除旧图片
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
        if(!request()->isAjax() && !request()->isPost()){
            return config('custom.not_post');
        }

        if (!$id  = input('post.id/d')){
            return errorMsg('失败');
        }

        $info= array();
        // 上下架
        if ($shelf_status = input('post.shelf_status/d')){
            $shelf_status = $shelf_status==1 ? 3 : 1 ;

            $info = ['shelf_status'=>$shelf_status];
        }

        $model = new \app\index_admin\model\Promotion();
        $rse = $model->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     *  分页查询
     */
    public function getList(){
        $model = new \app\index_admin\model\Promotion();
        $where = [];
        $where[] = ['status','=',0];
        // 上下架
        if(isset($_GET['shelf_status']) && intval($_GET['shelf_status'])){
            $where[] = ['shelf_status','=',input('get.shelf_status',0,'int')];
        }

        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['name','like', '%' . trim($keyword) . '%'];
        }

        $config = [
            'where'=>$where,
            'field'=>[
                'id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','is_selection'
            ],
            'order'=>[
                'sort'=>'desc',
                'id'=>'desc',
            ],
        ];

        $list = $model ->pageQuery($config);

        $this->assign('list',$list);

        return view('list_tpl');

    }

    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isAjax() && !request()->isPost()){
            return config('custom.not_post');
        }

        if(!input('?post.id') && !input('?post.id')){
            return errorMsg('失败');
        }

        if($id = input('post.id/d')){
            $condition = ['where' => [['id','=',$id]],];
            $where     = [['promotion_id','=',$id]];

        }else{
            $ids = input('post.ids/a');
            $condition = ['where' => [['id','in',$ids]],];
            $where     = [['promotion_id','in',$ids]];
        }

        // 删除关联记录，暂时没有好的方法先这样做.
        $model = new \app\index_admin\model\PromotionGoods();

        // 事务
        $model->startTrans();

        try {
            $model->del($where,false);
            $model = new \app\index_admin\model\Promotion();
            $result= $model->del($condition['where']);

            $model->commit();
            return $result;

        } catch (\Exception $e) {
            // 回滚事务
            $model->rollback();
            return errorMsg('失败');
        }
    }

    /**
     * 添加促销商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addGoods(){
        if(request()->isPost()){
            $model = new \app\index_admin\model\PromotionGoods();
            $data = input('post.selectedIds/a');
            $condition = [
                ['promotion_id','=',$data[0]['promotion_id']]
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

            $id = input('id/d');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }


}
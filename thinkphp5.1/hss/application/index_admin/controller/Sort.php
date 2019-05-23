<?php
namespace app\index_admin\controller;

/**
 * 项目品类控制器
 * Class Category
 * @package app\index_admin\controller
 */
class Sort extends Base
{
    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Sort();
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
                $info['intro'] = htmlspecialchars_decode($info['intro']);

                $this->assign('info',$info);
            }
            return $this->fetch();

        }else{
            // 基础处理
            $data = input('post.');
            unset($data['editorValue']);

            replace_splitter($data,['tag']);
            process_upload_files($data,['thumb_img'],'sort',false);
            process_upload_files($data,['main_img','process_img','detail_img'],'sort');
            htmlspecialchars_addslashes($data,['intro']);

            $data['title'] = $data['name'];
            $data['update_time'] = time();
            $data['audit'] = 1; // 暂时没有审核，先固定

            if(isset($data['id']) && $id=input('post.id/d')){//修改
                // 编辑
                $condition = ['where' => ['id' => $id,]];

                $info  = $model->getInfo($condition);
                $result= $model->edit($data,$condition['where']);
                if(!$result['status']) return $result;

                //删除旧文件
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$data['thumb_img']);
                }
                if($info['main_img']){
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$data['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                if($info['process_img']){
                    $oldImgArr = explode(',',$info['process_img']);
                    $newImgArr = explode(',',$data['process_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                if($info['detail_img']){
                    $oldImgArr = explode(',',$info['process_img']);
                    $newImgArr = explode(',',$data['process_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }


            } else{
                //新增
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
        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];

        $condition = [
            'where'=>$where,
            'field'=>['id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','is_selection'],
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
            $where = [['sort_id','=',$id]];
        }
        if($ids = input('post.ids/a')){
            $condition = [['id','in',$ids]];
            $where = [['sort_id','in',$ids]];
        }
        $model = new \app\index_admin\model\Sort();

        // 事务
        $model->startTrans();

        try {
            $result= $model->del($condition);
            $model = new \app\index_admin\model\SortPromotion();
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
        $this->assign('relation',config('custom.relation_type.sort'));

        return $this->fetch();
    }

    // 促销系列方法
    /**
     * 展示选中的促销方案
     */
    public function getSortPromotion(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $modelProject = new \app\index_admin\model\Sort();
        $condition = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $project = $modelProject->getInfo($condition);

        $this->assign('info',$project);

        $model = new \app\index_admin\model\SortPromotion();
        $condition = [
            'where'=>[
                ['sp.status','=',0],
                ['sp.sort_id','=',$id],
            ],'field' => [
                'p.id promotion_id','p.name','p.thumb_img','p.sort','p.shelf_status','sp.id '
            ],'join'  => [
                ['promotion p','sp.promotion_id=p.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];

        $list = $model->pageQuery($condition);
/*        p($model->getLastSql());
        exit;*/
        $this->assign('list',$list);
        $this->assign('relation',config('custom.relation_type.sort'));

        return $this->fetch();
    }

    /**
     * 关联促销方案
     */
    public function editSortPromotion(){

        if(request()->isPost()){

            $id = input('id/d');
            $promotion_ids  = input('promotion_ids/a');

            if (!$id){
                $this ->error('参数有误',url('manage'));
            }

            if ($promotion_ids){
                foreach($promotion_ids as $k => $v){
                    if ((int)$v){
                        $data = ['sort_id'=>$id,'promotion_id'=>$v];

                        // 先删后增 -保证唯一
                        $model = new \app\index_admin\model\SortPromotion();
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
    public function _getPromotionList(){
        $list = Promotion::getListData();

        // 其它业务 -标记已选中的
        if($id = input('param.id/d')){
            $Model= new \app\index_admin\model\SortPromotion();
            $condition = [
                'where' => [
                    ['sort_id','=', $id],
                ],'field'=> [
                    'promotion_id'
                ]
            ];
            $promotionList = $Model->getlist($condition);

            if ($promotionList){
                $promotionIds = array_column($promotionList,'promotion_id');
                // 取出交差值的数组
                foreach($list as $k => $v){
                    if ( in_array($v['id'],$promotionIds) ){
                        $list[$k]['exist'] = 1;
                    }
                }
            }
        }

        $this->assign('list',$list);

        return view('/promotion/list_promotion_tpl');

    }
    // 促销系列方法END

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
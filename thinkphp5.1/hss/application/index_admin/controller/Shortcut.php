<?php
namespace app\index_admin\controller;

/**
 * 广告控制器基类
 * ad,advertising,advert 单词 广告插件会拦截
 */
class Shortcut extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList,del'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Shortcut();
    }

    public function manage(){
        $id = input('id/d');

        if(!$id){
            $this->errorMsg('参数错误');
        }

        $this->assign('pid',$id);
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 编辑
     */
    public function edit(){
        $model = $this->obj;
        $pid = input('param.pid/d');
        $this->assign('pid',$pid);

        if(!$pid){
            $this->errorMsg('参数错误');
        }

        if(!request()->isPost()){
            //要修改的方案
            if($id = input('param.id/d')){

                $condition = ['where' => [['id','=',$id]]];
                $info = $model->getInfo($condition);

                $this->assign('info',$info);

            }
            return $this->fetch();

        }else{
            // 基础处理
            $data = input('post.');
            process_upload_files($data,['thumb_img'],'shortcut',false);
            htmlspecialchars_addslashes($data,['ad_link']);

            $data['update_time'] = time();
            $data['ad_position_id'] = input('pid/d');
            $data['sort'] = input('sort/d');

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

        $pid = input('id/d');

        if(!$pid){
            $this->errorMsg('参数错误');
        }

        $condition = [
            'field' => ['id','name','sort','shelf_status'],
            'where' => [
                ['ad_position_id','=',$pid],
                ['status','=',0],
            ],
        ];

        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'] = ['name','like', '%' . trim($keyword) . '%'];

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

        if($id = input('post.id/d')){
            $condition = [['id','=',$id]];
        }
        if($ids = input('post.ids/a')){
            $condition = [['id','in',$ids]];
        }

        $result= $this->obj->del($condition);

        return $result;
    }

    // 管理类别下的商品
    public function manageRelationGoods(){
        // 暂时先全部写在商品分类里
        GoodsCategory::getGoodsCategory();
        $this->assign('relation',config('custom.relation_type.project'));

        return $this->fetch();
    }

    // 促销系列方法
    /**
     * 展示选中的促销方案
     */
    public function getProjectPromotion(){
        // 查询
        if(!$id = input('id/d')){
            $this ->error('参数有误',url('manage'));
        }
        $modelProject = new \app\index_admin\model\Project();
        $condition = [
            'where'=>[
                ['id','=',$id],
            ],'field'=>[
                'id','name'
            ]
        ];
        $project = $modelProject->getInfo($condition);

        $this->assign('info',$project);

        $model = new \app\index_admin\model\ProjectPromotion();
        $condition = [
            'where'=>[
                ['pp.status','=',0],
                ['pp.project_id','=',$id],
            ],'field' => [
                'p.id promotion_id','p.name','p.thumb_img','p.sort','p.shelf_status','pp.id '
            ],'join'  => [
                ['promotion p','pp.promotion_id=p.id','left']
            ],'order' => [
                'sort'=> 'desc'
            ]
        ];
        $list = $model->pageQuery($condition);
        $this->assign('list',$list);
        $this->assign('relation',config('custom.relation_type.project'));

        return $this->fetch();
    }

    /**
     * 关联促销方案
     */
    public function editProjectPromotion(){

        if(request()->isPost()){

            $id = input('id/d');
            $promotion_ids  = input('promotion_ids/a');

            if (!$id){
                $this ->error('参数有误',url('manage'));
            }

            if ($promotion_ids){
                foreach($promotion_ids as $k => $v){
                    if ((int)$v){
                        $data = ['project_id'=>$id,'promotion_id'=>$v];

                        // 先删后增 -保证唯一
                        $model = new \app\index_admin\model\ProjectPromotion();
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
            $Model= new \app\index_admin\model\ProjectPromotion();
            $condition = [
                'where' => [
                    ['project_id','=', $id],
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
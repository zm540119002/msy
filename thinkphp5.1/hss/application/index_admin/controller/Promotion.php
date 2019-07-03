<?php
namespace app\index_admin\controller;

/**
 * 促销控制器基类
 */
class Promotion extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,setInfo,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\Promotion();
    }

    public function manage(){

        return $this->fetch('manage');
    }

    /**
     * (查询 :增加 OR 修改) OR 提交 做
     * @return array
     */
    public function edit(){
        $model = $this->obj;

        if(!request()->isPost()){
            if($id = input('param.id/d')){
                $condition = [
                    'field' => [
                        'id','name','shelf_status','sort','thumb_img','main_img','intro','tag','title','background_img','retail_price','franchise_price',
                        'agent_price','remarks','belong_to_member_buy','is_company_info','upgrade_member_level','share_title','share_desc'
                    ], 'where' => [
                        ['id','=',$id]
                    ],
                ];

                $info = $model->getinfo($condition);

                $condition = [
                    'field'   => [
                        'pg.goods_id','pg.goods_num','g.name goods_name'
                    ], 'where'=> [
                        ['promotion_id','=',$info['id']],
                    ],'join'  => [
                        ['goods g','pg.goods_id=g.id','left']
                    ],
                ];

                $modelPromotionGoods = new \app\index_admin\model\PromotionGoods();
                $data = $modelPromotionGoods->getList($condition);

                $info['intro'] = htmlspecialchars_decode($info['intro']);
                $info['belong_to_member_buy'] = strrev(decbin($info['belong_to_member_buy']));
                $info['goods_list'] = addslashes(json_encode($data));

                $this->assign('info',$info);
            }
            $this->assign('relation',config('custom.relation_type.promotion'));

            return $this->fetch();

        }
        else{
            // 基础处理
            if(!input('param.name/s')) return errorMsg('失败');

            $data = input('post.');
            unset($data['editorValue']);

            replace_splitter($data,['tag']);
            process_upload_files($data,['thumb_img'],'promotion',false);
            process_upload_files($data,['main_img'],'promotion');
            htmlspecialchars_addslashes($data,['intro']);

            $data['title'] = $data['name'];
            $data['update_time'] = time();
            $data['remarks'] = trim($data['remarks']);
            $data['audit'] = 1; // 暂时没有审核，先固定
            $data['belong_to_member_buy'] = bindec(strrev(implode(input('post.belong_to_member_buy/a'))));

            if(isset($_POST['id']) && $id=input('post.id/d')){ //修改

                // 编辑
                $condition = [
                    'where' => ['id' => $id,],
                    'field' => ['thumb_img,logo_img,background_img,main_img','intro']
                ];

                $info = $model->getinfo($condition);

                $id= $model->edit($data,$condition['where']);
                if(!$id) return errorMsg();

                //删除旧文件
                if($info['thumb_img']){
                    delImgFromPaths($info['thumb_img'],$data['thumb_img']);
                }
                if($info['logo_img']){
                    delImgFromPaths($info['logo_img'],$data['logo_img']);
                }
                if($info['background_img']){
                    delImgFromPaths($info['background_img'],$data['background_img']);
                }
                if($info['main_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$data['main_img']);

                    delImgFromPaths($oldImgArr,$newImgArr);
                }

            }
            else{//新增
                $data['create_time'] = time();
                $id = $model->edit($data);
                if(!$id) return errorMsg();

            }

            // 更新关联商品
            if($goods_ids=input('post.goods_id/a')){

                $goods_ids = explode(',',$goods_ids[0]);

                $goods_num = input('post.goods_num/a');
                $goods_num = $goods_num[0] ? explode(',',$goods_num[0]) : [];
                $goods_list = [];

                foreach($goods_ids as $k => $v){
                    $goods_list[$k]['promotion_id'] = $id;
                    $goods_list[$k]['goods_id']     = $v;
                    $goods_list[$k]['goods_num']    = $goods_num[$k] ? $goods_num[$k] : 1 ;
                }

                $model = new \app\index_admin\model\PromotionGoods();
                $condition = [['promotion_id','=',$id]];

                $model -> del($condition,false);
                $model->allowField(true)->editAll($goods_list)->toArray();
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

        $list = $this->getListData();

        $this->assign('list',$list);

        $view = 'list_tpl';
        return view($view);
    }

    /**
     * 其它的场景里用到
     */
    public function getListData(){
        $model = new \app\index_admin\model\Promotion();
        $where = [];
        $where[] = ['status','=',0];
        // 上下架
        if(isset($_GET['shelf_status']) && intval($_GET['shelf_status'])){
            $where[] = ['shelf_status','=',input('get.shelf_status',0,'int')];
        }

        $keyword = input('get.keyword/s');
        if($keyword) $where[] = ['name','like', '%' . trim($keyword) . '%'];


        $config = [
            'where'=>$where,
            'field'=>['id','name','thumb_img','main_img','intro','shelf_status','sort','create_time','retail_price','franchise_price'],
            'order'=>['id'=>'asc',],
        ];

        return  $model->pageQuery($config);
    }



    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isAjax() && !request()->isPost()){
            return config('custom.not_post');
        }

        if(!input('?post.id')&&!input('?post.ids'))  return errorMsg('失败');

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

    // 管理类别下的商品
    public function manageRelationGoods(){
        // 暂时先全部写在商品分类里
        GoodsCategory::getGoodsCategory();
        $this->assign('relation',config('custom.relation_type.promotion'));

        return $this->fetch();
    }


    /**
     * 取消关联的信息 scene_promotion,goods_category_promotion,project_promotion
     */
    public function delRelationPromotion(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id = input('post.id/d');

        if (!$id){
            return errorMsg('失败');
        }

        $relation = input('post.relation/d');

        // custom.php relation_type
        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index_admin\model\ScenePromotion();
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index_admin\model\ProjectPromotion();
                break;
            case config('custom.relation_type.sort'):
                $model = new \app\index_admin\model\SortPromotion();
                break;
            default:
                return errorMsg('参数有误');
        }

        if(!$model){
            return errorMsg('参数有误');
        }

        $condition = [
            ['id','=',$id],
        ];

        return $model->del($condition,false);
    }



}
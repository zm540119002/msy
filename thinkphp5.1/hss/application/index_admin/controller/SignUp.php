<?php
namespace app\index_admin\controller;

/**
 * 广告控制器基类
 * ad,advertising,advert 单词 广告插件会拦截
 */
class SignUp extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\WeixinShare();
    }

    public function manage(){
        return $this->fetch('manage');
    }


    /**
     *  分页查询
     */
    public function getList(){

        $condition = [
            'field' => ['id','link','sort','status'],
            'where' => [
                ['status','=',0],
            ],
        ];

        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'] = ['link','like', '%' . trim($keyword) . '%'];

        $list = $this->obj->pageQuery($condition);
        $this->assign('list',$list);

        return view('list_tpl');
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

}
<?php
namespace app\index\controller;

class Project extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 项目列表
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('参数有误');
        }

        $model = new \app\index\model\Project();
        $condition = [
            'field' => [
                'p.id','p.name','p.thumb_img','p.intro'
            ],
            'where' => [
                ['p.status','=',0],
                ['p.shelf_status','=',3],
            ],'order' => ['p.sort desc']
        ];

        $list = $model -> pageQuery($condition);
        $this->assign('list',$list);

        return $this->fetch('list_tpl');
    }

    /**
     * 查询项目下的相关视频 分页查询 暂时先随机
     */
    public function getVideoList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Project();
        $config=[
            'where'=>[
                ['status','=',0],
                ['shelf_status','=',1],
                ['audit','=',1],
            ],
            'field'=>[
               'id','name','update_time','video'
            ],
            'order'=>[
                'sort'=>'desc',
            ],
        ];

        if(input('?get.belong_to') && (int)input('?get.belong_to')){
            $config['where'][] = ['belong_to', '=', input('get.belong_to')];
        }


        $list = $model -> pageQuery($config);

        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            // 排列的数量不同
            switch($_GET['pageType']){
                case 'column_1' : return $this->fetch('list_video_tpl'); break;   // 一行一个
            }
        }
    }

    /**
     * 详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Project();
            $config =[
                'where' => [
                    ['p.status', '=', 0],
                    ['p.shelf_status', '=', 3],
                    ['p.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此项目已下架');
            }

            project_handle($info);

            $this->assign('info',$info);

            Promotion::displayPromotionList($id,'project');

            $this->assign('relation',config('custom.relation_type.project'));

            return $this->fetch();
        }
    }

    /**详情页
     */
    public function detailImg(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Project();
            $config =[
                'where' => [
                    ['p.status', '=', 0],
                    ['p.shelf_status', '=', 3],
                    ['p.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            //获取相关的商品
            $modelProjectGoods = new \app\index\model\ProjectGoods();
            $config =[
                'where' => [
                    ['pg.status', '=', 0],
                    ['pg.project_id', '=', $id],
                ],'field'=>[
                    'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
                ],'join'=>[
                    ['goods g','g.id = pg.goods_id','left']
                ]
            ];
            $goodsList= $modelProjectGoods->getList($config);
            $this->assign('goodsList',$goodsList);
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

}
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
     * 详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{

            $id = input('id/d');
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Project();
            $config =[
                'field' => [
                    'id','name','main_img','intro','tag','detail_img','video','title','process_img','description','remarks','process','recommend_goods'
                ],
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

            // 项目流程 json
/*            $arr = [
                'img' => '/hss_project/1557805583740576.jpg',
                'desc'=> '01、检测头皮状况',
            ];
            $array = [];
            for($i=0;$i<10;$i++){
                $array[] = $arr;
            }

            $array =  json_encode($array);
            echo $array;
            exit;
            $info['process'] = json_decode($array,true);*/

            // 项目推荐商品
            $modelGoods = new \app\index\model\Goods();
            $condition = [
                'field' => [
                    'id','name','specification','thumb_img'
                ],'where' => [
                    ['status','=',0],
                    ['shelf_status','=',3],
                    ['id','in',$info['recommend_goods']],
                ],'limit' => 4
            ];
            $info['recommend_goods'] = $modelGoods->getList($condition);

            project_handle($info);

            $this->assign('info',$info);

            Promotion::displayPromotionList($id,'project');

            $this->assign('relation',config('custom.relation_type.project'));

            return $this->fetch();
        }
    }

    /**详情页 old
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
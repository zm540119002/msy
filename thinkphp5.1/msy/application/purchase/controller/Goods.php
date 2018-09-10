<?php
namespace app\purchase\controller;

class Goods extends Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }

        $model = new\app\factory\model\Goods;
        $config=[
            'where'=>[
            ],
            'field'=>[
                'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort'
            ],
            'order'=>[
                'sort'=>'desc',
                'line_num'=>'asc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.storeId') && (int)input('?get.storeId')){
            $config['where'][] = ['g.store_id', '=', input('get.storeId')];
        }
        if(input('get.pageType','') == 'promotion' ) {//促销
            $config['where'][] = ['g.sale_type', '=', 0];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = $model -> pageQuery($config);
        $page = $list->getCurrentPage();
        $this->assign('page',$page);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'store' ){//促销
                return $this->fetch('list_tpl');
            }
            if($_GET['pageType'] == 'shelf' ){//上架管理
                return $this->fetch('list_shelf');
            }
            if($_GET['pageType'] == 'manage' ){//管理
                return $this->fetch('list_manage');
            }
            if($_GET['pageType'] == 'inventory' ){//入库
                return $this->fetch('list_inventory');
            }
            if($_GET['pageType'] == 'sort' ){//入库
                return $this->fetch('list_sort');
            }
        }
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goodsId'));
            if($goodsId){
                $modelGoods = new \app\factory\model\Goods();
                $config =[
                    'where' => [
                        ['g.status', '=', 0],
                        ['g.id', '=', $goodsId],
                    ],'field' => [
                        'g.id','g.name','g.sale_price','g.retail_price','g.main_img','g.parameters',
                    ],
                ];
                $info = $modelGoods->getInfo($config);
                if($info){
                    $info['main_img'] = explode(',',(string)$info['main_img']);
                    $this->assign('info',$info);
                }
            }
            return $this->fetch();
        }
    }
}
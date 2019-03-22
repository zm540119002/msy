<?php
namespace app\index\controller;

/**
 * 评价
 */
class Comment extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([16]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**
     *
     */
    public function add(){

        /**
         * user_id
        status
        score
        order_id
        goods_id
        title
        content
        img
        create_time
        update_time

         */


        if(request()->isPost()){



            $userId = $this->user['id'];
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);
                //主图第一张为缩略图
                $_POST['thumb_img'] = $tempArr[0];//
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',input('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            if( isset($_POST['goods_video']) && $_POST['goods_video'] ){
                $_POST['goods_video'] = moveImgFromTemp(config('upload_dir.mcs_scheme'),basename($_POST['goods_video']));
            }

            // 选中的店铺类型 十进制
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));

            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'where' => [
                        'id' => input('post.id/d'),
                        'status' => 0,
                    ],
                ];

                $data = $_POST;
                $data['update_time'] = time();
                $where = [
                    'id'=>input('post.id/d')
                ];

                $result = $modelGoods -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }

                $info = $modelGoods->getInfo($config);
                // 删除旧文件
                if($info['goods_video']){
                    delImgFromPaths($info['goods_video'],$_POST['goods_video']);
                }
                if($info['main_img']){
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$_POST['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                if($info['detail_img']){
                    $oldImgArr = explode(',',$info['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }

                $data['id'] = input('post.id/d');
                $list[] = $data;
                $this->generateQRcode($list);
            }else{//新增
                $data = $_POST;
                $data['create_time'] = time();
                $result = $modelGoods -> allowField(true) -> save($data);
                if(!$result){
                    return errorMsg('失败');
                }
                $data['id'] = $modelGoods->getAttr('id');
                $list[] = $data;
                $this->generateQRcode($list);
            }

            $model= new \app\index\model\Comment();

            $data = [
                'user_id' => $userId,
                'score' => 1,
                'order_id' => 1,
                'goods_id' => 1,
                'content' => 1,
                'img' => 1,
                'create_time' => time(),
                'update_time' => time()
            ];

            $result = $model -> allowField(true) -> save($data);

            return successMsg('成功');
        }
    }

    /**
     *  分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Comment();
        $goodsId = input('get.goods_id/d');
        $page = (int)input('get.page');
        if($page == 1){
            $where = [
                ['status','=',0],
                ['goods_id','=',$goodsId],
            ];
            $averageScore = $model -> where($where)->avg('score');
            $this ->assign('averageScore',$averageScore);
            $total = $model -> where($where)->count('user_id');
            $this ->assign('total',$total);
        }
        $config=[
            'where'=>[
                ['c.status','=',0],
                ['c.goods_id','=',$goodsId],
            ],
            'field'=>[
               'u.name','c.score','c.img','c.title','c.content','c.create_time','c.update_time'
            ],
            'join'=>[
                ['common.user u','u.id = c.user_id','left']
            ],
            'order'=>[
                'c.id'=>'desc'
            ],
        ];
        $list = $model -> pageQuery($config);
        $list->each(function($item, $key){
               $item['img'] =  explode(',',(string) $item['img']);

        });
        $this->assign('list',$list);
        $page++;
        $this ->assign('nextPage',$page);
        return $this->fetch('list_tpl');
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Comment();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $goodsId],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**获取推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $goodsId = input('get.goods_id/d');
        //相关推荐商品
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'rg.recommend_goods_id',
            ]
        ];
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $recommendGoodsIds = $modelRecommendGoods->getList($config);
        $recommendGoodsIds = array_column($recommendGoodsIds,'recommend_goods_id');

        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
                ['g.id', 'in', $recommendGoodsIds],
            ],'field'=>[
               'g.id as goods_id','g.headline','g.thumb_img','g.bulk_price'
            ]
        ];

        $model = new \app\index\model\Comment();
        $list = $model->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }
}
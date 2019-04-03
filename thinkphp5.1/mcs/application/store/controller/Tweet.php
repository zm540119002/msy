<?php
namespace app\store\controller;

class Tweet extends \common\controller\FactoryStoreBase
{
    //促销管理
    public function manage()
    {
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *编辑
     */
    public function edit()
    {
        $data =  input();
        if($data['release_type'] == 1){
            $_POST['fileBase64'] = $data['img'];
            $result =  $this->uploadFileToTemp();
            $imgs = '';
            if($result['status']){
                foreach ($result['info'] as $k=>$v){
                    $imgs .= moveImgFromTemp(config('upload_dir.factory_promotion'),basename($v)) .',';
                }
                $data['img'] = $imgs;

            }else{
                return errorMsg('上传失败');
            }
        }
        print_r($data);
        exit;
        $model = new \app\store\model\Tweeet;

        exit;
        $model = new \common\model\Promotion;
        if(request()->isPost()){
            return $model -> edit($this->store['id']);
        }
        if(input('?id') && $this->store['id']){
            $promotionId = (int)input('id');
            $config = [
                'where' => [
                    ['p.id','=',$promotionId],
                    ['p.store_id','=',$this->store['id']],
                ],'field' => [
                    'p.id,p.name,p.first_img,p.second_img,p.goods_ids,p.start_time,p.end_time,p.store_id'
                ],
            ];
            $promotionInfo =  $model -> getInfo($config);
            if(empty($promotionInfo)){
                $this->error('此产品已下架');
            }
            $modelGoods = new \common\model\Goods;
            $goodsIds = explode(',',$promotionInfo['goods_ids']);
            $config = [
                'where' => [
                    ['id','in',$goodsIds],
                    ['sale_type','=',1],
                ],'field' => [
                    'g.id as goods_id,g.special',
                ],
            ];
            $goodsList = $modelGoods -> getList($config);
            $promotionInfo['goods'] = json_encode($goodsList);
            $this -> assign('promotionInfo',$promotionInfo);
        }
        return $this->fetch();
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new \common\model\Promotion;
        $config=[
            'where'=>[
                ['p.store_id','=',$this->store['id']],
            ],
            'field'=>[
                'p.id','p.name','p.goods_ids','p.start_time','p.end_time','p.create_time','p.sort',
            ],
            'order'=>[
                'id'=>'desc','sort'=>'desc'
            ],
        ];
        $activityStatus = (int)input('get.activityStatus');
        if($activityStatus == 1){//未结束
            $config['where'][] = ['p.end_time','>',time()];
        }
        if($activityStatus == 0){//已结束
            $config['where'][] = ['p.end_time','<=',time()];
        }

        $keyword = input('get.keyword','');
        if($keyword){
            $config['where'][] = ['p.name', 'like', '%'.trim($keyword).'%'];
        }

        $list = $model -> pageQuery($config);
        $page = $list->getCurrentPage();
        $this->assign('page',$page);
        $this->assign('list',$list);
        if(isset($_GET['activityStatus'])){
            if($_GET['activityStatus'] == 1 ){//未结束
                return $this->fetch('list_notover');
            }
            if($_GET['activityStatus'] == 0 ){//结束
                return $this->fetch('list_over');
            }
        }
    }

    /**删除
     */
    public function  del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $model = new \common\model\Promotion();
        return $model->del($this->store['id'],true);
    }
}
<?php
namespace app\factory\controller;

class Promotion extends StoreBase
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
        $model = new \app\factory\model\Promotion;
        if(request()->isPost()){
            return $model -> edit($this->store['id']);
        }
        if(input('?id') && $this->store['id']){
            $promotionId = (int)input('id');
            $where = [
                ['p.id','=',$promotionId],
                ['p.store_id','=',$this->store['id']],
            ];
            $file = [
                'p.id,p.name,p.first_img,p.second_img,p.goods_ids,p.start_time,p.end_time,p.store_id'
            ];
            $promotionInfo =  $model -> getInfo($where,$file);
            if(empty($promotionInfo)){
                $this->error('此产品已下架');
            }
            $modelGoods = new \app\factory\model\Goods;
            $goodsIds = explode(',',$promotionInfo['goods_ids']);
            $where = [
                ['id','in',$goodsIds],
                ['sale_type','=',1],
            ];
            $goodsFile = [
                'g.id as goods_id,g.special'
            ];
            $goodsList = $modelGoods -> getList($where,$goodsFile);
            $promotionInfo['goods'] = json_encode($goodsList);
            $this -> assign('promotionInfo',$promotionInfo);
        }
        return $this->fetch();
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new \app\factory\model\Promotion;
        $where = [
            ['p.store_id','=',$this->store['id']],
        ];

        $field = array(
            'p.id','p.name','p.goods_ids','p.start_time','p.end_time','p.create_time','p.sort',
        );
        $list = $model -> pageQuery($where,$field);
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
        $model = new \app\factory\model\Promotion();
        return $model->del($this->store['id'],true);
    }
}
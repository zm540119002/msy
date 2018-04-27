<?php
namespace app\factory\controller;

class Promotion extends FactoryBase
{
    //促销管理
    public function manage()
    {
        $storeType = input('storeType');
        if($storeType !='purchases' && $storeType!='commission' && $storeType!='retail') {
            $storeType = 'purchases';
        }
        $this -> assign('storeType',$storeType);
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
            return $model -> edit($this->factory['id']);
        }
        if(input('?promotion_id')){
            $promotionId = (int)input('promotion_id');
            $where = [
                ['p.id','=',$promotionId],
                ['p.factory_id','=',$this->factory['id']],
            ];
            $file = [
                'p.id,p.name,p.img,p.goods_id,p.promotion_price,p.start_time,p.end_time,p.factory_id,g.thumb_img,g.name as goods_name'
            ];
            $join =[
              ['goods g','g.id = p.goods_id'],
            ];
            $promotionInfo =  $model -> getPromotion($where,$file,$join);
            if(empty($promotionInfo)){
                $this->error('此产品已下架');
            }
            $this -> assign('promotionInfo',$promotionInfo);
        }
        $storeType = input('storeType');
        if($storeType!='purchases' &&  $storeType!='commission' && $storeType!='retail') {
            $storeType = 'purchases';
        }
        $this -> assign('storeType',$storeType);
        return $this->fetch();
    }




}
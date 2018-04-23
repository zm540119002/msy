<?php
namespace app\factory\controller;

class Promotion extends FactoryBase
{
    public function manage()
    {
        return 11 ;
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $model = new \app\factory\model\Promotion;
        if(request()->isPost()){
            if(input('?post.promotion_id') && !input('?post.promotion_id') == ''){
                return $model -> edit($this->factory['id']);
            }
            return $model -> add($this->factory['id']);
        }
        if(input('?promotion_id')){
            $promotionId = (int)input('promotion_id');
            $where = [
                ['id','=',$promotionId],
                ['factory_id','=',$this->factory['id']],
            ];
            $promotionInfo =  $model -> getPromotion($where);
            if(empty($promotionInfo)){
                $this->error('此产品已下架');
            }
            $this -> assign('promotionInfo',$promotionInfo);
        }
        return $this->fetch();
    }




}
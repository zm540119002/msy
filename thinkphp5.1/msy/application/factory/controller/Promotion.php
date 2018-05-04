<?php
namespace app\factory\controller;

class Promotion extends FactoryBase
{
    //促销管理
    public function manage()
    {
        $storeType = input('storeType');
        if($storeType !=1 && $storeType!=2 && $storeType!=3) {
            $storeType = 1;
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
            return $model -> edit($this->factory['factory_id']);
        }
        if(input('?promotion_id')){
            $promotionId = (int)input('promotion_id');
            $where = [
                ['p.id','=',$promotionId],
                ['p.factory_id','=',$this->factory['factory_id']],
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
        if($storeType!=1 &&  $storeType!=2 && $storeType!=3) {
            $storeType =1;
        }
        $this -> assign('storeType',$storeType);
        return $this->fetch();
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new \app\factory\model\Promotion;
        $where = [
            ['p.factory_id','=',$this->factory['factory_id']],
        ];
        $list = $model -> pageQuery($where);
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




}
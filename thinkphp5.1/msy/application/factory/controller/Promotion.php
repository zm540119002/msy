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
            print_r(input());exit;
            return $model -> edit($this->store['id']);
        }
        if(input('?id') && $this->store['id']){
            $promotionId = (int)input('id');
            $where = [
                ['p.id','=',$promotionId],
                ['p.store_id','=',$this->store['id']],
            ];
            $file = [
                'p.id,p.name,p.img,p.goods_id,p.promotion_price,p.start_time,p.end_time,p.store_id,g.thumb_img,g.name as goods_name'
            ];
            $join =[
              ['goods g','g.id = p.goods_id'],
            ];
            $promotionInfo =  $model -> getInfo($where,$file,$join);
            if(empty($promotionInfo)){
                $this->error('此产品已下架');
            }
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
        $join = [
            ['goods g','g.id = p.goods_id'],
        ];
        $field = array(
            'p.id','p.name','p.img','p.goods_id','p.promotion_price','p.start_time','p.end_time','p.create_time','p.sort',
            'g.name as goods_name','g.retail_price'
        );
        $list = $model -> pageQuery($where,$field,$join);
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
        $modelRole = new \app\factory\model\Promotion();
        return $modelRole->del($this->store['id'],true);
    }

}
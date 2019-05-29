<?php
namespace app\index_admin\controller;

class Order extends Base{
    //首页
    public function manage(){

        $modelExpress = new \app\index_admin\model\Express();
        $condition = [
            'field' => [
                'id','name'
            ], 'where' => [
                ['status','=',1]
            ],'order'  => [
                'order' => 'desc',
                'letter'=> 'asc'
            ]
        ];
        $expressList = $modelExpress->getList($condition);
        $this->assign('expressList',$expressList);

        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){

        $where = [
            ['o.status','=',0],
            ['o.order_status','<>',0],
        ];

        if($pay_code = input('pay_code/d')){
            $where[] = ['o.pay_code','=',$pay_code];
        }
        if($after_sale_status = input('after_sale_status/d')){
            $where[] = ['o.after_sale_status','=',$after_sale_status];
        }
        if($order_status = input('order_status/d')){
            $where[] = ['o.order_status','=',$order_status];
        }

        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['o.sn','like', '%' . trim($keyword) . '%'];
        }
        $config = [
            'field'=>[
                'u.mobile_phone',
                'o.id','o.sn','o.order_status','o.after_sale_status','o.pay_code','o.user_id', 'o.create_time',
                'o.province','o.city','o.area','o.detail_address','o.express_sn'
            ],'where'=>$where,
            'join' => [
                ['common.user u','o.user_id = u.id'],
            ],
            'order'=>[
                'o.id'=>'desc',
            ],
        ];

        $model = new \app\index_admin\model\Order();
        $list = $model ->pageQuery($config);

        $this->assign('list',$list);
        return view('list_tpl');
    }

    /**
     * 订单信息
     */
    public function getInfo(){
        $id = input('id/d');

        if(!$id){
            $this->error('没有该订单');
        }
        $model = new \app\index\model\Order();
        $condition = [
            'field' => [
                'o.id','o.sn','o.pay_sn','o.order_status','o.pay_code','o.remark','o.payment_time',
                'o.after_sale_status', 'o.amount','o.coupons_amount','o.actually_amount','o.create_time','o.receive_time',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.express_sn','o.exp_id',
                'od.goods_name','od.goods_img','od.price','od.num'
            ],'where' => [
              ['o.status','=',0],
              ['o.order_status','<>',0],
              ['o.id','=',$id],
            ],'join' => [
                ['order_detail od','o.id=od.father_order_id','left']
            ]
        ];
        $data = $model->getList($condition);

        if(empty($data)){
            $this->error('没有找到该订单');
        }
        $orderInfo = reset($data);
        foreach($data as $k => $v){
            $goods = [
                'goods_name' => $v['goods_name'],
                'goods_img'  => $v['goods_img'],
                'price'      => $v['price'],
                'num'        => $v['num'],
            ];
            $orderInfo['goods_info'][] = $goods;
        }
        $type = input('type/d');

        $this->assign('info',$orderInfo);

        if($type=='info'){
            return $this->fetch('order_info');
        }

    }

    /**
     * 增加快递信息
     */
    public function addExpress(){
        $id        = input('id/d');
        $express_id= input('express_id/d');
        $express_sn= input('express_sn/s');

       if( !$id OR !$express_id OR !$express_sn  ){
           return $this->errorMsg('参数不能为空');
       }

       $data = [
           'express_id'  => $express_id,
           'express_sn'  => $express_sn,
           'order_status'=> 3,
       ];

        $condition = [
            ['id','=',$id],
            ['status','=',0],
        ];
        $model = new \app\index_admin\model\Order();
        $res = $model->edit($data,$condition);

        if($res===false){
            return $this->errorMsg('更新失败');

        }else{
            return $this->successMsg('更新成功');
        }
    }
}
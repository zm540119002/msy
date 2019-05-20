<?php
namespace app\index_admin\controller;

class Order extends Base{
    //首页
    public function manage(){

        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){

// 获取表的所有字段
/*        $model = new \app\index_admin\model\Order();
        $table = $model->getTableFields();
        foreach($table as $k => $v){
            $table[$k] = "'".$v."'";
        }
        $table = implode(',',$table);

        p($table);
        exit;*/

        $where[] = ['o.status','=',0];

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
                'o.id','o.sn','o.pay_sn','o.order_status','o.after_sale_status','o.pay_code','o.amount','o.coupons_pay','o.actually_amount','o.remark','o.user_id',
                'o.create_time','o.payment_time','o.finished_time','o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address',
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
        p($model->getLastSql());
        exit;
        $this->assign('list',$list);
        if($_GET['pageType'] == 'layer'){
            return view('goods/list_layer_tpl');
        }
        if($_GET['pageType'] == 'manage'){
            return view('goods/list_tpl');
        }
    }
}
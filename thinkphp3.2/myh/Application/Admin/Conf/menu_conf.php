<?php
return array(
    'system'=>
        array('name' => '商品管理',
            'sub_menu' => array(
                array('name'=>'分类管理','act'=>'GoodsCategory','op'=>'goodsCategoryManage'),
                array('name' => '单位管理', 'act'=>'Unit', 'op'=>'unitManage'),
                array('name' => '商品管理', 'act'=>'Goods', 'op'=>'goodsManage'),
                array('name' => '商品公共图片管理', 'act'=>'Goods', 'op'=>'commonImageEdit'),
        )),
        array('name' => '订单管理',
            'sub_menu' => array(
                array('name'=>'订单列表','act'=>'Order','op'=>'index'),
                array('name'=>'发货订单','act'=>'Order','op'=>'deliveryList'),
                array('name'=>'退款订单','act'=>'Order','op'=>'refundOrderList'),
                array('name'=>'退换货订单','act'=>'Order','op'=>'returnList'),
        )),


);
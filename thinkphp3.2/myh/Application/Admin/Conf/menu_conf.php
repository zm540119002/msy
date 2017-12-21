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

        array('name' => '系统统计',
            'sub_menu' => array(
                 array('name'=>'柱状图','act'=>'Index','op'=>'charts-4'),
        )),

    
    
    
);
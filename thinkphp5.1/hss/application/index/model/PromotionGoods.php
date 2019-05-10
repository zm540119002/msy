<?php
namespace app\index\model;

/**
 * 基础模型器
 */

class PromotionGoods extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'promotion_goods';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	// 别名
	protected $alias = 'pg';

    /**
     * 获取各套餐列表商品总价
     * @param $list array 方案列表
     * @return array
     */
    public function getListGoodsPrice($list){
        // 套餐下的商品总价 单个
        foreach($list as $k => $v){

            if( $v['id']>0 ){

                $condition = [
                    'field' => [
                        'sum(g.bulk_price) price',
                    ], 'where' => [
                        ['g.status','=',0],
                        ['g.shelf_status','=',3],
                        ['pg.promotion_id','=',$v['id']],
                    ],'join' => [
                        ['goods g','pg.goods_id = g.id','left']
                    ],
                ];

                $info = $this->getInfo($condition);
                $list[$k]['price'] = $info['price'] ? $info['price'] : '0.00';
            }
        }
        return $list;
    }
}
<?php
namespace app\index\model;

/**
 * 基础模型器
 */

class AdPositions extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'ad_positions';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	// 别名
	protected $alias = 'ap';


    // 根据key获取广告列表
    public function getAds($key){
        $condition = [
            'field' => [
                's.name','s.thumb_img','s.ad_link',
            ], 'where' => [
                ['ap.key','=',$key],
                ['ap.shelf_status','=',3],
                ['s.status','=',0],
                ['s.shelf_status','=',3],
            ], 'join' => [
                ['shortcut s','ap.id=s.ad_position_id','left']
            ],
        ];

        return $this->getList($condition);


    }
}
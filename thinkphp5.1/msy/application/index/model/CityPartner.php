<?php
namespace app\index\model;

class CityPartner extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'city_partner';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    public $connection = 'db_config_hss';
	//表的别名
	protected $alias = 'f';

}

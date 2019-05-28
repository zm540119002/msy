<?php
namespace app\index\model;

class Franchise extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'franchise';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    public $connection = 'db_config_hss';
	//表的别名
	protected $alias = 'f';

}

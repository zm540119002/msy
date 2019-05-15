<?php
namespace app\index_admin\model;

/**
 * 广告基础模型器
 */

class Shortcut extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'shortcut';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	// 别名
	protected $alias = 's';
}
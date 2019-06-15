<?php
namespace app\index\model;

class MemberBeforehandRegister extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'member_beforehand_register';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'mbr';
	
}
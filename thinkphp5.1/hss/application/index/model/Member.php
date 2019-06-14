<?php
namespace app\index\model;

class Member extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'member';
	// 设置主键
	public $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'm';

    /**
     * 获取账号信息
     */
    public function getMemberInfo($uid){
        $config = [

            'where' => [
                ['user_id','=',$uid],
            ]
        ];
        //p($config);
        //exit;
        //return $this->where($where)->field($field)->find();
        return $this->getInfo($config);
    }


}
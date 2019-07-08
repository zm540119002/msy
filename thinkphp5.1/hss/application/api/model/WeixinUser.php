<?php
namespace app\index\model;

class WeixinUser extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'weixin_user';
	// 设置主键
    protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'wx';

    /**
     * 获取账号信息
     */
    public function getMemberInfo($uid){
        $config = [
            'where' => [
                ['user_id','=',$uid],
            ]
        ];

        return $this->getInfo($config);
    }


}
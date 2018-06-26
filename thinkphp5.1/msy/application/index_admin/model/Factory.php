<?php
namespace app\index_admin\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Factory extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'factory';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	/**
	 * 审核厂商
	 */
	public function audit(){
		$postData = input('post.');
		$res = $this->isUpdate(true)->save($postData);
		if($res===false){
			return errorMsg('更新失败',$this->getError());
		}else{
			return successMsg('成功！',$postData);
		}
	}

	/**查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'f.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('f')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)?$list->toArray():[];
	}

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'f.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('f')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}
}
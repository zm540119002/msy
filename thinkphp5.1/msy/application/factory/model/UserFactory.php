<?php
namespace app\factory\model;

class UserFactory extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	//设置默认厂商
	public function setDefaultFactory($userId=0){
		if(request()->isAjax()){
			$this->startTrans();//开启事务
			$where = [
				['status','=',0],
				['user_id','=',$userId],
			];
			$result = $this->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$id = (int)input('post.id');
			if($id){
				$where[] = ['id','=',$id];
			}
			$factoryId = input('post.factoryId');
			if(intval($factoryId)){
				$where[] = ['factory_id','=',$factoryId];
			}
			$result = $this->where($where)->setField('is_default',1);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$this->commit();//提交事务
			return successMsg("成功");
		}
	}

	/**查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'u.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('u')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return empty($list)?[]:$list->toArray();
	}

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'u.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('u')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return empty($info)?[]:$info->toArray();
	}
}
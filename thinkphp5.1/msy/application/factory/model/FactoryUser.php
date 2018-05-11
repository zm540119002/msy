<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class FactoryUser extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
//	protected $readonly = ['name'];

	/**
	 * 修改
	 */
	public function edit($uid=''){
		

	}
	//设置默认厂商
	public function setDefaultFactory($uid=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'user_id'=>$uid]);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['user_id','=',$uid],
			];
			$result = $this ->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改失败');
			}
			$this->commit();
			return successMsg("已选择");
		}
	}
	/**
	 * @param array $where
	 * @param array $field
	 * @param array $order
	 * @param array $join
	 * @param string $limit
	 * @return array|\PDOStatement|string|\think\Collection
	 * 查询多条数据
	 */
	public function selectFactoryUser($where=[],$field=[],$join=[],$order=[],$limit=''){
		$_where = array(
			'u.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('u')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('u')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}
		return $list;
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getFactoryUser($where=[],$field=[],$join=[]){
		$_where = array(
			'u.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('u')
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this->alias('u')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}
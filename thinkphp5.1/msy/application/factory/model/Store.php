<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Store extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
//	protected $readonly = ['name'];

	/**
	 * 编辑
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('Store');
//		if(!$result = $validate->check($data)) {
//			return errorMsg($validate->getError());
//		}
		if(input('?post.store_id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id']]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			return successMsg('提交申请成功');
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
	public function selectStore($where=[],$field=[],$join=[],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('s')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('s')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}
		if(!empty($list)){
			$list = $list ->toArray();
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
	public function getStore($where=[],$field=[],$join=[]){
		$_where = array(
			'status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('s')
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this->alias('s')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		if(!empty($info)){
			$info = $info ->toArray();
		}
		return $info;
	}
}
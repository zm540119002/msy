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
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
	/**
	 * 编辑
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('Store');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(input('?post.id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id'],'factory_id'=>$factoryId]);
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
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			's.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('s')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
	    return count($list)?$list->toArray():[];
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('s')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}

	//设置默认店铺
	public function setDefaultStore($factoryId=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'factory_id'=>$factoryId]);
			print_r($this->getLastSql());exit;
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['factory_id','=',$factoryId],
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
	/**检查店铺是否属于此厂商
	 */
	public function checkStoreExist($id,$factoryId){
		$where = [
			['id','=',$id],
			['factory_id','=',$factoryId]
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}

	}
}
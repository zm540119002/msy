<?php
namespace app\store\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Series extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'series';
	// 设置主键
	protected $pk = 'id';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';
	/**
	 * 新增
	 */
	public function add($storeId){
		$data = input('post.');
		$validate = validate('Series');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$this->startTrans();
		$data['store_id'] = $storeId;
		$data['create_time'] = time();
		$result = $this->allowField(true)->save($data);
		$id = $this->getAttr('id');
		if(false == $result){
			$this->rollBack();// 事务A回滚
			return errorMsg($this->getError());
		}
		$data = array('sort'=>$id);
		$result = $this->allowField(true)->save($data,['id' => $id]);
		if(false == $result){
			$this->rollBack();// 事务A回滚
			return errorMsg($this->getError());

		}
		$this->commit();
		return successMsg("成功添加");

	}

	/**
	 * 修改
	 */
	public function edit($storeId){
		$data = input('post.');
		$validate = validate('Series');
		if(!$result = $validate->scene('edit')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data,['id' => $data['series_id'],'store_id'=>$storeId]);
		if(false !== $result){
			return successMsg("已修改");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 删除
	 */
	public function del($storeId){
		$data = input('post.');
		if(is_array($data['series_id'])){
			$where['id']  = array('in',$data['series_id']);
		}else{
			$where['id'] = $data['series_id'];
			$where['store_id'] = $storeId;
		}
		$result = $this->where($where)->delete();;
		if(false !== $result){
			return successMsg("已删除");
		}else{
			return errorMsg($this->getError());
		}
	}

	//移动
	public function move($storeId){
		$data = input('post.');
		if($data['move']){
			$where = [
				['store_id','=',$storeId],
				['sort', '<', $data['sort']]
			];
		}else{
			$where = [
				['store_id','=',$storeId],
				['sort', '>', $data['sort']]
			];
		}
		$lastSeries = $this -> getList($where,[],[],'1');
		if(!empty($lastSeries)){
			$this -> startTrans();
			$updateData = [
				'sort' => $data['sort'],
			];
			$result = $this->allowField(true)->save($updateData,['id' => $lastSeries[0]['id'],'store_id' => $storeId]);
			if(false == $result){
				$this->rollBack();// 事务A回滚
				return errorMsg($this->getError());
			}
			$updateData = [
				'sort' => $lastSeries[0]['sort'],
			];
			$result = $this->allowField(true)->save($updateData,['id' => $data['series_id'],'store_id' => $storeId]);
			if(false == $result){
				$this->rollBack();// 事务A回滚
				return errorMsg($this->getError());
			}
			$this->commit();
			return successMsg("成功添加");
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
	public function getList($where=[],$field=['*'],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('s')
			->where($where)
			->field($field)
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
	public function getInfo($where=[],$field=['*']){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$info = $this->alias('s')
			->field($field)
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}
}
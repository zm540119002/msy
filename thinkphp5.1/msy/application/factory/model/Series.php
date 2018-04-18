<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Series extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'series';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';
	/**
	 * 新增
	 */
	public function add($factoryId){
		$data = input('post.');
		$validate = validate('Series');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$this->startTrans();
		$data['factory_id'] = $factoryId;
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
	public function edit($factoryId){
		$data = input('post.');
		$validate = validate('Series');
		if(!$result = $validate->scene('edit')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data,['id' => $data['series_id'],'factory_id'=>$factoryId]);
		if(false !== $result){
			return successMsg("已修改");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 删除
	 */
	public function del($factoryId){
		$data = input('post.');
		if(is_array($data['series_id'])){
			$where['id']  = array('in',$data['series_id']);
		}else{
			$where['id'] = $data['series_id'];
			$where['factory_id'] = $factoryId;
		}
		$result = $this->where($where)->delete();;
		if(false !== $result){
			return successMsg("已删除");
		}else{
			return errorMsg($this->getError());
		}
	}

	//
	public function moveUp($factoryId){
		$data = input('post.');
		$where = array(
			'id' => $data['series_id'],
			'factory_id' => $data['factory_id'],
            'sort' => $data['sort'],
		);
		$sort = $data['sort'];
		$this -> selectSeries($where,[],['sort'=>'desc'],"$sort,1");
		echo $this -> getLastSql();exit;

	}
	public function moveDown($factoryId){

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
	public function selectSeries($where=[],$field=[],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('s')
				->where($where)
				->field($field)
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('s')
				->where($where)
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
	public function getSeries($where=[],$field=[]){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		if($field){
			$info = $this->alias('s')
				->field($field)
				->where($where)
				->find();
		}else{
			$info = $this->alias('s')
				->where($where)
				->find();
		}
		return $info;
	}
}
<?php
namespace app\store\model;

class Series extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'series';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
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

	//移动
	public function move($factoryId){
		$data = input('post.');
		if($data['move']){
			$where = [
				['factory_id','=',$factoryId],
				['sort', '<', $data['sort']]
			];
		}else{
			$where = [
				['factory_id','=',$factoryId],
				['sort', '>', $data['sort']]
			];
		}
		$lastSeries = $this -> getList($where,[],[],'1');
		if(!empty($lastSeries)){
			$this -> startTrans();
			$updateData = [
				'sort' => $data['sort'],
			];
			$result = $this->allowField(true)->save($updateData,['id' => $lastSeries[0]['id'],'factory_id' => $factoryId]);
			if(false == $result){
				$this->rollBack();// 事务A回滚
				return errorMsg($this->getError());
			}
			$updateData = [
				'sort' => $lastSeries[0]['sort'],
			];
			$result = $this->allowField(true)->save($updateData,['id' => $data['series_id'],'factory_id' => $factoryId]);
			if(false == $result){
				$this->rollBack();// 事务A回滚
				return errorMsg($this->getError());
			}
			$this->commit();
			return successMsg("成功添加");
		}

	}
	
}
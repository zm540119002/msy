<?php
namespace app\store\model;

class Role extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';

	//编辑
	public function edit($storeId){
		$postData = input('post.');
		$validateRole = new \app\store\validate\Role();
		if(!$validateRole->scene('edit')->check($postData)){
			return errorMsg($validateRole->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				return errorMsg('更新失败',$this->getError());
			}
		}else{
			if(!intval($storeId)){
				return errorMsg('参数错误');
			}
			$postData['store_id'] = $storeId;
			unset($postData['id']);
			$res = $this->isUpdate(false)->save($postData);
			if($res===false){
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
		}
		return successMsg('成功！',$postData);
	}

	//获取列表
	public function getList($storeId){
		if(!request()->isGet()){
			return errorMsg(config('custom.not_get'));
		}
		$where = [
			['status', '=', 0],
			['store_id', '=', $storeId],
		];
		$field = array(
			'id','name',
		);
		$order = 'id';
		$list = $this->where($where)->field($field)->order($order)->select();
		return count($list)?$list->toArray():[];
	}

	//删除
	public function del($storeId,$tag=true){
		if(!intval($storeId)){
			return errorMsg('参数错误');
		}
		$where = [
			['status', '=', 0],
			['store_id', '=', $storeId],
		];
		$id = input('post.id');
		if(!intval($id)){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		if($tag){//标记删除
			$result = $this->where($where)->setField('status',2);
		}else{
			$result = $this->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
}
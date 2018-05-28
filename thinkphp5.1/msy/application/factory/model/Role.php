<?php
namespace app\factory\model;

class Role extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	//编辑
	public function edit($factoryId){
		$postData = input('post.');
		$validateRole = new \app\factory\validate\Role();
		if(!$validateRole->scene('edit')->check($postData)){
			return errorMsg($validateRole->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$res = $this->isUpdate(true)->save($postData);
			if(!$res){
				return errorMsg('更新失败',$this->getError());
			}
		}else{
			if(!intval($factoryId)){
				return errorMsg('参数错误');
			}
			$postData['factory_id'] = $factoryId;
			unset($postData['id']);
			$res = $this->save($postData);
			if(!$res){
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
		}
		return successMsg('成功！',$postData);
	}

	//获取列表
	public function getList($factoryId){
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
		];
		$field = array(
			'id','name',
		);
		$order = 'id';
		$list = $this->where($where)->field($field)->order($order)->select()->toArray();
		return empty($list)?[]:$list;
	}

	//删除
	public function del($factoryId,$tag=true){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
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
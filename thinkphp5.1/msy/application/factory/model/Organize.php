<?php
namespace app\factory\model;

class Organize extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'organize';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	//编辑
	public function edit($factoryId){
		$postData = input('post.');
		$postData['factory_id'] = $factoryId;
		$postData['level'] = intval($postData['level']) + 1;
		$validateOrganize = new \app\factory\validate\Organize();
		if(!$validateOrganize->scene('edit')->check($postData)){
			return errorMsg($validateOrganize->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$postData['update_time'] = time();
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			$postData['create_time'] = time();
			$postData['superior_id'] = $postData['organize_id'];
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			return successMsg('成功！',array('id'=>$this->getAttr('id')));
		}else{
			return successMsg('成功！',array_merge(array('id'=>$this->getAttr('id')),$postData));
		}
	}

	//获取组织列表
	public function getOrganizeList($factoryId){
		$where = [
			['factory_id', '=', $factoryId],
		];
		$allOrganize = $this->createTree($this->getAllOrganize($where));
		return empty($allOrganize)?[]:$allOrganize;
	}

	//递归生成菜单树
	private function createTree($multiArr,$superiorId=1){
		static $list = [];
		foreach ($multiArr as $k => $v){
			if($v['superior_id']==$superiorId){
				$list[] = $v;
				self::createTree($multiArr,$v['id']);
			}
		}
		return $list;
	}

	//获取组织
	private function getAllOrganize($where){
		$_where = [
			['status', '=', 0],
		];
		$where = array_merge($_where,$where);
		$field = array(
			'id','name','level','superior_id',
		);	
		$order = 'id';
		$allOrganize = $this->where($where)->field($field)->order($order)->select()->toArray();
		return empty($allOrganize)?[]:$allOrganize;
	}

	//删除
	public function del($factoryId,$tag=true){
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
		];
		$id = input('post.id/a');
		if(!is_array($id) || empty($id)){
			return errorMsg('参数错误');
		}
		$where[] = ['id', 'in', $id];
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
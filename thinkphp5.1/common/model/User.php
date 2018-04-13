<?php
namespace common\model;

class User extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit(){
		$postData = input('post.');
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功！',array('id'=>$this->getAttr('id')));
	}
	//分页查询
	public function pageQuery(){
		$where = [
			['status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = array(
			'id','name','nickname','mobile_phone',
		);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
	}
	//删除
	public function del(){
		$where = [
			['status', '=', 0],
		];
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		$result = $this->where($where)->delete();
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
	//标记删除
	public function tagDel(){
		$where = [
			['status', '=', 0],
		];
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		$result = $this->where($where)->setField('status',2);
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
}
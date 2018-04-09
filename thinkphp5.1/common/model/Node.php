<?php
namespace common\model;

class Node extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'node';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//管理
	public function manage(){
		$data = input('post.');
		return successMsg('成功！');
	}
	//编辑
	public function edit(){
		$postData = input('post.');
		$validateNode = new \common\validate\Node();
		if(!$validateNode->scene('edit')->check($postData)){
			return errorMsg($validateNode->getError());
		}
		$this->save($postData);
		if(!$this->getAttr('id')){
			return errorMsg('失败');
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
			'id','name','path','remark',
		);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
	}
}
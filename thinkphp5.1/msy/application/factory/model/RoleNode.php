<?php
namespace app\factory\model;

class RoleNode extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'role_node';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	//编辑
	public function edit(){
		$postData = input('post.');
		if(!intval($postData['roleId'])){
			return errorMsg('参数有误');
		}
		if(is_array($postData['nodeIds']) && !empty($postData['nodeIds'])){
			$response = $this->where('role_id','=',$postData['roleId'])->select();
			$response = $response->toArray();
			//原节点
			$originNodeIds = array_column($response,'node_id');
			//新增节点
			$addNodeIds = empty($originNodeIds)?$postData['nodeIds']:array_diff($postData['nodeIds'],$originNodeIds);
			if(!empty($addNodeIds)){
				$list = [];
				foreach ($addNodeIds as $val){
					$arr = ['role_id'=>$postData['roleId'],'node_id'=>$val];
					$list[] = $arr;
				}
				!empty($list) && $this->saveAll($list);
			}
			//删除节点
			if(!empty($originNodeIds)){
				$delNodeIds = array_diff($originNodeIds,$postData['nodeIds']);
				$delNodeIds = array_values($delNodeIds);
				if(!empty($delNodeIds) && !$this->batchDelByNodeIds($delNodeIds)){
					return errorMsg('删除失败',$this->getError());
				}
			}
		}
		return successMsg('成功！');
	}

	//批量删除
	public function batchDelByNodeIds($nodeIds){
		$where = [
			['node_id', 'in', $nodeIds],
		];
		return $this->where($where)->delete();
	}

	//获取列表
	public function getList(){
		$postData = input('post.');
		$where = [];
		if(is_array($postData['roleId']) && !empty($postData['roleId'])){
			$where[] = ['role_id', 'in', $postData['roleId']];
		}elseif(!is_array($postData['roleId']) && intval($postData['roleId'])){
			$where[] = ['role_id', '=', $postData['roleId']];
		}else{
			return errorMsg('参数错误');
		}
		$list = $this->where($where)->select();
		return empty($list)?[]:$list->toArray();
	}
}
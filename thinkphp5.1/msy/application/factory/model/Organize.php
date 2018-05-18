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
		return successMsg('成功！',array_merge(array('id'=>$this->getAttr('id')),$postData));
	}
	
	//分页查询
	public function pageQuery(){
		$where = [
			['status', '=', 0],
			['level', '=', 1],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = array(
			'id','name','level','parent_id_1','parent_id_2','remark','sort','img',
		);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
	}

	//删除
	public function del($tag=true){
		$where = [
			['status', '=', 0],
		];
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		$level = input('post.level',0);
		$whereOr = [];
		if($level==1){
			$whereOr[] = ['parent_id_1', '=', $id];
		}elseif($level==2){
			$whereOr[] = ['parent_id_2', '=', $id];
		}
		if($tag){//标记删除
			$result = $this->where($where)->whereOr($whereOr)->setField('status',2);
		}else{
			$result = $this->where($where)->whereOr($whereOr)->delete();
		}
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}

}
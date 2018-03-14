<?php
namespace app\common\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Factory extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'factory';
	// 设置当前模型的数据库连接
//	protected $connection = 'db_factory';
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$validate = validate('Factory');
		if(!$result = $validate->scene('add')->check($data)) {
			return ajaxReturn($validate->getError());
		}
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return ajaxReturn("已提交申请", 1);
		}else{
			return ajaxReturn($this->getError(),-1);
		}
	}
}
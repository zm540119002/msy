<?php
namespace common\model;

class Base extends \think\Model {

	/**查询多条数据
	 */
	public function getList($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$list = $_model->select();
		return count($list)?$list->toArray():[];
	}

	/**查找一条数据
	 */
	public function getInfo($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$info = $_model->find();
		return $info;
	}

	/**分页查询
	 */
	public function pageQuery($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
        $_model->paginate($pageSize);
		return $this->getLastSql();
	}
}
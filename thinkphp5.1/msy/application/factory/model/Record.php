<?php
namespace app\factory\model;
use think\Model;
use think\Db;
use think\Route;

/**
 * 基础模型器
 */

class Record extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'record';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	/**
	 * 新增
	 */
	public function add($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		//$validate = validate('Record');
//		if(!$result = $validate->scene('add')->check($data)) {
//			return errorMsg($validate->getError());
//		}
		if(!empty($data['company_img'])){
			$data['company_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['company_img']));
		}
		if(!empty($data['logo_img'])){
			$data['logo_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['logo_img']));
		}
		if(!empty($data['rb_img'])){
			$data['rb_img'] = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['rb_img']);
		}
		if(!empty($data['factory_video'])){
			$data['factory_video'] = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['factory_video']);
		}
		if(!empty($data['license'])){
			$data['license'] = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['license']);
		}
		if(!empty($data['glory_img'])){
			$data['glory_img'] = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['glory_img']);
		}
		$data['create_time'] = time();
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return successMsg("已成功添加");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 修改
	 */
	public function edit($factoryId){
		$data = input('post.');
		$where['id'] = $data['record_id'];
		$file = array(
			'logo_img','company_img','rb_img','factory_video','license','glory_img'
		);

		$oldRecordInfo = $this -> getRecord($where,$file);
		//return $oldRecordInfo;
//		$validate = validate('Record');
//		if(!$result = $validate->scene('edit')->check($data)) {
//			return errorMsg($validate->getError());
//		}
		if(!empty($data['company_img'])){
			$data['company_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['company_img']));
		}

		if(!empty($data['logo_img'])){
			$data['logo_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['logo_img']));
		}

		if(!empty($data['rb_img'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['rb_img']);
			$data['rb_img'] = $rse['imgsWithDecNew'];
			$newRbImg = $rse['imgsArray'];
		}

		if(!empty($data['factory_video'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['factory_video']);
			$data['factory_video'] = $rse['imgsWithDecNew'];
			$newFactoryVideo = $rse['imgsArray'];
		}
		if(!empty($data['license'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['license']);
			$data['license'] = $rse['imgsWithDecNew'];
			$newLicense = $rse['imgsArray'];
		}
		if(!empty($data['glory_img'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['glory_img']);
			$data['glory_img'] = $rse['imgsWithDecNew'];
			$newGloryImg = $rse['imgsArray'];
		}
		$data['update_time'] = time();

		$result = $this->allowField(true)->save($data,['id' => $data['record_id'],'factory_id'=>$factoryId]);
		if(false !== $result){
			delImgFromPaths($oldRecordInfo['company_img'],$data['company_img']);
			delImgFromPaths($oldRecordInfo['logo_img'],$data['logo_img']);
			if(!empty($oldRecordInfo['rb_img']) && !empty($data['rb_img'])){
				$rbImgWithDec = json_decode($oldRecordInfo['rb_img'],true);
				$oldRbImg = [];
				foreach ($rbImgWithDec as $item){
					$oldRbImg[] = $item['imgSrc'];
				}
				delImgFromPaths($oldRbImg,$newRbImg);
			}
			if(!empty($oldRecordInfo['factory_video']) && !empty($data['factory_video'])) {
				$rbImgWithDec = json_decode($oldRecordInfo['factory_video'],true);
				$oldFactoryVideo = [];
				foreach ($rbImgWithDec as $item){
					$oldFactoryVideo[] = $item['imgSrc'];
				}
				delImgFromPaths($oldFactoryVideo,$newFactoryVideo);
			}
			if(!empty($oldRecordInfo['license']) && !empty($data['license'])){
				$rbImgWithDec = json_decode($oldRecordInfo['license'],true);
				$oldLicense = [];
				foreach ($rbImgWithDec as $item){
					$oldLicense[] = $item['imgSrc'];
				}
				delImgFromPaths($oldLicense,$newLicense);
			}
			if(!empty($oldRecordInfo['glory_img']) && !empty($data['glory_img'])){
				$rbImgWithDec = json_decode($oldRecordInfo['glory_img'],true);
				$oldGloryImg = [];
				foreach ($rbImgWithDec as $item){
					$oldGloryImg[] = $item['imgSrc'];
				}
				delImgFromPaths($oldGloryImg,$newGloryImg);
			}

			return successMsg("已修改");
		}else{
			return errorMsg($this->getError());
		}
	}
	/**
	 * @param array $where
	 * @param array $field
	 * @param array $order
	 * @param array $join
	 * @param string $limit
	 * @return array|\PDOStatement|string|\think\Collection
	 * 查询多条数据
	 */
	public function selectRecord($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'r.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('r')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('r')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}
		return $list;
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getRecord($where=[],$field=[],$join=[]){
		$_where = array(
			'r.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('r')
				->field($field)
				->where($where)
				->join( 'factory f','f.id = r.factory_id')
				->find();
		}else{
			$info = $this->alias('r')
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}
		return $info;
	}
}
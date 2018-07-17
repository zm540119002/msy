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
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	/**
	 * 编辑厂商档案 新增和修改
	 * @param string $factoryId
	 * @return array
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('Record');
		if(!$result = $validate -> check($data)) {
			return errorMsg($validate->getError());
		}
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

		if(input('?post.record_id') && !input('?post.record_id') == ''){
			$where['id'] = $data['record_id'];
			$file = array(
				'logo_img','company_img','rb_img','factory_video','license','glory_img'
			);
			$oldRecordInfo = $this -> getInfo($where,$file);
			$data['update_time'] = time();
            $this->startTrans();
			$result = $this->allowField(true)->save($data,['id' => $data['record_id'],'factory_id'=>$factoryId]);
			if(false == $result){
				$this ->rollback();
				return errorMsg('失败！');
			}
			$modelStore = new \app\factory\model\Store;
			$whereStore = [
				['factory_id','=',$factoryId],
				['store_type','=',1],
			];
			$fileStore = ['s.id,s.logo_img'];
			$storeList = $modelStore->getList($whereStore,$fileStore);
			$ids = [];
			if(!empty($storeList)){
				foreach ($storeList as $k=>&$v){
					if($v['logo_img'] == $oldRecordInfo['logo_img'] && $data['logo_img'] != $oldRecordInfo['logo_img']){
						$ids[] = $v['id'];
					}
				}
				if(!empty($ids)){
					$data1 = [
						'logo_img' => $data['logo_img']
					];
					$where1 = [
						['id','in',$ids],
						['factory_id','=',$factoryId],
					];
					$result = $modelStore -> allowField(true)->save($data1,$where1);
					if(false == $result){
						$this ->rollback();
						return errorMsg('失败！');
					}
					$this->commit();
				}
			}
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
		}
		if(false !== $result){
			//删除旧图片
			if(input('?post.record_id') && !input('?post.record_id') == ''){
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
			}
			return successMsg("成功");
		}else{
			return errorMsg("失败");
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
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'r.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'r.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('r')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)?$list->toArray():[];
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'r.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('r')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}

}
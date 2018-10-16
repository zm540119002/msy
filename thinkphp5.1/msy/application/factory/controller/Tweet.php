<?php
namespace app\factory\controller;

class Tweet extends \common\controller\StoreBase
{
    //促销管理
    public function manage()
    {
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *编辑
     */
    public function edit()
    {
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $data = input('post.');
        $model = new \common\model\Tweet;
        if(isset($data['fileBase64']) && !empty($data['fileBase64'])){
            $result =  $this->uploadFileToTemp();
            $file = '';
            if($result['status']) {
                foreach ($result['info'] as $k => $v) {
                    $file .= moveImgFromTemp(config('upload_dir.factory_tweet'), basename($v)) . ',';
                }
                if ($data['release_type'] == 1) {
                    $data['img'] = $file;
                } else if ($data['release_type'] == 2 || $data['release_type'] == 3) {
                    $data['video'] = $file;
                }
            }
        }
        unset($data['fileBase64']);
        if($data['release_type'] == 4){
            if(isset($data['img']) && !empty($data['img'])){
                $_POST['fileBase64'] = $data['img'];
                $result =  $this->uploadFileToTemp();
                if($result['status']) {
                    $data['img'] = moveImgFromTemp(config('upload_dir.factory_tweet'), basename($result['info'])) . ',';
                }
            }
            if(isset($data['video']) && !empty($data['video'])){
                $_POST['fileBase64'] = $data['video'];
                $result =  $this->uploadFileToTemp();
                if($result['status']) {
                    $data['video'] = moveImgFromTemp(config('upload_dir.factory_tweet'), basename($result['info'])) . ',';
                }
            }
        }

        $data['store_id'] = $this->store['id'];
        $data['run_type'] = $this->store['run_type'];
        if(input('?post.id')){
            $data['update_time'] = time();
            $result = $model->allowField(true)->save($data,['id'=>input('post.id'),'store_id'=> $this->store['id']]);
            if($result === false){
                return successMsg('失败');
            }
        }else{
            $data['create_time'] = time();
            $result = $model->allowField(true)->save($data);
            if(!$result){
                return successMsg('失败');
            }
        }
        if($result){
            return successMsg('成功');
        }
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new \common\model\Promotion;
        $config=[
            'where'=>[
                ['p.store_id','=',$this->store['id']],
            ],
            'field'=>[
                'p.id','p.name','p.goods_ids','p.start_time','p.end_time','p.create_time','p.sort',
            ],
            'order'=>[
                'id'=>'desc','sort'=>'desc'
            ],
        ];
        $activityStatus = (int)input('get.activityStatus');
        if($activityStatus == 1){//未结束
            $config['where'][] = ['p.end_time','>',time()];
        }
        if($activityStatus == 0){//已结束
            $config['where'][] = ['p.end_time','<=',time()];
        }

        $keyword = input('get.keyword','');
        if($keyword){
            $config['where'][] = ['p.name', 'like', '%'.trim($keyword).'%'];
        }

        $list = $model -> pageQuery($config);
        $page = $list->getCurrentPage();
        $this->assign('page',$page);
        $this->assign('list',$list);
        if(isset($_GET['activityStatus'])){
            if($_GET['activityStatus'] == 1 ){//未结束
                return $this->fetch('list_notover');
            }
            if($_GET['activityStatus'] == 0 ){//结束
                return $this->fetch('list_over');
            }
        }
    }

    /**删除
     */
    public function  del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $model = new \common\model\Promotion();
        $data = input('post.');
        $condition = [
            ['store_id','=',$this->store['id']]
        ];
        if(is_array($data['id'])){
            $condition[] = ['id','in',$data['id']];
        }else{
            $condition[] = ['id','=',$data['id']];
        }
        return $model->del($condition);
    }
}
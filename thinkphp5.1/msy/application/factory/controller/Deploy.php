<?php
namespace app\factory\controller;

use common\controller\UserBase;

class Deploy extends UserBase
{
    //部署首页
    public function index(){
        return $this->fetch();
    }

    /**入驻登记
     */
    public function register()
    {
        $model = new \app\factory\model\Factory();
        $uid = $this -> user['id'];
        if(request()->isAjax()){
            if(input('?post.factory_id')){
                return $model -> edit($uid);
            }else{
                return $model -> add($uid);
            }
        }else{
            $mobilePhone = $this -> user['mobile_phone'];
            $this->assign('mobilePhone',$mobilePhone);
            if(input('?factory_id')){
                $factoryId = input('factory_id');
                $where = array(
                    'id' => $factoryId,
                );
                $factoryInfo =  $model -> getFactory($where);
                $this -> assign('factoryInfo',$factoryInfo);
            }
            return $this->fetch();
        }
    }

  

    //设置默认产商
    public function setDefaultFactory(){
        if(request()->isAjax()){
            $model = new \app\factory\model\Factory();
            $id = (int)input('post.id');
            if(!$id){
                return errorMsg('参数错误');
            }
            $model->startTrans();
            try{
                $data = array('is_default' => 1);
                $result = $model->allowField(true)->save($data,['id' => $id,'user_id'=>$this->user['id']]);
                $where['id']  = array('id','<>',$id);
                $where['user_id']  = $this->user['id'];
                $result = $model ->where($where)->setField('is_default',0);
                $model->commit();
                return successMsg("已选择");
            }catch(\Exception $e){
                $model->rollback();
            }
        }
    }
}
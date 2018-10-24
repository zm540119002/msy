<?php
namespace app\index\controller;
class Address extends \common\controller\UserBase {
    //增加修改地址页面
    public function edit(){
        $model = new \common\model\Address();
        $userId = $this->user['id'];
        if(request()->isPost()){
            $data = input('post.');
            if(input('?post.address_id') && !empty(input('post.address_id')) ){
                //开启事务
                $model -> startTrans();
                //修改
                $addressId = input('post.address_id');
                $condition = [
                    ['id','=',$addressId],
                    ['user_id','=',$userId],
                ];
                $result = $model -> edit($data,$condition);
                if( !$result['status'] ){
                    $model ->rollback();
                    return errorMsg('失败');
                }
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['id',"<>",$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return errorMsg('失败');
                    }
                }
                $model->commit();
                return successMsg('成功');
            }else{
                //增加
                $config = [
                    'where'=>[
                        ['user_id','=',$userId]
                    ],
                ];
                $addressList = $model -> getList($config);
                if(empty($addressList)){
                    $data['is_default'] = 1;
                }
                //开启事务
                $model -> startTrans();
                $data['user_id'] = $userId;
                $result = $model->edit($data);
                if(!$result['status']){
                    return errorMsg('失败');
                }
                $addressId = $result['id'];
                //修改其他地址不为默认值
                if($_POST['is_default'] == 1){
                    $where = [
                        ['id',"<>",$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model->where($where)->setField('is_default',0);
                    if(false === $result){
                        $model ->rollback();
                        return errorMsg('失败');
                    }
                }
                $model->commit();
                return successMsg('成功');
            }
        }

        $footerCartConfig = [5];
        if(input('?address_id') && !empty(input('address_id'))){
            $id = input('address_id');
            $config = [
                'where' => [
                    ['id','=',$id],
                    ['user_id','=',$userId],
                ],
            ];
            $address = $model -> getInfo($config);
            $this->assign('address',$address);
            $footerCartConfig = [6];
        }
        $unlockingFooterCart = unlockingFooterCartConfig($footerCartConfig);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this -> fetch();

    }

    //地址列表
    public function getList(){
        $model = new \common\model\Address();
        $config = [
            'where'=>[
                ['user_id','=',$this->user['id']]
            ],
        ];
        $addressList = $model -> getList($config);
        $this->assign('addressList',$addressList);
        $unlockingFooterCart = unlockingFooterCartConfig([7]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this -> fetch();
    }

    //删除地址
    public function del(){
        if(IS_POST){
            $model = new \common\model\Address();
            $where['user_id'] =$this->user['id'];
            $where['id'] = $_POST['addressId'];
            $result = $model -> del($where);
            if($result['status']){
                return successMsg('删除成功');
            }else{
                return errorMsg('删除失败');
            }
        }
    }

}
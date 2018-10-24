<?php
namespace app\index\controller;
class Address extends \common\controller\UserBase {
    //增加修改地址页面
    public function edit(){
        $model = new \common\model\Address();
        $userId = $this->user['id'];
        if(request()->isPost()){
            $data = $_POST;
            if(isset($_POST['addressId']) && !empty($_POST['addressId']) ){
                //开启事务
                $model -> startTrans();
                //修改
                $addressId = $_POST['addressId'];
                $condition = [
                    ['id','=',$addressId],
                    ['user_id','=',$userId],
                ];
                $result = $model -> edit($data,$condition);
                if( !$result['status'] ){
                    $model ->rollback();
                    return errorMsg('失败');
                }
                if($_POST['is_default'] == 1){
                    $data2 = [
                        'is_default'=>1,
                    ];
                    $condition = [
                        ['id','<>',$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model -> edit($data2,$condition);
                    if(!$result['status']){
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
                $addressId = $model->edit($data);
                if(!$addressId){
                    return errorMsg('失败');
                }
                if($_POST['is_default'] == 1){
                    $data2 = [
                        'is_default'=>1,
                    ];
                    $condition = [
                        ['id','<>',$addressId],
                        ['user_id','=',$userId],
                    ];
                    $result = $model -> edit($data2,$condition);
                    if(!$result['status']){
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
        $unlockingFooterCart = unlockingFooterCartConfig([0,1,2]);
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
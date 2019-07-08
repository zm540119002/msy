<?php
namespace app\api\controller;
class Address extends \common\controller\UserBase {
    //增加修改地址页面
    public function edit(){
        if(!request()->isPost()){
            $this->errorMsg('');
        }
        $model = new \common\model\Address();
        $model->useGlobalScope(false)->select();
        $userId = $this->user['id'];

        $data = input('post.');
        if(input('?post.id') && !empty(input('post.id')) ){
            //开启事务
            $model -> startTrans();
            //修改
            $addressId = input('post.id');
            $condition = [
                ['status','=',0],
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
                    ['status','=',0],
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
            $data['id'] = $addressId;
            return $data;
        }else{
            //增加
            $config = [
                'where'=>[
                    ['status','=',0],
                    ['user_id','=',$userId]
                ],
            ];
            $addressCount = $model -> where($config['where'])->count('id');
            if(!$addressCount){
                $data['is_default'] = 1;
            }
            //开启事务
            $model -> startTrans();
            $data['user_id'] = $userId;
            $id = $model->edit($data);
            if(!$id){
                return errorMsg('失败');
            }
            $addressId = $id['id'];
            //修改其他地址不为默认值
            if($_POST['is_default'] == 1){
                $where = [
                    ['status','=',0],
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
            $data['id'] = $addressId;
            return $data;
        }


    }

    //地址列表
    public function manage(){
        $model = new \common\model\Address();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']]
            ],
        ];
        $addressList = $model -> getList($config);
        $this->assign('addressList',$addressList);
        $unlockingFooterCart = unlockingFooterCartConfig([8]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this -> fetch();
    }

    //获取
    public function getList()
    {
        $model = new \common\model\Address();
        $config = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']]
            ],
        ];
        $addressList = $model -> getList($config);
        return $addressList;

    }
    //删除地址
    public function del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $id = input('post.address_id',0,'int');
        $model = new \common\model\Address();
        $condition = [
            ['id','=',$id],
        ];
        $result = $model -> del($condition);

        if($result['status']){
            return successMsg('删除成功');
        }else{
            return errorMsg('删除失败');
        }

    }

    /**
     * 获取地址列表  弹窗
     */
    public function popGetList(){

        $model= new \common\model\Address();

        $condition = [
            'field' => [
                'id','consignee','detail_address','tel_phone','mobile','is_default','status','province','city','area'
            ]
        ];

        $data = $model->getList($condition);

        $this->assign('addressList',$data);

        return view('pop_list');
        //echo  $this->fetch('pop_list');
    }


}
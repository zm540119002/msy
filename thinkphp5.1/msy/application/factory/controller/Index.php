<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
class Index extends UserBase
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**供应商列表页
     */
    public function factoryList(){
        $modelUserFactory = new \app\factory\model\UserFactory();
        $where = [
            ['status','=',0],
            ['user_id','=',$this->user['id']],
        ];
        $list = $modelUserFactory->where($where)->field('factory_id')->select()->toArray();
    }
}
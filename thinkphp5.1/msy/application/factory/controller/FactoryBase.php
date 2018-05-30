<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;
    protected $factoryList = null;

    public function __construct(){
        parent::__construct();
        $modelUserFactory = new \app\factory\model\UserFactory();
        $where = [
            ['status','=',0],
            ['user_id','=',$this->user['id']],
        ];
        $field = [
            'factory_id',
        ];
        $list = $modelUserFactory->getList($where,$field);
        $factoryCount = count($list);
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $info = $list[0];
            \common\cache\Factory::remove($info['factory_id']);
            $this->factory = \common\cache\Factory::get($info['factory_id']);
        }elseif($factoryCount>1){//入住多家供应商
            $this->factoryList = \common\cache\Factory::get(array_column($list,'factory_id'));
        }
        $this->assign('factoryList',$this->factoryList);
    }
}
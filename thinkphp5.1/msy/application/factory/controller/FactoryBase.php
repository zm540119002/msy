<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;
    
    public function __construct(){
        parent::__construct();
        $modelUserFactory = new \app\factory\model\UserFactory();
        $where = [
            ['status','=',0],
            ['user_id','=',$this->user['id']],
        ];
        $list = $modelUserFactory->where($where)->field('factory_id')->select()->toArray();
        $factoryCount = count($list);
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $info = $list[0];
            \common\cache\Factory::remove($info['id']);
            $this->factory = \common\cache\Factory::get($info['id']);
        }elseif($factoryCount>1){//入住多家供应商
            //如果按戴总的逻辑，这里停不下
        }
    }
}
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
            ['is_default','=',1],
            ['user_id','=',$this->user['id']],
        ];
        $info = $modelUserFactory->where($where)->field('factory_id')->find()->toArray();
        $factoryId = $info['factory_id'];
        if (!$factoryId){
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }
        \common\cache\Factory::remove($factoryId);
        $this->factory = \common\cache\Factory::get($factoryId);
    }
}
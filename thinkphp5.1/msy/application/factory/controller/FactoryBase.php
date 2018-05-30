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
            'factory_id','is_default',
        ];
        $list = $modelUserFactory->getList($where,$field);
        $factoryCount = count($list);
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $info = $list[0];
        }elseif($factoryCount>1){//入住多家供应商
            $info = [];
            foreach ($list as $val){
                if($val['is_default']){
                    $info = $val;
                }
            }
            if(empty($info)){//不存在默认供应商的情况
                $this->factoryList = \common\cache\Factory::get(array_column($list,'factory_id'));
            }
        }
        \common\cache\Factory::remove($info['factory_id']);
        $this->factory = \common\cache\Factory::get($info['factory_id']);
        $this->assign('factoryList',$this->factoryList);
    }

    //设置默认供应商
    public function setDefaultFactory(){
        $modelUserFactory = new \app\factory\model\UserFactory();
        return $modelUserFactory->setDefaultFactory($this->user['id']);
    }
}
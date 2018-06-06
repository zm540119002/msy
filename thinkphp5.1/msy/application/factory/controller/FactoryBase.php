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
        $list = $modelUserFactory->where($where)->field($field)->select();
        $list = $list->toArray();
        $factoryCount = count($list);
        $info = [];
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $info = $list[0];
        }elseif($factoryCount>1){//入住多家供应商
            foreach ($list as $val){
                if($val['is_default']){
                    $info = $val;
                    break;
                }
            }
            if(empty($info)){//不存在默认供应商的情况
                $this->factoryList = \common\cache\Factory::get(array_column($list,'factory_id'));
                $this->assign('factoryList',$this->factoryList);
            }
        }
        if(!empty($info)){
            \common\cache\Factory::remove($info['factory_id']);
            $this->factory = \common\cache\Factory::get($info['factory_id']);
        }
    }

    //设置默认供应商
    public function setDefaultFactory(){
        $modelUserFactory = new \app\factory\model\UserFactory();
        return $modelUserFactory->setDefaultFactory($this->user['id']);
    }

    //获取厂家详细信息
    public function getFactoryInfo(){
        $model = new \app\factory\model\Factory();
        $where = [
            ['f.id','=',$this->factory['id']],
        ];
        $file = [
            'f.id,f.name,r.logo_img'
        ];
        $join =[
            ['record r','r.factory_id = f.id',],
        ];
        return $model->getInfo($where,$file,$join);
    }
}
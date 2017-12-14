<?php
namespace Admin\Controller;
use Think\Controller;
use web\all\Controller\BaseController;

class PluginController extends BaseController {
    //后台首页
    public function index(){
        $this->display();
    }


    /*
    * 插件信息配置
    */
    public function setting(){

        $condition['type'] = I('get.type');
        $condition['code'] = I('get.code');

        $model = D('Plugin');
        $row = $model->where($condition)->find();
        $row['config'] = unserialize($row['config']);

        if(IS_POST){
            $config = I('post.config/a');
            //空格过滤
            $config = trim_array_element($config);

            if($config){
                $config = serialize($config);
            }
            $row = $model->where($condition)->save(array('config_value'=>$config));
            if($row){
                exit($this->success("操作成功"));
            }
            //exit($this->error("操作失败"));
        }

    }
}
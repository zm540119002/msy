<?php
namespace app\index_admin\controller;

use common\controller\Base;

class Node extends Base {
    /**代金券分类-管理
     */
    public function manage(){
        $model = D('Mall/');
        if(IS_POST){
            $this->List = $model->select();
            $this->fetch('nodeList');
        }else{
            $this->List = $model->select();
            $this->fetch();
        }
    }

    /**代金券分类-编辑
     */
    public function edit(){
        $model = D('Mall/');
        if(IS_POST){
            //日期转时间戳
            if( isset($_POST['failure_time']) && $_POST['failure_time']){
                $_POST['failure_time'] = strtotime($_POST['failure_time']);
            }
            if(isset($_POST['nodeId']) && intval($_POST['nodeId'])){
                $_POST['update_time'] = time();
                $res = $model->save();
            }else{
                $_POST['create_time'] = time();
                $res = $model->add();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['nodeId']) && intval($_GET['nodeId'])){
                $nodeId = input('get.nodeId', 0, 'int');
                $where = array(
                    'c.id' => $nodeId,
                );
                $Info = $model->select($where);
                $Info = $Info[0];
                //格式化失效时间
                if($Info['failure_time']){
                    $Info['failure_time'] = date('Y-m-d',$Info['failure_time']);
                }
                $this->Info = $Info;
            }
            $this->TypeList = config('ARR')['_type'];
            $this->SceneList = config('ARR')['_scene'];
            $this->fetch();
        }
    }

    //代金券列表
    public function nodeList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(config('not_get')));
        }

        $model = D('Mall/');
        $where = array(
            'c.status' => 0,
        );
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'c.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );

        $order = 'c.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? input('get.pageSize',0,'int') : config('custom.default_page_size');

        $List = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='c');

        $this->List = $List['data'];
        $this->pageList = $List['pageList'];
        $this->fetch();
    }

    /**代金券分类-删除
     */
    public function del(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(config('not_post')));
        }
        $model = D('Mall/');
        $res = $model->del();
        $this->ajaxReturn($res);
    }
}
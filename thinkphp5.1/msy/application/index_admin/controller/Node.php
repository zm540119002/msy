<?php
namespace app\index_admin\controller;

use common\controller\Base;

class Node extends Base {
    /**节点-管理
     */
    public function manage(){
        if(request()->isPost()){
            $modelNode = \common\model\Node();
            $this->fetch('nodeList');
        }else{
            return $this->fetch();
        }
    }
    /**节点-编辑
     */
    public function edit(){
        if(request()->isPost()){
            $modelNode = \common\model\Node();
            return $modelNode;
        }else{
            return $this->fetch();
        }
    }
    /**节点-列表
     */
    public function nodeList(){
//        $this->assign('list',array(array('name'=>'zhangmin'),array('name'=>'zhanglingjuan')));
        return $this->fetch();
        if(!request()->isGet()){
            return config('not_get');
        }
        $modelNode = '';
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
        $List = page_query($modelNode,$where,$field,$order,$join,$group,$pageSize,$alias='c');

        $this->List = $List['data'];
        $this->pageList = $List['pageList'];
        $this->fetch();
    }
    /**节点-删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('not_post');
        }
        $modelNode = '';
        $res = $modelNode->del();
        return $res;
    }
}
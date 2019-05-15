<?php
namespace app\index_admin\controller;

/**
 * 广告位控制器基类
 */
class AdPositions extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\AdPositions();
    }

    public function manage(){
        return $this->fetch();
    }

    /**
     * @return array
     * 编辑
     */
    public function edit(){
        $model = $this->obj;

        if(!request()->isPost()){
            //要修改的方案
            if($id = input('param.id/d')){
                $condition = ['where' => [['id','=',$id]]];
                $info = $model->getInfo($condition);

                $this->assign('info',$info);
            }
            return $this->fetch();

        }else{
            // 基础处理
            $data = input('post.');
            unset($data['editorValue']);

            $data['update_time'] = time();

            if(isset($data['id']) && $id=input('post.id/d')){//修改
                // 编辑
                $condition = ['where' => ['id' => $id,]];

                $result= $model->edit($data,$condition['where']);
                if(!$result['status']) return $result;

            } else{
                //新增
                $data['create_time'] = time();
                $result = $model->edit($data);
                if(!$result['status']) return $result;

            }
            return successMsg('成功');
        }
    }

    /**
     *  分页查询
     */
    public function getList(){

        $condition = [
            'field'=>['id','name','shelf_status'],
        ];
        // 条件
        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'] = ['name','like', '%' . trim($keyword) . '%'];

        $list = $this->obj->pageQuery($condition);
        $this->assign('list',$list);

        return view('list_tpl');
    }


}
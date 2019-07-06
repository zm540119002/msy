<?php
namespace app\index_admin\controller;

/**
 * 广告控制器基类
 * ad,advertising,advert 单词 广告插件会拦截
 */
class SignUp extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'getList,del'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\SignUp();
    }

    public function manage(){
        return $this->fetch('manage');
    }

    /**
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        if(!input('?post.id')&&!input('?post.ids'))  return errorMsg('失败');

        $condition = array();

        if($id = input('post.id/d')){
            $condition = [['id','=',$id]];
        }
        if($ids = input('post.ids/a')){
            $condition = [['id','in',$ids]];
        }

        $result= $this->obj->del($condition);

        return $result;
    }
    /**
     *  分页查询
     */
    public function getList(){

        $condition = [
            'field' => ['id','name','mobile','status','create_time'],
            'where' => [
                ['status','=',0],
            ],
        ];

        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'] = ['name','like', '%' . trim($keyword) . '%'];

        $list = $this->obj->pageQuery($condition);
        $this->assign('list',$list);

        return view('list_tpl');
    }



}
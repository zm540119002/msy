<?php
namespace app\factory\controller;
use think\Controller;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function enters()
    {
        if(request()->isPost()){
            $data = input('post.');
            // validate
            $validate = validate('Factory');
            if(!$validate->check($data)) {
                $this->error($validate->getError());
            }
            
            $this->success('1');
        }

        return $this->fetch();

    }

}
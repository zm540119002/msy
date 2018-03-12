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
        if($this->request->isPost()){
            print_r(input());
        }else{
            return $this->fetch();
        }
    }
    public function a()
    {
        if($this->request->isPost()) {
            print_r(input('img'));
        }
    }

}
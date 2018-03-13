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
            $validate = new \app\factory\validate\Factory;
//            print_r($validate);exit;

            if (!$validate::check($_POST)) {
                echo 1;exit;
                dump($validate->getError());
            }
            $this->success('1');
        }

        return $this->fetch();

    }

}
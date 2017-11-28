<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {
    //美尚云-美容机构
    public function index(){
        $this->display();
    }

    //美尚云-从业人员
    public function practitioner(){
        $this->display();
    }
}
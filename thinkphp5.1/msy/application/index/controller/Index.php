<?php
namespace app\index\controller;

use think\Controller;
use Request;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
<<<<<<< HEAD
=======
        return $this->fetch();
    }

    public function hello()
    {
>>>>>>> 8b86c73c5e5e133e08364efaefba68fda42f7d0b
        return $this->fetch();
    }
}

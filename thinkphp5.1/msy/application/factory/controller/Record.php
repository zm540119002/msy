<?php
namespace app\factory\controller;

use common\controller\Base;

class Record extends Base
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }
    /**首页
     */
    public function edit()
    {
        return $this->fetch();
    }

    /**预览
     */
    public function preview()
    {
        return $this->fetch();
    }


}
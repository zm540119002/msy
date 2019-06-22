<?php
namespace common\controller;

/**店铺基础类，需要判断是否入驻店铺的类需要继承
 */
class BaseApi extends Base
{
    protected $getData = null;
    protected $postData = null;

    public function __construct(){
        parent::__construct();
    }
}
<?php
namespace app\index\controller;

/**
 * 促销控制器
 */
class Promotion extends \common\controller\Base{

    public function detail(){
        $this->assign('relation',config('custom.relation_type.promotion'));
        return $this->fetch();
    }
}
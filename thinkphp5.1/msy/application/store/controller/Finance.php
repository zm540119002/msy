<?php
/**
 * Created by PhpStorm.
 * User: Mr.WEI
 * Date: 2018/7/13
 * Time: 10:12
 * 财务管理控制器
 */

namespace app\store\controller;
use app\store\model\Finance as FinanceModel;

class Finance extends \common\controller\StoreBase
{
    private $finance;

    public function __construct()
    {
        parent::__construct();
        if( !is_object($this->finance) ){
            $this->finance = new FinanceModel();
        }
    }

    public function index()
    {
        //财务管理首页
        return $this->fetch();
    }

    public function getApp()
    {
        return $this->fetch();
    }

    public function cre()
    {
        return $this->finance->createFinanceId();
    }

    public function pay()
    {
        return $this->q();
    }
}
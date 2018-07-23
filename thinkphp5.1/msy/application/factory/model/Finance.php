<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 17:21
 */

namespace app\factory\model;

use think\Model;

class Finance extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'finance';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'id';

    public function getFinance(){
        $this->field('*')->find();
    }

    public function getStatus(){
        $this->alias('f')->select();
    }
}
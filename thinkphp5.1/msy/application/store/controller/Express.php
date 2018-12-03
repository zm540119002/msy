<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/6/11
 * Time: 10:33
 */
namespace app\store\controller;

use app\store\model\Express as ExpressModel;

class Express extends \common\controller\StoreBase
{
    private $express;

    public function __construct()
    {
        parent::__construct();
        if (!is_object($this->express)) {
            $this->express = new ExpressModel;
        }
    }

    /**
     * 添加物流信息
     * @param $order_id
     * @param $express_name
     * @param $express_code
     * @return array
     */
    public function addExpress($order_id, $express_name, $express_code)
    {
        $ret = $this->express->addExpress($order_id, $express_name, $express_code);
        if($ret['status']==0) {
            return $ret;
        }
        $data = ['data'=>"
                    <dl>
                        <dt>物流公司：
                            <input type='text' value='{$express_name}' id='name_{$ret["express_id"]}' />
                        </dt>
                        <dt>物流单号：
                            <input type='text' value='{$express_code}' id='code_{$ret["express_id"]}' />
                        </dt>
                        <dt class='operation'>
                            <span class='update_express' data='{$ret["express_id"]}'>修改</span>
                            <span class='del_express' data='{$ret["express_id"]}'>删除</span>
                        </dt><span>删除</span>
                    </dl>           
        "];
        return array_merge($ret, $data) ;
    }

    public function deleteExpress($id)
    {
        return $this->express->deleteExpress($id);
    }

    public function updateExpress($id, $express_name, $express_code)
    {
        return $this->express->updateExpress($id, $express_name, $express_code);
    }

}
<?php
namespace Home\Controller;

use Common\Controller\AuthSellerController;

/**卖手平台-从业人员端-卖手认证基类
 */
class BaseAuthSellerController extends AuthSellerController{
    public function __construct(){
        parent::__construct();
    }

    /**获取任务单项目表信息
     */
    protected function getProjectByTaskOrderId($taskOrderId){
        $model = M('task_order_project tp');
        $where = array(
            'tp.task_order_id' => $taskOrderId,
        );
        $field = array(
            'tp.id','tp.project_id','p.name project_name',
        );
        $join = array(
            ' left join project p on tp.project_id = p.id ',
        );
        $projectList = $model->where($where)->field($field)->join($join)->select();
        $projectInfo = '';
        foreach ($projectList as $key => $val){
            if(!$key){
                $projectInfo = '[' . $val['project_name'] . ']';
            }else{
                $projectInfo .= '+[' . $val['project_name'] . ']';
            }
        }
        return $projectInfo;
    }

    /**任务单ID
     */
    protected function getTaskOrderInfoById($taskOrderId){
        $model = M('task_order t');
        $where = array(
            't.status' => 0,
            't.user_id' => $this->user['id'],
            't.id' => $taskOrderId,
        );
        $field = array(
            't.id','t.name','t.no','t.create_time','t.expect_date','t.expect_days','t.user_id','t.online','t.sign_status','t.type',
            't.invite','t.seller_quantity','t.seller_appearance_fee','t.pay_seller_amount','t.seller_share_proportion',
            't.payment_way','t.stipulation_instructions','t.training_course_requirements','t.create_time',
            'c.id company_id','c.name company_name','c.registrant','c.address','c.registrant_mobile','c.province','c.city','c.area',
            's.id seller_id','s.name seller_name',
        );
        $join = array(
            'left join seller s on t.seller_id = s.id ',
            'left join company c on t.company_id = c.id ',
        );

        //任务单
        $taskOrder = $model->where($where)->field($field)->join($join)->find();
        if($taskOrder === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        if($taskOrder){
            //计划日程
            if($taskOrder['expect_date']){
                $taskOrder['scheduleDate'] = $taskOrder['expect_date'];
                if($taskOrder['expect_days']){
                    $taskOrder['scheduleDate'] .= ' 至 ' . date('Y-m-d H:i',strtotime($taskOrder['expect_date'] . ' +' . $taskOrder['expect_days'] . ' day'));
                }
            }

            //任务单项目
            $model = M('task_order_project tp');
            $where = array(
                'tp.task_order_id' => $taskOrder['id'],
            );
            $field = array(
                'tp.id','tp.project_id','p.name project_name',
            );
            $join = array(
                ' left join project p on tp.project_id = p.id ',
            );
            $projectList = $model->where($where)->field($field)->join($join)->select();
            $taskOrder['project'] = '';
            foreach ($projectList as $key => $val){
                if(!$key){
                    $taskOrder['project'] = '[' . $val['project_name'] . ']';
                }else{
                    $taskOrder['project'] .= '+[' . $val['project_name'] . ']';
                }
            }

            //下单店家-即机构
            $company = array(
                'company_province_city_area' => array(
                    'province' => $taskOrder['province'],
                    'city' => $taskOrder['city'],
                    'area' => $taskOrder['area'],
                ),
            );
        }
        $returnArray = array_merge($company,$taskOrder);
        return $returnArray;
    }

    //验证是否应征
    protected function isEnlist($taskOrderId){
        $model = M('task_order_seller_enlist_signing');
        $where = array(
            'seller_id' => $this->seller['id'],
            'task_order_id' => $taskOrderId,
            'status' => 0,
        );
        $res = $model->where($where)->count();
        return $res ? true : false;
    }

    /**卖手-我的接单-列表
     */
    protected function getTaskOrderListBySellerId($sellerId,$signStatus){
        $model = M('task_order_seller_enlist_signing ts');
        $where = array(
            'ts.seller_id' => $sellerId,
            't.sign_status' => $signStatus,
        );
        $field = array(
            't.id','t.name','t.no','t.create_time','t.expect_date','t.expect_days','t.user_id','t.online','t.sign_status',
            't.type','t.invite','t.seller_id','t.seller_quantity','t.seller_appearance_fee','t.pay_seller_amount',
            't.seller_share_proportion','t.payment_way','t.stipulation_instructions','t.training_course_requirements','t.create_time',
            'c.name company_name','c.registrant','c.address','c.registrant_mobile','c.province','c.city','c.area',
        );
        $join = array(
            'left join task_order t on t.id = ts.task_order_id ',
            'left join company c on t.company_id = c.id ',
        );
        $taskOrderList = $model->where($where)->field($field)->join($join)->select();
        return empty($taskOrderList) ? [] : $taskOrderList;
    }
}



<?php
namespace Home\Controller;

use web\all\Controller\AuthCompanyAuthoriseController;

/**卖手平台-机构端-机构认证基类
 */
class BaseAuthCompanyController extends AuthCompanyAuthoriseController{
    public function __construct(){
        parent::__construct();
    }

    //新增任务单
    protected function addTaskOrder(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $model = M('task_order');
        //开启事务
        $model->startTrans();

        $rules = array(
            array('name','require','任务单标题必须！'),
        );
        $_POST['user_id'] = $this->user['id'];
        $_POST['company_id'] = $this->company['id'];
        $_POST['create_time'] = time();
        $_POST['no'] = date('YmdHis',time()) . create_random_str();

        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $id = $model->add();
        if(!$id){
            $model->rollback();//事务回滚
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        if(isset($_POST['projectIds']) && count($_POST['projectIds'])){
            $model_task_order_project = M('task_order_project');
            $data_task_order_project = array(
                'task_order_id' => $id,
            );

            foreach ($_POST['projectIds'] as $val){
                $project_id = intval($val);
                if($project_id){
                    $data_task_order_project['project_id'] = $project_id;
                    $task_order_project_id = $model_task_order_project->add($data_task_order_project);
                    if(!$task_order_project_id){
                        $model->rollback();//事务回滚
                        $this->ajaxReturn(errorMsg($model_task_order_project->getError()));
                    }
                }
            }
        }

        $returnData = array(
            'id'=>$id,
        );

        //事务提交
        $model->commit();
        $this->ajaxReturn(successMsg('新增成功',$returnData));
    }

    //修改任务单
    protected function saveTaskOrder(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        unset($_POST['id']);

        if(!isset($_POST['taskOrderId']) || !$_POST['taskOrderId']){
            $this->ajaxReturn(errorMsg('缺少任务单ID参数！'));
        }

        $model = M('task_order');
        //开启事务
        $model->startTrans();

        $rules = array(
            array('name','require','任务单标题必须！'),
        );
        $_POST['update_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $id = intval($_POST['taskOrderId']);
        $where = array(
            'id' => $id,
            'user_id' => $this->user['id'],
            'status' => 0,
        );
        $res = $model->where($where)->save();
        if(false === $res){
            $model->rollback();//回滚
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        if( isset($_POST['projectIds']) && count($_POST['projectIds']) ){
            $newProjectIdArr = $_POST['projectIds'];
        }
        $model_task_order_project = M('task_order_project');
        $oldProjectIdArr = $model_task_order_project->where(array('task_order_id' => $id))->getField('project_id',true);

        $addProjectIdArr = array_diff ($newProjectIdArr ,$oldProjectIdArr );
        if(!empty($addProjectIdArr)){
            $res = $this->_addTaskOrderProjectByIds($model_task_order_project,$addProjectIdArr,$id);
            if(false === $res){
                $model->rollback();//回滚
                $this->ajaxReturn(errorMsg($model_task_order_project->getError()));
            }
        }

        $delProjectIdArr = array_diff ($oldProjectIdArr ,$newProjectIdArr );
        if(!empty($delProjectIdArr)){
            $res = $this->_delTaskOrderProjectByIds($model_task_order_project,$delProjectIdArr,$id);
            if(false === $res){
                $model->rollback();//回滚
                $this->ajaxReturn(errorMsg($model_task_order_project->getError()));
            }
        }

        //提交
        $model->commit();

        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('修改成功',$returnData));
    }

    //新增task_order_project记录
    private function _addTaskOrderProjectByIds($model,$idsArr,$id){
        foreach ($idsArr as $item) {
            $_data[] = array(
                'task_order_id' => $id,
                'project_id' => $item,
            );
        }
        return $model->addAll($_data);
    }

    //删除task_order_project记录
    private function _delTaskOrderProjectByIds($model,$idsArr,$id){
        $where = array(
            'task_order_id' => $id,
            'project_id' => array('in',$idsArr),
        );
        return $model->where($where)->delete();
    }

    //删除任务单
    protected function _delTaskOrder(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }

        if(!isset($_POST['taskOrderId']) || !$_POST['taskOrderId']){
            $this->ajaxReturn(errorMsg('缺少任务单ID参数！'));
        }

        $model = M('task_order');
        $id = intval($_POST['taskOrderId']);
        $where = array(
            'id' => $id,
            'user_id' => $this->user['id'],
            'company_id' => $this->company['id'],
        );
        $res = $model->where($where)->setField('status',2);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('删除成功'));
    }

    /**任务单-统计
     * @param array $where查询条件
     * @return int
     */
    protected function countTaskOrder($where = []){
        $model = M('task_order');
        $_where = array(
            'status' => 0,
        );
        return $model->where(array_merge($_where,$where))->count();
    }

    /**任务单ID
     * @param $taskOrderId
     * @return array
     */
    protected function getTaskOrderInfoById($taskOrderId){
        $model = M('task_order t');
        $where = array(
            't.status' => 0,
            't.user_id' => $this->user['id'],
            't.company_id' => $this->company['id'],
            't.id' => $taskOrderId,
        );
        $field = array(
            't.id','t.name','t.no','t.create_time','t.expect_date','t.expect_days','t.user_id',
            't.sign_status','t.type','t.invite','t.seller_id','s.name seller_name','t.online',
            't.seller_quantity','t.seller_appearance_fee','t.pay_seller_amount','t.seller_share_proportion',
            't.payment_way','t.stipulation_instructions','t.training_course_requirements','t.create_time',
        );
        $join = array(
            'left join seller s on t.seller_id = s.id '
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
        }

        //下单店家-即机构
        $company = array(
            'company_id' => $this->company['id'],
            'company_name' => $this->company['name'],
            'registrant' => $this->company['registrant'],
            'registrant_mobile' => $this->company['registrant_mobile'],
            'address' => $this->company['address'],
            'company_level' => $this->company['level'],
            'company_province_city_area' => array(
                'province' => $this->company['province'],
                'city' => $this->company['city'],
                'area' => $this->company['area'],
            ),
        );

        $returnArray = array_merge($company,$taskOrder);

        return $returnArray;
    }

    //收藏卖手-查询
    protected function selectSellerFavorites($where=[],$field=[],$join=[]){
        $model = M('seller_favorites sf');
        $_where = array(
        );
        $_field = array(
            'sf.id','sf.seller_id','sf.company_id','sf.create_time','sf.status',
            's.name','s.nickname','s.sex','s.type','s.avatar',
        );
        $_join = array(
            ' left join seller s on s.id = sf.seller_id '
        );
        $sellerList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($sellerList) ? [] : $sellerList;
    }
}



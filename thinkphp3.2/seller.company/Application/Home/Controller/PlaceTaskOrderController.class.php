<?php
namespace Home\Controller;

class PlaceTaskOrderController extends BaseAuthCompanyController {
    //下任务单
    public function taskOrder(){
        if(IS_POST){
            if(isset($_POST['taskOrderId']) && $_POST['taskOrderId'] ){//修改
                $this->saveTaskOrder();
            }else{//新增
                $this->addTaskOrder();
            }
        }else{
            $sellerType = I('get.sellerType',0,'int');
            $this->assign('sellerType',$sellerType);

            //我要指派给已收藏的卖手
            $where = array(
                'sf.status' => 0,
                'sf.company_id' => $this->company['id'],
            );
            $sellerList = $this->selectSellerFavorites($where);
            foreach ($sellerList as &$item){
                $item['grade'] = $this->getSellerAvgScoreById($item['seller_id']);
            }
            $this->assign('sellerList',$sellerList);

            //任务单
            $taskOrderId = I('get.taskOrderId',0,'int');
            $model = M('task_order t');
            $where = array(
                't.status' => 0,
                't.id' => $taskOrderId,
                't.user_id' => $this->user['id'],
                't.company_id' => $this->company['id'],
            );
            $field = array(
                't.id','t.name','t.no','t.create_time','t.expect_days','t.seller_id','t.seller_quantity','t.stipulation_instructions',
                't.type','t.sign_status','t.expect_date','t.seller_appearance_fee','t.seller_share_proportion','t.payment_way',
                't.training_course_requirements','t.pay_seller_amount',
            );

            $taskOrder = $model->where($where)->field($field)->find();
            $this->assign('taskOrder',$taskOrder);

            if($sellerType == 1 || $sellerType == 2){
                //店销活动拟开展的项目
                if($taskOrder['id']){
                    $model = M('task_order_project tp');
                    $where = array(
                        'tp.task_order_id' => $taskOrder['id'],
                    );
                    $field = array(
                        'tp.id','tp.task_order_id','tp.project_id',
                    );

                    $taskOrderProject = $model->where($where)->field($field)->select();
                    $this->assign('taskOrderProject',array_column($taskOrderProject,'project_id'));
                }

                $model = M('project');
                $where = array(
                    'type' => 1,
                    'status' => 0,
                    'online' => 1,
                    'founder_id' => $this->user['id'],
                );
                $filed = array(
                    'id','home_focus_img',
                );
                //美容机构自有项目
                $projectSelf = $model->where($where)->field($filed)->select();
                $this->assign('pojectSelf',$projectSelf);

                $where = array(
                    'type' => 2,
                    'status' => 0,
                    'online' => 1,
                    'founder_id' => $this->user['id'],
                );
                //美尚平台推荐项目
                $projectRecommend = $model->where($where)->field($filed)->select();
                $this->assign('projectRecommend',$projectRecommend);

                $this->display('taskOrderSell');
            }elseif($sellerType == 3){
                $this->display('taskOrderTrain');
            }else{
                $this->error('错误的卖手类型');
            }
        }
    }
}

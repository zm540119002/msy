<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class CommentController extends AuthUserController {
    /**
     *
     */
    public function EditComment(){
        $model = D('Comment');
        if(IS_POST){
            if(isset($_POST['commentId']) && intval($_POST['commentId'])){//修改
                $where = array(
                    'gb.id' => I('post.commentId',0,'int'),
                );
                $res = $model->saveComment();
            }else{//新增
                $_POST['create_time'] = time();
                $_POST['user_id'] = $this->user['id'];
                $res = $model->addComment();
            }
            $this->ajaxReturn($res);
        }else{
            $this->orderId = $_GET['orderId'];
            $this->aveScore = round($model->avg('score'),1);//平均分数
//            $this->userCommentNum =$model->count('distinct(user_id)');//多少用户评价
            $this->userCommentNum =$model->count();//多少用户评价
            $this->scoreInfo = $model->field('score,count(*) as num')->group('score')->select();//按评分分组
            $this->display();
        }
    }
}
<?php
namespace SellerCompany\Controller;

class SellerFavoritesController extends BaseAuthCompanyController {
    public function index(){
        if(IS_POST){
        }else{
            $where = array(
                'sf.company_id' => $this->company['id'],
                'sf.status' => 0,
            );
            $sellerList = $this->selectSellerFavorites($where);
            foreach ($sellerList as &$item){
                $item['grade'] = $this->getSellerAvgScoreById($item['seller_id']);
            }
            $this->assign('sellerList',$sellerList);

            $this->display();
        }
    }

    //卖手详情
    public function sellerDetail(){
        if(IS_POST){
        }else{
            $sellerId = I('get.sellerId',0,'int');

            $where = array(
                's.auth_status' => 2,
                's.id' => $sellerId,
                's.user_id' => $this->user['id'],
            );
            $field = array(
                's.intro','s.goods_skills',
            );
            //卖手信息
            $seller = $this->selectSeller($where,$field);
            $seller = $seller[0];
            $seller['grade'] = $this->getSellerAvgScoreById($sellerId);
            $this->assign('seller',$seller);

            $this->display();
        }
    }

    //卖手取消收藏
    public function sellerFavoritesCancel(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }

        $model = M('seller_favorites');

        if(!isset($_POST['seller_id']) || !intval($_POST['seller_id'])){
            $this->error('参数seller_id错误');
        }
        $id = I('post.seller_id',0,'int');
        $where = array(
            'seller_id' => $id,
            'company_id' => $this->company['id'],
        );
        $res = $model->where($where)->setField('status',1);
        if($res === false){
            $this->error($model->getError());
        }

        $returnArray = array(
            'id' => $id,
        );
        $this->ajaxReturn(successMsg('已取消',$returnArray));
    }
}

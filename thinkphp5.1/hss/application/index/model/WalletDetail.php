<?php
namespace app\index\model;

class WalletDetail extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'wallet_detail';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'wd';

	/**充值支付回调
	 * @param $parameter
	 */
	public function rechargeHandle($data,$info)
	{
		$modelWalletDetail = new \app\index\model\WalletDetail();
		$modelWalletDetail->startTrans();
		//更新订单状态
		$data2 = [];
		$data2['recharge_status'] = 2;
		$data2['pay_code'] = $data['pay_code'];
		$data2['pay_sn'] = $data['pay_sn'];
		$data2['payment_time'] = $data['payment_time'];
		$condition = [
			['user_id', '=', $info['user_id']],
			['sn', '=', $data['order_sn']],
		];
		$res = $modelWalletDetail->allowField(true)->save($data2,$condition);
		if($res === false){
			$modelWalletDetail->rollback();
			//返回状态给微信服务器
			return errorMsg('失败');
		}
		$modelWallet = new \app\index\model\Wallet();
		$config = [
			'where'=>[
				['user_id', '=', $info['user_id']],
			]
		];
		$walletInfo = $modelWallet->getInfo($config);
		if(empty($walletInfo)){
			$data3 = [
				'user_id' => $info['user_id'],
				'amount' =>  $info['amount'],
			];
			$res = $modelWallet->isUpdate(false)->save($data3);
			if(!$res){
				$modelWallet->rollback();
				//返回状态给微信服务器
				return errorMsg('失败');
			}
		}else{
			$where = [
				['user_id', '=', $info['user_id']],
			];
			$res = $modelWallet->where($where)->setInc('amount', $info['amount']);
			if($res === false){
				$modelWallet->rollback();
				//返回状态给微信服务器
				return errorMsg('失败');
			}
		}

		$modelWalletDetail->commit();//提交事务
		//返回状态给微信服务器
		return successMsg('成功');
	}

	/**钱包支付回调
	 * @param $parameter
	 */
	public function walletPaymentHandle($data)
	{
		$modelWalletDetail = new \app\index\model\WalletDetail();
		$modelWalletDetail->startTrans();
		//生成钱包明细
		$data2 = [];
		$data2['recharge_status'] = 2;
		$data2['pay_code'] = 4;
		$data2['pay_sn'] = $data['sn'];
		$data2['payment_time'] = $data['payment_time'];
		$data2['user_id'] = $data['user_id'];
		$data2['sn'] = $data['sn'];
		$data2['create_time'] = $data['payment_time'];
		$data2['amount'] = $data['actually_amount'];
		$res = $modelWalletDetail->allowField(true)->save($data2);
		if($res === false){
			$modelWalletDetail->rollback();
			//返回状态给微信服务器
			return errorMsg('失败');
		}
		$modelWallet = new \app\index\model\Wallet();
		$where = [
			['user_id', '=', $data['user_id']],
		];
		$res = $modelWallet->where($where)->setInc('amount', -1*$data['actually_amount']);
		if($res === false){
			$modelWallet->rollback();
			//返回状态给微信服务器
			return errorMsg('失败');
		}
		$modelWalletDetail->commit();//提交事务
		//返回状态给微信服务器
		return successMsg('成功');
	}
}
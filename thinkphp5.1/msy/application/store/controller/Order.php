<?php
/**
 * @author :  Mr.wei
 * @date : 2018-05-07
 * @effect : 厂商的订单管理
 */
namespace app\store\controller;

use app\store\model\Order as OrderModel;

class Order extends ShopBase
{
    private  $order;

    public function __construct()
    {
        parent::__construct();
        if(!is_object($this->order)){
            $this->order = new OrderModel;
        }
    }

    /**
     *订单首页
     *
     */
    public function index()
    {
        $this->assign( 'list', $this->order->getOrderList($this->store['id']) );
        return $this->fetch();
    }

    /**
     * 测试合成图片功能
     * @return string
     *
     */
    public function  test()
    {
        return $this->compose();
    }

    /**
     * 售前
     *
     */
    public function beforeSale()
    {
        return $this->fetch('index');
    }

    /**
     * 出仓
     *
     */
    public function out()
    {
        return $this->fetch();
    }

    /**
     * 发货/完成
     *
     */
    public function delivery()
    {
        return $this->fetch('index');
    }

    /**
     * 填单
     *
     */
    public function bill()
    {
        return $this->fetch();
    }

    /**
     *售后
     *
     */
    public function afterSale()
    {
        return $this->fetch('index');
    }

    //根据订单号查订单详情
    public function  getOrderDetail($order_id, $data_html=true)
    {
        $ret = $this->order->getOrderDetail($this->store['id'], $order_id);
        if($ret['status']=0){
            return $ret['info'];
        }
        $html = "";
        $html .="
            <dl>
				<dt class='role-label'><strong>订单号：<span>{$ret['order']['order_sn']}</span></strong></dt>
				<dd>
					<p>下单时间：{$ret['order']['create_time']}</p>
					<p>分类：购买商品 ??</p>
					<p>支付状态：{$ret['order']['status']}</p>
					<p>订单状态：{$ret['order']['status_unpack']}</p>
					<p>总价：￥ {$ret['order']['pay_money']} 元</p>
					<p>收货人：{$ret['order']['consignee']}</p>
					<p>收货手机：{$ret['order']['phone']}</p>
					<p>订单来源：{$ret['order']['source']}</p>
					<p><strong>支付金额：￥ {$ret['order']['pay_money']} 元</strong></p>
					<p>支付方式：{$ret['order']['pay_method']}</p>
					<p>支付备注：{$ret['order']['remark']}</p>
					</dd>
			</dl>
        ";
        $html .= "
            <dl>
				<dd>
					<p>收货人：{$ret['order']['consignee']}</p>
					<p>收货手机：{$ret['order']['phone']}</p>
					<p>订单来源：{$ret['order']['source']}</p>
					<p>支付备注：{$ret['order']['remark']}</p>
				</dd>
			</dl>
        ";
        foreach($ret['order_detail'] as $v){
            $html .= "
                <dl>
                    <dd>
                        <p><img src='".config('template.tpl_replace_string.public_uploads')."/{$v["thumb_img"]}' /></p>
                        <p>商品名称：{$v['name']}</p>
                        <p>购买数量：{$v['number']}</p>
                        <p>发货数量：<span id='goods_{$v['goods_id']}'>{$v['send_number']}</span></p>
                        <p>当时销售价：{$v['goods_price']}</p>
                        <p>售后价：{$v['after_sale_price']}</p>
                    </dd>
                </dl>
            ";
        }
        $html .= "<div class='print' data='{$ret['order']['order_sn']}' data_id='{$ret['order']['order_id']}'>
            打印订单</div>";
        if($data_html){
            return response($html);
        }
        return $html;
    }
    
    /**
     * 填写物流单号
     * @param number|string $order_id 订单号
     * @param number|string $express_id  物流单号
     * @param number $name_id 物流公司代号
     * @return boolean
     */
    public function setExpress($order_id, $express_id, $name_id)
    {
        return true;
    }

    /**
     * 更改订单状态
     * @param number|string $order_id 订单号
     * @param number $status 订单状态
     * @return boolean
     */
    public function setStatusUnpack($order_id, $status_unpack)
    {
        return $this->order->setStatusUnpack($this->store['id'], $order_id, $status_unpack);
    }

    /**
     * 订单商品数据与扫描数据比对
     * @param json $scan 扫描到的商品数据
     * @param number|string $order_id 订单号
     * @return boolean|json|array 比对完全一致或返回其差异数据
     */
    public function compareGoods($order_id, $scan){
        return true;
    }

    public function isOwnOrder($order_sn)
    {
        //是否拥有此订单
        $ret = $this->order->isOwnOrder($this->store['id'], $order_sn);
        if($ret['status']==0){
            return $ret;
        }
        $data = [
            'order_id'=>$ret['order_id'] ,
            'status_unpack'=>$ret['status_unpack'],
            'order_sn'=>$order_sn,
            'data'=>$this->getOrderDetail($ret['order_id'], false)
        ];
        return successMsg('订单单条明细数据', $data);

    }

    public function getExpress($order_sn)
    {
       $ret = $this->order->getOrderExpress($this->store['id'], $order_sn);
        if($ret['status']==0){
            return $ret;
        }
        $order_html = '';
        $express_html = '';
        foreach($ret['data'] as $v){
            if( !$order_html ){
                $order_html = "
                    <dl>
                        <dt>订单号：{$v['order_sn']}</dt>
                        <dt>下单时间：{$v['create_time']}</dt>
                        <dt>支付状态：{$v['status']}</dt>
                        <dt>订单状态：{$v['status_unpack']}</dt>
                        <dt>支付金额：{$v['pay_money']}</dt>
                    </dl>
                    <p><strong>订单物流信息：</strong></p>
                ";
            }
            if($v['express_name']){
                $express_html .= "
                    <dl>
                        <dt>物流公司：{$v['express_name']}</dt>
                        <dt>物流单号：{$v['express_code']}</dt>
                    </dl>
                ";
            }

        }
        $order_html .= $express_html;
        return successMsg('查询成功', ['data'=>$order_html]);
    }

}
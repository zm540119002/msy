/**
 * Created by Administrator on 2017/10/30.
 */
//调用微信JS api 支付
function jsApiCall(data){
    WeixinJSBridge.invoke('getBrandWCPayRequest',data,function(res){
        if(res.err_msg == 'get_brand_wcpay_request:ok'){
            location.href = MODULE + '/recharge/payComplete';
        }else if(res.err_msg == 'get_brand_wcpay_request:cancel'){
            dialog.error('用户取消支付!');
        }else{
            dialog.error('支付失败!');
        }
    });
}
function callPay(data){
    if (typeof WeixinJSBridge == 'undefined'){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall(data);
    }
}
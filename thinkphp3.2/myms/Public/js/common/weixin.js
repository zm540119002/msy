/**
 * Created by Administrator on 2018/1/12.
 */
//授权登录跳转
$(window).load(function() {
    var url = window.location.href;
    var ua = window.navigator.userAgent.toLowerCase();
    var wxUsered = $('.wxUsered').val();
    alert(wxUsered)
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){//判断微信浏览器
        if(!wxUsered){

            self.location=MODULE + '/WeiXin/checkWxUser/?url='+url;
        }
    }
});

/**
 * Created by Administrator on 2018/1/12.
 */
//授权登录跳转
$(window).load(function() {
    var url = window.location.href;
    var ua = window.navigator.userAgent.toLowerCase();
    var wxUsered = $('.wxUsered').val();
    console.log(wxUsered);
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){//判断微信浏览器
        console.log(1);
        if(wxUsered == 0){
            console.log(2);
            self.location=MODULE + '/WeiXin/checkWxUser/?url='+url;
        }
    }
});

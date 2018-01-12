/**
 * Created by Administrator on 2018/1/12.
 */
// setTimeout(function(){
//     $(window).load(function() {
//         alert(1);return;
//         var url = window.location.href;
//         var ua = window.navigator.userAgent.toLowerCase();
//         if(ua.match(/MicroMessenger/i) == 'micromessenger'){//判断微信浏览器
//             if(url.indexOf("code=") <= 0 ) { //code
//                 self.location=MODULE + 'checkWxUser';
//             }
//         }
//     });
// },1000);
$(window).load(function() {
    var url = window.location.href;
    console.log(url);return;
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){//判断微信浏览器
        if(url.indexOf("code=") <= 0 ) { //code
            self.location=MODULE + '/WeiXin/checkWxUser/?url='+url;
        }
    }
});

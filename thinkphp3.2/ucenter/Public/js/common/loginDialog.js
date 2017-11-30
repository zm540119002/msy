var timer;
var requestSign = true;
//获取验证码
$('body').on('click','.mesg_code',function(){
    if($(this).attr('disabled')){
        return false;
    }
    var _form = $(this).parents('form');
    var postData = {};
    postData.mobile_phone = _form.find('[name=mobile_phone]').val();
    postData.captcha_type = _form.find('[name=captcha_type]').val();
    var userName=_form.find('.user_name').val();
    var userPhone=_form.find('.user_phone').val();
    if(!requestSign){
        return false;
    }
    console.log(postData);
    //requestSign = false;
    var time=60;
    if(!checkAccount(userName)){
        errorTipc('请输入正确用户名');
        return false;
    }else if(!register.phoneCheck(userPhone)){
        errorTipc('请输入正确手机号码');
        return false;
    }
    
    $('.tel_code').val('');
    clearInterval(timer);
    timer=setInterval(CountDown,1000);
    function CountDown(){
        _form.find('.mesg_code').attr('disabled',true);
        _form.find('.mesg_code').text('重新发送'+time+'s');
        if(time==0){
            _form.find('.mesg_code').text("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
        }
        time--;
    }
    return false;
    // var url = 'http://' + UCENTER_DOMAIN + '/index.php/Home/User/send_sms';
    // $.post(url,postData,function(msg){
    //     requestSign = true;
    //     console.log(msg);return;
    //     if(msg.status == 0){
    //         $('.phone').val('').removeAttr("disabled");
    //         _form.find('.mesg_code').val("获取验证码").removeAttr("disabled");
    //         _form.find('.tel_code').val('');
    //         clearInterval(timer);
    //         layer.open({
    //             content:msg.info,
    //             time:3
    //         });
    //         return false;
    //     }else if(msg.status == 1){
    //         layer.open({
    //             content:"验证码已发送至手机:"+phone+' ，请查看。',
    //             time:3
    //         });
    //         return false;
    //     }
    // });
});
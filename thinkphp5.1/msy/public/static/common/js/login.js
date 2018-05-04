var timer;
var requestSign = true;
//获取验证码
$('body').on('click','.send_sms',function(){
    if($(this).attr('disabled')){
        return false;
    }
    var _form = $(this).parents('form');
    var postData = {};
    postData.mobile_phone = _form.find('[name=mobile_phone]').val();
    var userName=_form.find('.user_name').val();
    var userPhone=_form.find('.user_phone').val();
    if(!requestSign){
        return false;
    }
    var time=60;
    var content='';
    if(!register.phoneCheck(userPhone)){
        content='请输入正确手机号码';
    }
    if(content){
        errorTipc(content);
        return false;
    }
    $('.tel_code').val('');
    clearInterval(timer);
    timer=setInterval(CountDown,1000);
    function CountDown(){
        _form.find('.send_sms').attr('disabled',true);
        _form.find('.send_sms').text('重新发送'+time+'s');
        if(time==0){
            _form.find('.send_sms').text("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
        }
        time--;
    }
    var url = send_sms;
    $.post(url,postData,function(msg){
        requestSign = true;
        if(msg.status == 0){
            $('.phone').val('').removeAttr("disabled");
            _form.find('.send_sms').val("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
            errorTipc(msg.info,3000);
            return false;
        }else if(msg.status == 1){
            errorTipc("验证码已发送至手机:"+ postData.mobile_phone +' ，请查看。',3000);
            return false;
        }
    });
});
$(function(){
    //弹窗忘记密码
    $('body').on('click','.forget_dialog',function(){
        // forgetPasswordDialog();
    });
    tab_down('.loginNav li','.loginTab .login_wrap','click');
    $('body').on('click','.loginNav li',function(){
        var _this=$(this);
        if(_this.index()==0){
            $('.login_item').find('.send_sms').show();
            $('.use-item').show();
            $('.forget_password').hide();
            $('.entry-button').text('注册').removeClass('loginBtn').addClass('registerBtn');
            $('.login_item .password').attr('placeholder','设置密码');
        }else{
            $('.login_item').find('.send_sms').hide();
            $('.use-item').hide();
             $('.forget_password').show();
            $('.entry-button').text('登录').removeClass('registerBtn').addClass('loginBtn');
            $('.login_item .password').attr('placeholder','密码');
            $('.login_wrap').removeClass('active');
        }
    });
    //登录 or 注册
    $('body').on('click','.loginBtn,.registerBtn',function(){
        var $layer = $('.loginTab').find('.active');
        var _index = $('.loginTab').find('.login_wrap.active').index();
        var content='';
        switch(_index){
            //注册
            case 1:
                var verifiCode=$layer.find('.tel_code').val();
                var userPhone=$('.loginTab').find('.user_phone').val();
                var password=$('.loginTab').find('.password').val();
                if(!register.phoneCheck(userPhone)){
                    content='请输入正确手机号码';
                }else if(!register.vfyCheck(verifiCode)){
                    content = "请输入正确的验证码";
                }else if(!register.pswCheck(password)){
                    content = "请输入密码";
                }
                break;
            //登录
            default:
                var userPhone=$('.loginTab').find('.user_phone').val();
                var password=$('.loginTab').find('.password').val();
                if(!register.phoneCheck(userPhone)){
                    content='请输入正确手机号';
                }else if(!register.pswCheck(password)){
                    content = "请输入6-16数字或字母的密码";
                }
                break;
        }
        if(content){
            dialog.error(content);
            return false;
        }
        if(_index==1){//注册
            var url = controller + 'register';
        }else{//登录
            var url = action;
        }
        var postData = $('#formLogin').serializeObject();
        $.post(url,postData,function (data) {
            // console.log(data);return;
            if(data.status==0){
                dialog.error(data.info);
                return false;
            }else if(data.status==1){
                location.href = data.info;
            }
        });
    });
    //弹窗重置密码
    $('body').on('click','.forgetPasswdLayer .forgetPasswordBtn',function(){
        var $layer=$('.forgetPasswdLayer').find('.forgetPasswd_wrap');
        //验证
        var password=$layer.find('.password').val();
        var newPassword=$layer.find('.cofirm_password').val();
        var userPhone=$layer.find('.user_phone').val();
        var verifiCode=$layer.find('.tel_code').val();
        var content='';
        if(!register.pswCheck(password)){
            content = "请输入正确的密码"; 
        }else if(password!=newPassword){
            content = "两次密码输入不一致";
        }else if(!register.phoneCheck(userPhone)){
            content = "请输入正确的手机号码";
        }else if(!register.vfyCheck(verifiCode)){
            content = "请输入正确的验证码";
        }
        if(content){ 
            errorTipc(content);
            return false;
        }
        var url = controller + 'forgetPassword';
        var postData = $('.forgetPasswdLayer').find('#formReset').serializeObject();
        $.ajax({
            url:url,
            type:'post',
            data:postData,
            error:function(xhr){},
            success:function(data){
                if(data.status==0){
                    errorTipc(data.info);
                    return false;
                }else if(data.status==1){
                    layer.close($layer);
                }
            }
        });
    });
});

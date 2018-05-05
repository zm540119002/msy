$(function(){
    //切换
    tab_down('.loginNav li','.loginTab ','click');
    $('body').on('click','.loginNav li',function(){
        var _this=$(this);
        if(_this.index()==0){
            $('.entry-button').text('注册').removeClass('loginBtn').addClass('registerBtn');
            $('.login_item .password').attr('placeholder','设置密码');
        }else{
            $('.entry-button').text('登录').removeClass('registerBtn').addClass('loginBtn');
            $('.login_item .password').attr('placeholder','密码');
            $('.login_wrap').removeClass('active');
        }
    });

    //登录 or 注册 or 重置密码
    $('body').on('click','.loginBtn,.registerBtn,.forgetPasswordLayer .layui-m-layerbtn span',function(){
        var _this = $(this);
        var method = _this.data('method');
        console.log(method);
        return ;
        var userPhone=$('.loginTab.active').find('.user_phone').val();
        var password=$('.loginTab.active').find('.password').val();
        var verifiCode=$('.loginTab.active').find('.tel_code').val();
        var _index = $('.loginNav li.current').index();
        var content='';
        if(!register.phoneCheck(userPhone)){
            content='请输入正确手机号码';
        }else if(_index==0&&!register.vfyCheck(verifiCode)){
            content = "请输入正确的验证码";
        }
        else if(!register.pswCheck(password)){
            content = "请输入6-16数字或字母的密码";
        }
        if(content){
            dialog.error(content);
            return false;
        }
        if(_index==0){//注册
            var url = controller + 'register';
            submitForm($('#formRegister').serializeObject(),url);
        }else{//登录
            var url = action;
            submitForm($('#formLogin').serializeObject(),url);
        }
    });

    //显示隐藏密码
    var onOff = true;
    $('body').on('click','.view-password',function(){
        var _this=$(this);
        _this.toggleClass('active');
        if(onOff){
            $('.login_item .password').attr('type','text');
            onOff=false;
        }else{
            $('.login_item .password').attr('type','password');
            onOff=true;
        }
    });

    //获取验证码
    var timer;
    var requestSign = true;
    $('body').on('click','.send_sms',function(){
        if($(this).attr('disabled')){
            return false;
        }
        var _form = $(this).parents('form');
        var postData = {};
        postData.mobile_phone = _form.find('[name=mobile_phone]').val();
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

    //使用须知
    var attentionForm=$('#attentionForm').html();
    $('body').on('click','.use-attention',function(){
        var pageii = layer.open({
            title:['《美尚平台使用须知》','border-bottom:1px solid #d9d9d9;'],
            className:'addCcountLayer',
            type: 1,
            content: attentionForm,
            anim: 'up',
            style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;',
            success:function(){
            },
            btn:['确定']
        });
    });
});
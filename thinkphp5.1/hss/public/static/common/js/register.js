$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    var name,
        applicant,
        mobile,
        agentAuthorization,
        postData={};
    //填写基本资料
    $('body').on('click','.one-step',function(){
        // name=$('.name').val();
        // applicant=$('.applicant').val();
        postData=$('.applicant_form').serializeObject();
        var content='';
        if(!postData.name){
            content='请填写店家名称';
        }else if(!postData.applicant){
            content='请填写申请人姓名';
        }else if(!register.phoneCheck(postData.mobile)){
            content='请填写手机号码';
        }
        if(content){
            dialog.error(content);
        }else{
            $('.weui-flex-item:eq(0)').removeClass('current');
            $('.weui-flex-item:eq(1)').addClass('current');
            $('.apply-module:eq(0)').hide();
            $('.apply-module:eq(1)').show();
        }
    });
    //验证是否上传图片 与 提交申请
    $('body').on('click','.two-step',function(){
        name=trim($('.name').val(),'g');
        applicant=trim($('.applicant').val(),'g');
        businessLicense=$('.business-license').val();
        agentAuthorization=$('.agent-authorization').val();
        id = $('.id').val();
        var content='';
        if(!name){
            content='请填写厂商全称';
        }else if(!applicant){
            content='请填写代办人姓名';
        }else if(!businessLicense){
            content='请上传企业营业执照照片';
        }
        if(!uploadsSingleImgFlag ){
            dialog.error('图片还没有上传完毕');
            return false;
        }
        postData={
            id : id,
            name:name,
            agent:applicant,
            business_license:businessLicense,
            auth_letter:agentAuthorization
        };
        if(content){
            dialog.error(content);
            return false;
        }
        var _this = $(this);
        _this.addClass("nodisabled");
        $.post(controller + 'register',postData,function(msg){
            _this.removeClass("nodisabled");
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                $('.weui-flex-item:eq(1)').removeClass('current');
                $('.weui-flex-item:eq(2)').addClass('current');
                $('.apply-module:eq(1)').hide();
                $('.apply-module:eq(2)').show();
                $('.weui-flex-item:eq(0)').addClass('disabled');
                $('.weui-flex-item:eq(1)').addClass('disabled');
                dialog.success(msg.info,module+'Store/index');
            }
        });
    });
    //确定通过入驻
    $('body').on('click','.three-step',function(){
    })
});
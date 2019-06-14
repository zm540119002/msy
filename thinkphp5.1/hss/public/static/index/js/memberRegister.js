var area_address,
    applicantData={};

$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    //填写基本资料
    $('body').on('click','.next-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        applicantData.applicant=data.applicant;
        applicantData.mobile=data.mobile;
        var content='';
        if(!applicantData.applicant){
            content='请填写你的姓名';
        }
        if(content){
            dialog.error(content);
            return false;
        }else{
            $('.memberRegTpl .weui-flex-item:eq(0)').removeClass('current');
            $('.memberRegTpl .weui-flex-item:eq(1)').addClass('current');
            $('.memberRegTpl .apply-module:eq(0)').hide();
            $('.memberRegTpl .apply-module:eq(1)').show();
        }
    });
    $('body').on('click','.two-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        applicantData.consignee=data.consignee;
        applicantData.mobile=data.mobile;
        applicantData.detail_address=data.detail_address;
        area_address =$('.area-address-name').getArea();
        applicantData.province = area_address[0];
        applicantData.city = area_address[1];
        applicantData.area = area_address[2];
        var content='';
        if(!applicantData.consignee){
            content='请填写收件人姓名';
        }else if(!register.phoneCheck(applicantData.mobile)){
            content='请填写手机号码';
        }else if(!area_address){
            content='请选择地区';
        }else if(!applicantData.detail_address){
            content='请填写详细地址';
        }
        if(content){
            dialog.error(content);
        }else{
            $('.memberRegTpl .weui-flex-item:eq(1)').removeClass('current');
            $('.memberRegTpl .weui-flex-item:eq(2)').addClass('current');
            $('.memberRegTpl .apply-module:eq(1)').hide();
            $('.memberRegTpl .apply-module:eq(2)').show();
            console.log(applicantData);
            submitMemberInfo(_this,applicantData);
        }
    });
});
// 提交申请
function submitMemberInfo(_this,postData){
    //var url = module + '';
    _this.addClass("nodisabled");//防止重复提交
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            _this.removeClass("nodisabled");//删除防止重复提交
            $('.loading').hide();
            if(data.status){
                location.href = data.data.url;
            }else{
                dialog.success(data.info);
            }
        }
    });
}

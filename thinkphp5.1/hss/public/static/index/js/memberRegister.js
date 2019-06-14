var area_address,
    applicantData={};

$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');

    //初始化 未完成的申请
    if(apply!=null){
        applicantData = {
            id:apply.id,
            name:apply.name,
            applicant:apply.applicant,
            mobile:apply.mobile,
            province:apply.province,
            city:apply.city,
            area:apply.area,
            detail_address:apply.detail_address,
            old_apply_status:apply.apply_status,
            pay_id:apply.pay_id,
        };
        //省市区初始化
        var region = [];
        region.push(apply.province);
        region.push(apply.city);
        region.push(apply.area);
        $('.area_address').setArea(region);
        $('.detail_address').val(apply.detail_address);
        //资料初始化
        $('.name').val(apply.name);
        $('.applicant').val(apply.applicant);
        $('.mobile').val(apply.mobile);
        //定位到已完成步骤
        var index=apply.apply_status-1;
        $('nav.apply-data-nav li:eq('+index+')').click(function(){
            $(this).addClass('current').siblings().removeClass('current');
        });
        $('nav.apply-data-nav li:eq('+index+')').click();
    }
    //填写基本资料
    $('body').on('click','.next-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        // applicantData.name=data.name;
        applicantData.applicant=data.applicant;
        applicantData.mobile=data.mobile;
        // applicantData.detail_address=data.detail_address;
        // area_address =$('.area-address-name').getArea();
        // applicantData.province = area_address[0];
        // applicantData.city = area_address[1];
        // applicantData.area = area_address[2];
        var content='';
        // if(!applicantData.name){
        //     content='请填写店家名称';
        // }else if(!applicantData.applicant){
        //     content='请填写申请人姓名';
        // }else if(!register.phoneCheck(applicantData.mobile)){
        //     content='请填写手机号码';
        // }else if(!area_address){
        //     content='请选择地区';
        // }else if(!applicantData.detail_address){
        //     content='请填写详细地址';
        // }
        // if(content){
        //     dialog.error(content);
        // }else{
        //     applicantData.step = 1;
        //     console.log(applicantData);
        //     submitApplicant(_this,applicantData);
        // }
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
            console.log(applicantData);
            //submitApplicant(_this,applicantData);
        }
    });
    $('body').on('click','.two-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        // applicantData.name=data.name;
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
            $('.memberRegTpl .weui-flex-item:eq(0)').removeClass('current');
            $('.memberRegTpl .weui-flex-item:eq(1)').addClass('current');
            $('.memberRegTpl .apply-module:eq(0)').hide();
            $('.memberRegTpl .apply-module:eq(1)').show();
            console.log(applicantData);
            //submitApplicant(_this,applicantData);
        }
    });
});
// 提交申请
function submitApplicant(_this,postData){
    var url = module + 'Franchise/franchiseSettlement';
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
                applicantData.id = data.data.id;
                if(postData.step==1){
                    $('.weui-flex-item:eq(0)').removeClass('current');
                    $('.weui-flex-item:eq(1)').addClass('current');
                    $('.apply-module:eq(0)').hide();
                    $('.apply-module:eq(1)').show();
                }else{
                    location.href = data.data.url;
                }
            }else{
                dialog.success(data.info);
            }
        }
    });
}

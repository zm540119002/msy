var area_address,
    applicantData={};

$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    //初始化 未完成的申请
    if(apply!=null){
        applicantData = {
            id:apply[0].id,
            name:apply[0].name,
            applicant:apply[0].applicant,
            mobile:apply[0].mobile,
            province:apply[0].province,
            city:apply[0].city,
            area:apply[0].area,
            detail_address:apply[0].detail_address,
            old_apply_status:apply[0].apply_status,
            pay_id:apply[0].pay_id,
        };
        //省市区初始化
        var region = [];
        region.push(apply[0].province);
        region.push(apply[0].city);
        region.push(apply[0].area);
        $('.area_address').setArea(region);
        $('.detail_address').val(apply[0].detail_address);
        //资料初始化
        $('.name').val(apply[0].name);
        $('.applicant').val(apply[0].applicant);
        $('.mobile').val(apply[0].mobile);

        //定位到已完成步骤
        var index=apply.apply_status-1;
        $('nav.apply-data-nav li:eq('+index+')').click(function(){
            $(this).addClass('current').siblings().removeClass('current');
        });
        $('nav.apply-data-nav li:eq('+index+')').click();
    }
    //填写基本资料
    $('body').on('click','.one-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        applicantData.name=data.name;
        applicantData.applicant=data.applicant;
        applicantData.mobile=data.mobile;
        applicantData.detail_address=data.detail_address;
        area_address =$('.area-address-name').getArea();
        applicantData.province = area_address[0];
        applicantData.city = area_address[1];
        applicantData.area = area_address[2];
        var content='';
        if(!applicantData.name){
            content='请填写店家名称';
        }else if(!applicantData.applicant){
            content='请填写申请人姓名';
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
            applicantData.step = 1;
            submitApplicant(_this,applicantData);
        }
    });
    
    //确定通过入驻
    $('body').on('click','.three-step',function(){
    });
    // 弹出支付方式
    $('body').on('click','.recharge_money',function(){
        var settlementMethod=$('.settlementMethod').html();
        layer.open({
            type: 1
            ,anim: 'up'
            ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 50%; padding:10px 0; border:none;',
            className:'settlementMethod bankTransferLayer',
            content: settlementMethod
        });
    });
    // 选择充值结算方式
    $('body').on('click','.settlementMethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });

    // 结算
    $('body').on('click','.settlement_btn',function () {
        applicantData.province = area_address[0];
        applicantData.city = area_address[1];
        applicantData.area = area_address[2];
        applicantData.pay_code = $('.pay_code').val();
        applicantData.step = 2;
        _this = $(this);
        if(!applicantData.pay_code){
            dialog.error('请选择结算方式');
        }else{
            submitApplicant(_this,applicantData);
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

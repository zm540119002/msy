var area_address,
    applicantData={
    };
$(function(){
    //补款倒计时
    var date=new Date(new Date(new Date().toLocaleDateString()).getTime()+24*60*60*1000-1);
		countDown(date,$('#countDownBox'));
    //nav切换
    $('body').on('click','.apply-data-nav .switch-item',function(){
        $(this).addClass('current').siblings().removeClass('current');
        $('.apply-module').hide().eq($(this).index()).show();
    });

     //初始化 未完成的申请
    if(!$.isEmptyArray(apply)){
        var statusType=apply[0].apply_status-1;
        applicantData.id= apply[0].id;
        applicantData = {
            id:apply[0].id,
            province:apply[0].province,
            city:apply[0].city,
            company_name:apply[0].company_name,
            applicant:apply[0].applicant,
            mobile:apply[0].mobile,
            old_apply_status:apply[0].apply_status,
            pay_id:apply[0].pay_id,
        };
        var region = [];
        region.push(apply[0].province);
        region.push(apply[0].city);
        if(statusType<3){
            //省市区初始化
            $('.area_address').setArea(region);
            //资料初始化
            $('.company_name').val(apply[0].company_name);
            $('.applicant').val(apply[0].applicant);
            $('.mobile').val(apply[0].mobile);
            //定位到已完成步骤
            var index=apply[0].apply_status-1;
            $('nav.apply-data-nav li:eq('+index+')').click(function(){
                $(this).addClass('current').siblings().removeClass('current');
            });
            $('nav.apply-data-nav li:eq('+index+')').click();
            $('.weui-flex li:eq(3)').addClass('nodisabled');

        }else{
            //待审核
            //资格完款
            if(statusType==4 || statusType==3){
                $('nav.apply-data-nav li:eq(3)').click(function(){
                    $(this).addClass('current').siblings().removeClass('current');
                });
                $('nav.apply-data-nav li:eq(3)').click();
                $('.weui-flex li:eq(0),.weui-flex li:eq(1),.weui-flex li:eq(2)').addClass('nodisabled');
                if(statusType == 4){
                    //资格完款
                    var start_pay_time = apply[0]['payment_time']*1000;
                    var date=new Date(start_pay_time+(24*60*60*1000-1));
                    countDown(date,$('#countDownBox'));
                    $('.apply_city').setArea(region);
                }
            }
        }
    }


    //填写地址
    $('body').on('click','.search-city',function(){
        area_address =$('.area-address-name').getArea();
        if(!area_address){
            dialog.error('请选择城市');
            return false;
        }
        var cityArr=[];
		for (var key=0;key<applied.length;key++) {
			cityArr.push([parseInt(applied[key].province),parseInt(applied[key].city)]);
		}
        var cityData=[];
        cityData.push(parseInt(area_address[0]),parseInt(area_address[1]));
        applicantData.province = area_address[0];
        applicantData.city = area_address[1];
        applicantData.step = 1;
        var provinces=arrayHasElement(cityArr,cityData);
        if(!provinces){
            layer.open({
                content:'所查询的城市可以申请城市合伙人<br/>声明：同一城市可能存在多位申请人,同等条件下按先申请先审核签约原则。',
                btn:['确定'],
                className:'confirm',
                yes:function(index){
                    _this = $(".confirm span[type='1']");
                    submitApplicant(_this,applicantData);
                    layer.close(index);
                }
            });
        }else{
            layer.open({
                content:'所查询的城市暂时没有空缺<br/>备注：城市合伙人可能已被签约，或者正处于保留状态，建议过段时间再查询。',
                btn:['确定'],
                yes:function(index){
                    layer.close(index);
                }
            });

        }
    });
     //填写基本资料
    $('body').on('click','.one-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        applicantData.company_name=data.company_name;
        applicantData.applicant=data.applicant;
        applicantData.mobile=data.mobile;
        applicantData.step = 2;
        var content='';
        if(!applicantData.company_name){
            content='请填写企业名称';
        }else if(!applicantData.applicant){
            content='请填写申请人姓名';
        }else if(!register.phoneCheck(applicantData.mobile)){
            content='请填写手机号码';
        }
        if(content){
            dialog.error(content);
            return false;
        }
        submitApplicant(_this,applicantData);
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

    // 资格结算
    $('body').on('click','.settlement_btn',function () {
        applicantData.step =  $('.apply-data-nav .switch-item.current').index()+1;
        applicantData.pay_code = $('.pay_code').val();
        _this = $(this);
        if(!applicantData.pay_code){
            dialog.error('请选择结算方式');
        }else{
            submitApplicant(_this,applicantData);
        }
    });
});

var arrayHasElement = function(array, element) {  
    // 判断二维数组array中是否存在一维数组element
    for (var el of array) {
        if (el.length === element.length) {
        for (var index in el) {
            if (el[index] !== element[index]) {
            break;
        }
        // 判断二维数组array中是否存在一维数组element
            if (index == (el.length - 1)) {   
                return true;
            }
        }
        }
    }
    return false;
}
// 提交申请
function submitApplicant(_this,postData){
    var url = module + 'CityPartner/submitApplicant';
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
                }else if(postData.step==2){
                    $('.weui-flex-item:eq(0),.weui-flex-item:eq(1)').removeClass('current');
                    $('.weui-flex-item:eq(2)').addClass('current');
                    $('.apply-module:eq(1)').hide();
                    $('.apply-module:eq(2)').show();
                }else if(postData.step==3 ||postData.step==4 ){
                    location.href = data.data.url;
                }
            }else{
                dialog.success(data.info);
            }
        }
    });
}

var area_address,
    applicantData={
    };
$(function(){
    //nav切换
    $('body').on('click','.apply-data-nav .switch-item',function(){
        $(this).addClass('current').siblings().removeClass('current');
        $('.apply-module').hide().eq($(this).index()).show();
    });

    if(info){
        // 步骤

        var step = $('.weui-flex-item');
        var step_list = $('.apply-module');

        switch(parseInt(info.apply_status)){
            case 2:
                step.removeClass('current');
                step_list.hide();
                $('.weui-flex-item:eq(2)').addClass('current');
                $('.apply-module:eq(2)').show();

                $('.weui-flex li:eq(3)').addClass('nodisabled');
                break;
            case 3:
            case 4:
            case 5:
                // 补款倒计时
                var update_time = info.update_time;
                var date = new Date(update_time.replace(/-/g, '/'));

                date = new Date(date.getTime()+(24*60*60*1000-1));
                countDown(date,$('#countDownBox'));

                step.removeClass('current');
                step_list.hide();
                $('.weui-flex-item:eq(3)').addClass('current');
                $('.apply-module:eq(3)').show();

                // 禁止修改记录
                $(".step").remove();
                $(".apply-module:eq(2) .group_cart_nav").remove();
                $(".express-area").removeClass('express-area');
                $('.apply-items input,.applicant_form .select-value').addClass('nodisabled');

                break;
        }

    }else{
        // 城市回显
        var cityPartner = localStorage.getItem("cityPartner");
        if(cityPartner){
            cityPartner = JSON.parse(cityPartner);
            $('input[name="province"]').val(cityPartner.province);
            $('input[name="city"]').val(cityPartner.city);
            $('.select-value').val(cityPartner.area_address);
        }else{
            $('.select-value').val('城市列表');
        }

        $('.weui-flex li:eq(2),.weui-flex li:eq(3)').addClass('nodisabled');
    }





     //初始化 未完成的申请
/*    if(!$.isEmptyArray(apply)){
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
    }*/


    //填写地址
    // 地区查询
    $('body').on('click','.search-city',function(){
        //area_address =$('.area-address-name').val();

/*        var province    = $('input[name="province"]').val();
        var city        = $('input[name="city"]').val();
        var area_address= $('.select-value').val();*/
/*        if(!province || !city){
            dialog.error('请先选择城市');
            return false;
        }*/

        var data=$('.applicant_form').serializeObject();
        step1(data);
        data.step = 1;

        layer.open({
            content:'所查询的城市可以申请城市合伙人<br/>声明：同一城市可能存在多位申请人,同等条件下按先申请先审核签约原则。',
            btn:['确定'],
            className:'confirm',
            yes:function(index){
                var _this = $(this);
                submitApplicant(_this,data);
                layer.close(index);

/*                _this = $(".confirm span[type='1']");

                _this.addClass("nodisabled");//防止重复提交
                $.ajax({
                    url: module + 'CityPartner/submitApplicant',
                    data: data,
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
                            // 开放下一步

                            $('.weui-flex-item:eq(0)').removeClass('current');
                            $('.weui-flex-item:eq(1)').addClass('current');
                            $('.apply-module:eq(0)').hide();
                            $('.apply-module:eq(1)').show();

                            layer.close(index);
                        }else{
                            if(data.data.status){
                                layer.open({
                                    content: '所查询的城市暂时没有空缺<br/>备注：城市合伙人可能已被签约，或者正处于保留状态，建议过段时间再查询。',
                                    time: 2 //2秒后自动关闭
                                });
                            }else{
                                dialog.error(data.info);
                            }
                        }
                    }
                });*/
            }
        });

    });
     //填写基本资料
    $('body').on('click','.one-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        data.step = 2;
        step1(data);
        step2(data);

        submitApplicant(_this,data);

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
        var data=$('.applicant_form').serializeObject();
        data.step =  $('.apply-data-nav .switch-item.current').index()+1;
        data.pay_code = $('.pay_code').val();
        _this = $(this);
        if(!data.pay_code){
            dialog.error('请选择结算方式');
        }else{

            /*        applicantData.company_name=data.company_name;
             applicantData.applicant=data.applicant;
             applicantData.mobile=data.mobile;*/
            //data.step = 3;

            step1(data);

            step2(data);

            submitApplicant(_this,data);
        }
    });
});

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
                //applicantData.id = data.data.id;
                if(postData.step==1){
                    $('.weui-flex-item:eq(0)').removeClass('current');
                    $('.weui-flex-item:eq(1)').addClass('current');
                    $('.apply-module:eq(0)').hide();
                    $('.apply-module:eq(1)').show();

                     $('.city_name').html(data.data.city_name);
                     $('.city_level').html(data.data.level);
                     $('.market_name').html(data.data.market_name);
                     $('.amount').find('price').html(data.data.amount);
                     $('.earnest').find('price').html(data.data.earnest);



                }else if(postData.step==2){
/*                    $('.weui-flex-item:eq(0),.weui-flex-item:eq(1)').removeClass('current');
                    $('.weui-flex-item:eq(2)').addClass('current');
                    $('.apply-module:eq(1)').hide();
                    $('.apply-module:eq(2)').show();*/

/*                    $('.city_name').html(data.data.city_name);
                    $('.market_name').html(data.data.market_name+'城市合伙人');
                    $('.amount').find('price').html(data.data.amount);
                    $('.earnest').find('price').html(data.data.earnest);*/

                    //location.href = data.data.url;
                    localStorage.removeItem("cityPartner");
                    location.reload();


                }else if(postData.step==3 ||postData.step==4 ){
                    location.href = data.data.url;
                }

            }else{
                if(data.data.status){
                    layer.open({
                        content: '所查询的城市暂时没有空缺<br/>备注：城市合伙人可能已被签约，或者正处于保留状态，建议过段时间再查询。',
                        time: 2 //2秒后自动关闭
                    });
                }else{
                    dialog.error(data.info);
                }
            }
        }
    });
}


function step1(data) {
    var content;
    if(!data.province || !data.city){
        content='请先选择城市';
    }
    if(content){
        dialog.error(content);
        return false;
    }
}

function step2(data) {
    var content;
    if(!data.company_name){
        content='请填写企业名称';
    }else if(!data.applicant){
        content='请填写申请人姓名';
    }else if(!register.phoneCheck(data.mobile)){
        content='请填写手机号码';
    }
    if(content){
        dialog.error(content);
        return false;
    }
}

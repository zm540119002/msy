$(function(){
    var epyInfo=$('#employeeInfo').html();
    var epyPst=$('#epyPosition').html();
    var branchOff=$('#branchOffice').html();
    var epyIStore=$('#epyStore').html();

    //增加员工账户
    var layerCount = -1;
    var layerCountEmploee = 0;
    $('body').on('click','.add_a',function(){
        $('.del_btn,.mod_btn').css('display','none');
        $('.branch_n_inpt').removeClass('current');
        layerCountEmploee = layer.open({
            type:1,
            btn:['确定','取消'],
            title:['增加员工账户','border-bottom:1px solid #d9d9d9;'],
            className:'employee_ad_layer',
            content:epyInfo,
            success:function(){
                layerCount++;
            },
            yes: function(index){
                var layerForm=$('.employee_ad_layer').find('#form1');
                var addEpyInfo={
                    epyName:layerForm.find('.epl_name').val(),
                    epyPhone:layerForm.find('.epl_phone').val(),
                    epyPassword:layerForm.find('.epl_password').val(),
                    epyBranch:layerForm.find('.epy_i_branch').text(),
                    epyBranchId:layerForm.find('.epy_i_branch_id').val(),
                    epyStore:layerForm.find('.epy_i_store').text(),
                    epyStoreId:layerForm.find('.epy_i_store_id').val(),
                    epyPost:layerForm.find('.dis_post').text(),
                    epyPostId:layerForm.find('.dis_post_id').val()
                };
                var content = '';
                if(scaleType == 4){
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(!checkValidPasswd(addEpyInfo.epyPassword)){
                        content = '请输入6-16数字或字母密码';
                    }else if(addEpyInfo.epyBranch=='未设置'){
                        content = '请设置员工所属分公司';
                    }else if(addEpyInfo.epyStore=='未设置'){
                        content = '请设置员工所属门店';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }else if(scaleType == 3){
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(!checkValidPasswd(addEpyInfo.epyPassword)){
                        content = '请输入6-16数字或字母密码';
                    }else if(addEpyInfo.epyStore=='未设置'){
                        content = '请设置员工所属门店';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }else{
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(!checkValidPasswd(addEpyInfo.epyPassword)){
                        content = '请输入6-16数字或字母密码';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }

                if(content){
                    layer.open({
                        content: content,
                        skin:'msg',
                        success:function(e){
                            layerCount++;
                        },
                        time: 2
                    });
                    return false;
                }

                $.post('manageAccount',layerForm.serialize(),function(msg){
                    if(msg.status == 0){
                        layer.open({
                            content: msg.info,
                            skin:'msg',
                            time: 2
                        });
                        return false;
                    }else if(msg.status == 1){
                        var epyInfoList=$('#employeeInfoList');
                        epyInfoList.find('.layer_epy_name').text(addEpyInfo.epyName);
                        epyInfoList.find('.layer_epy_name').attr('employeeId',msg.id);
                        epyInfoList.find('.layer_epy_phone').text(addEpyInfo.epyPhone);
                        epyInfoList.find('.layer_epy_psw').text(addEpyInfo.epyPassword);
                        epyInfoList.find('.layer_epy_branch').text(addEpyInfo.epyBranch);
                        epyInfoList.find('.layer_epy_branch_id').val(addEpyInfo.epyBranchId);
                        epyInfoList.find('.layer_epy_store').text(addEpyInfo.epyStore);
                        epyInfoList.find('.layer_epy_store_id').val(addEpyInfo.epyStoreId);
                        epyInfoList.find('.layer_epy_pst').text(addEpyInfo.epyPost);
                        epyInfoList.find('.layer_epy_pst_id').val(addEpyInfo.epyPostId);
                        var employee_ad_list = $('.employee_ad_list');
                        employee_ad_list.find('li.emptyEmployee').remove();
                        employee_ad_list.prepend(epyInfoList.html());
                        layer.close(index);
                    }
                });
            }
        });
    });

    //设置员工所属分公司
    $('body').on('click','.branch_n_inpt',function(){
        $(this).addClass('current');
        $(this).parent().siblings().find('.branch_n_inpt').removeClass('current');
    });
    $('body').on('click','.set_branch',function(){
        layer.open({
            btn:['确定','取消'],
            title:['设置门店所属分公司','border-bottom:1px solid #d9d9d9;'],
            className:'branch_store_layer',
            content:branchOff,
            success:function(){
                layerCount++;
                var tBranchText=$('#form1').find('.epy_i_branch').text();
                $.each($('.branch_store_layer .branch_n_inpt'), function(i,val){
                    if(tBranchText==$(this).text()){
                        $(this).addClass('current');
                        $(this).parent().siblings().find('.branch_n_inpt').removeClass('current');
                        return false;
                    }
                });
            },
            yes:function(index){
                var currentCompany = $('.branch_n_inpt.current');
                var currentCompanyName = currentCompany.text();
                $(".epy_i_branch").text(currentCompanyName);
                var currentCompanyId = currentCompany.next().val();
                $(".epy_i_branch_id").val(currentCompanyId);
                layer.close(index);
            }
        });
    });

    //设置员工所属门店
    $('body').on('click','.epy_store_layer li',function(){
        $(this).addClass('current');
        $(this).siblings().removeClass('current');
    });
    $('body').on('click','.set_store',function(){
        layer.open({
            btn:['确定','取消'],
            title:['设置员工所属门店','border-bottom:1px solid #d9d9d9;'],
            className:'epy_store_layer',
            content:epyIStore,
            success:function(){
                layerCount++;
                //公司-门店联动
                var companyId = $('div[index='+layerCountEmploee+']').find('.epy_i_branch_id').val();
                var shopLis = $('div[index='+ layerCount +']').find('li');
                $.each(shopLis,function(i,o){
                    if($(this).attr('companyId') == companyId){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                });

                var epyStoreText=$('#form1').find('.epy_i_store').text();
                $.each($('.epy_store_layer li'), function(i,val){
                    if(epyStoreText==$(this).find('.epy_store_n').text()){
                        $(this).addClass('current');
                        $(this).siblings().removeClass('current');
                        return false;
                    }
                });
            },
            yes:function(index){
                var current = $('.current');
                $(".epy_i_store").text(current.find('.epy_store_n').text());
                $(".epy_i_store_id").val(current.find('[name=shopId]').val());
                layer.close(index);
                return false;
            },
            end:function(){
                return false;
            }
        });
    });

    //设置员工岗位职务
    $('body').on('click','.pst_option',function(){
        $(this).addClass('current').siblings().removeClass('current');
        $(this).parent().siblings('li').find('.pst_option').removeClass('current');
    });
    $('body').on('click','.set_position',function(){
        layer.open({
            btn:['确定','取消'],
            title:['设置员工岗位职务','border-bottom:1px solid #d9d9d9;'],
            className:'employee_pst_layer',
            content:epyPst,
            success:function(){
                layerCount++;
                var epyPostText=$('#form1').find('.dis_post').text();
                $.each($('.employee_pst_layer .pst_option'),function(i,val){
                    if(epyPostText==$(this).text()){
                        $(this).addClass('current').siblings().removeClass('current');
                        $(this).parent().siblings('li').find('.pst_option').removeClass('current');
                        return false;
                    }
                });
            },
            yes:function(index){
                var current = $('.current');
                $(".dis_post").text(current.text());
                $(".dis_post_id").val(current.attr('position-id'));
                layer.close(index);
            }
        });
    });

    //删除员工账户
    $('.del_a').on('click',function(){
        $('.del_btn').css('display','block');
        $('.mod_btn').css('display','none');
    });
    $('body').on('click','.del_btn',function(){
        var _this=$(this);
        var employeeId = _this.parent().find('.layer_epy_name').attr('employeeid');
        layer.open({
            content:'确定删除选定的员工账户？',
            btn:['确定','取消'],
            success:function(e){
                layerCount++;
            },
            yes:function(index){
                $.post('delEmployee',{employeeId:employeeId},function(msg){
                    if(msg.status == 0){
                        layer.open({
                            content: msg.info,
                            skin:'msg',
                            time: 2
                        });
                        return false;
                    }else if(msg.status == 1){
                        _this.parent().remove();
                        layer.close(index);
                    }
                });
            }
        });
    });

    //修改员工账户
    $('.up_a').on('click',function(){
        $('.mod_btn').css('display','block');
        $('.del_btn').css('display','none');
    });
    $('body').on('click','.mod_btn',function(){
        var _this=$(this);
        layerCountEmploee = layer.open({
            type:1,
            title:'修改员工账户',
            className:'employee_ad_layer',
            btn:['确定','取消'],
            content:epyInfo,
            success:function(index){
                var layerForm=$('.employee_ad_layer').find('#form1');
                //去掉初设密码
                layerForm.find('.initialPasswd').remove();
                layerCount++;
            },
            yes:function(index){
                var layerForm=$('div[index='+index+']').find('#form1');

                var addEpyInfo={
                    epyName:layerForm.find('.epl_name').val(),
                    epyPhone:layerForm.find('.epl_phone').val(),
                    epyPassword:layerForm.find('.epl_password').val(),
                    epyBranch:layerForm.find('.epy_i_branch').text(),
                    epyBranchId:layerForm.find('.epy_i_branch_id').val(),
                    epyStore:layerForm.find('.epy_i_store').text(),
                    epyStoreId:layerForm.find('.epy_i_store_id').val(),
                    epyPost:layerForm.find('.dis_post').text(),
                    epyPostId:layerForm.find('.dis_post_id').val()
                };
                var content = '';
                if(scaleType == 4){
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(addEpyInfo.epyBranch=='未设置'){
                        content = '请设置员工所属分公司';
                    }else if(addEpyInfo.epyStore=='未设置'){
                        content = '请设置员工所属门店';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }else if(scaleType == 3){
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(addEpyInfo.epyStore=='未设置'){
                        content = '请设置员工所属门店';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }else{
                    if(!addEpyInfo.epyName){
                        content = '请填写员工姓名！';
                    }else if(!isMobilePhone(addEpyInfo.epyPhone)){
                        content = '请填写正确的手机号码';
                    }else if(addEpyInfo.epyPost=='未设置'){
                        content = '请设置员工岗位职务';
                    }
                }

                if(content){
                    layer.open({
                        content: content,
                        skin:'msg',
                        success:function(e){
                            layerCount++;
                        },
                        time: 2
                    });
                    return false;
                }

                $.post('manageAccount',layerForm.serialize(),function(msg){
                    if(msg.status == 0){
                        layer.open({
                            content: msg.info,
                            skin:'msg',
                            time: 2
                        });
                        return false;
                    }else if(msg.status == 1){
                        _this.parent().find('.layer_epy_name').text(layerForm.find('.epl_name').val());
                        _this.parent().find('.layer_epy_name').attr('employeeid',layerForm.find('[name=employeeId]').val());
                        _this.parent().find('.layer_epy_phone').text(layerForm.find('.epl_phone').val());
                        _this.parent().find('.layer_epy_psw').text(layerForm.find('.epl_password').val());
                        _this.parent().find('.layer_epy_branch').text(layerForm.find('.epy_i_branch').text());
                        _this.parent().find('.layer_epy_branch_id').val(layerForm.find('.epy_i_branch_id').val());
                        _this.parent().find('.layer_epy_store').text(layerForm.find('.epy_i_store').text());
                        _this.parent().find('.layer_epy_store_id').val(layerForm.find('.epy_i_store_id').val());
                        _this.parent().find('.layer_epy_pst').text(layerForm.find('.dis_post').text());
                        _this.parent().find('.layer_epy_pst_id').val(layerForm.find('.dis_post_id').val());
                        layer.close(index);
                    }
                });
            }
        });
        //列表值赋值给弹层对应的值
        $('.epl_name').val(_this.parent().find('.layer_epy_name').text());
        $('[name=employeeId]').val(_this.parent().find('.layer_epy_name').attr('employeeid'));
        $('.epl_phone').val(_this.parent().find('.layer_epy_phone').text());
        $('.epl_password').val(_this.parent().find('.layer_epy_psw').text());
        $('.epy_i_branch').text(_this.parent().find('.layer_epy_branch').text());
        $('.epy_i_branch_id').val(_this.parent().find('.layer_epy_branch_id').val());
        $('.epy_i_store').text(_this.parent().find('.layer_epy_store').text());
        $('.epy_i_store_id').val(_this.parent().find('.layer_epy_store_id').val());
        $('.dis_post').text(_this.parent().find('.layer_epy_pst').text());
        $('.dis_post_id').val(_this.parent().find('.layer_epy_pst_id').val());
    });
});



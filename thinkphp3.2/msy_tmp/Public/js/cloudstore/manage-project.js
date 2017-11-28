//定义全局变量
$(function(){
    var manageProject = $('#manageProject').html();
    var serviceProjectContent = $('#serviceProjectContent').html();
    var serviceProjectDescription = $('#serviceProjectDescription').html();
    var serviceProjectPicture = $('#serviceProjectgPicture').html();
    var employeeStore = $('#employeeStore').html();

    //增加云店拓客项目
    $('body').on('click','.add_a',function(){
        $('.branch_n_inpt').removeClass('current');
        $('.pg_status').css('display','none');
        layer.open({
            type:1,
            btn:['确定','取消'],
            title:['增加云店拓客项目','border-bottom:1px solid #d9d9d9;'],
            className:'manage_pg_layer',
            content:manageProject,
            success:function(){
            },
            yes: function(index){
                var layerForm = $('.manage_pg_layer').find('#form1');

                var addProjectInfo = {
                    projectName:layerForm.find('.project_name').val()
                };
                var content = '';
                if(!addProjectInfo.projectName){
                    content = '请填写项目名称！';
                }
                if(content){
                    layer.open({
                        content: content,
                        skin:'msg',
                        time: 2
                    });
                    return false;
                }

                var postData = layerForm.serializeObject();
                //服务门店
                var shopIdArr = layerForm.find('.set_store').data('shopIdArr');
                postData.shopIdArr = shopIdArr;
                //项目列表图
                var list_view_img = layerForm.find('.upload_project_picture').data('list_view_img');
                if(list_view_img){
                    postData.list_view_img = list_view_img;
                }
                //项目首焦图
                var home_focus_img = layerForm.find('.upload_project_picture').data('home_focus_img');
                if(home_focus_img){
                    postData.home_focus_img = home_focus_img;
                }

                $.post('manageProject',postData,function(msg){
                    if(msg.status ==0){
                        layer.open({
                            content:msg.info,
                            skin:'msg',
                            time:2
                        });
                    }else{
                        $('.cs_pg_list').find('.empty').remove();
                        $('.cs_pg_list').prepend(msg);
                        layer.close(index);
                    }
                });
            }
        });
    });

    //修改拓客项目
    $('.mod_pg_a').on('click',function(){
        $('.pg_status').css('display','flex');
        $('.on_off_line').css('display','none');
        $('.mod_pg').css('display','block');
    });
    $('body').on('click','.mod_pg',function(){
        var _this=$(this);
        var _thisLi = _this.parent().parent().parent().parent();
        var img = _thisLi.find('img');
        layer.open({
            type:1,
            title:'修改云店拓客项目',
            className:'manage_pg_layer',
            btn:['确定','取消'],
            content:manageProject,
            success:function(){
                var layerForm = $('.manage_pg_layer').find('#form1');
                //列表值赋
                copyDataByName(_thisLi,layerForm);
                //绑定数据
                var option = _thisLi.find('[name=shopIdArr] option');
                var shopIdArr = [];
                $.each(option,function(){
                    shopIdArr.push($(this).val());
                });
                $('.set_store').data('shopIdArr',shopIdArr);
                $('.upload_project_picture').data('home_focus_img',_thisLi.find('[name=home_focus_img]').val());
                $('.upload_project_picture').data('list_view_img',_thisLi.find('[name=list_view_img]').val());
            },
            yes:function(index){
                var layerForm = $('.manage_pg_layer').find('#form1');

                var addProjectInfo={
                    projectName:layerForm.find('.project_name').val()
                };
                var content = '';
                if(!addProjectInfo.projectName){
                    content = '请填写项目名称！';
                }
                if(content){
                    layer.open({
                        content: content,
                        skin:'msg',
                        time: 2
                    });
                    return false;
                }

                var postData = layerForm.serializeObject();
                //服务门店
                var shopIdArr = layerForm.find('.set_store').data('shopIdArr');
                postData.shopIdArr = shopIdArr;
                //项目列表图
                var list_view_img = layerForm.find('.upload_project_picture').data('list_view_img');
                if(list_view_img){
                    postData.list_view_img = list_view_img;
                }
                //项目首焦图
                var home_focus_img = layerForm.find('.upload_project_picture').data('home_focus_img');
                if(home_focus_img){
                    postData.home_focus_img = home_focus_img;
                }

                $.post('manageProject',postData,function(msg){
                    if(msg.status ==0){
                        layer.open({
                            content:msg.info,
                            skin:'msg',
                            time:2
                        });
                    }else{
                        var projectUl = $('.cs_pg_list');
                        var project_id = $(msg).find('[name=project_id]').val();
                        $.each(projectUl.find('li'),function(){
                            if($(this).data('id') == project_id ){
                                $(this).replaceWith(msg);
                            }
                        });
                        layer.close(index);
                    }
                });
            }
        });
    });

    //上传美容服务项目图片
    $('body').on('click','.upload_project_picture',function(){
        var _this = $(this);
        layer.open({
            type:1,
            btn:['确定','取消'],
            title:['上传美容服务项目图片','border-bottom:1px solid #d9d9d9;'],
            className:'serivce_pg_layer',
            content:serviceProjectPicture,
            success:function(){
                var list_view_img = $('.serivce_pg_layer').find('[name=list_view_img]').next();
                _this.data('list_view_img') && list_view_img.attr('src',UPLOAD + _this.data('list_view_img'));
                var home_focus_img = $('.serivce_pg_layer').find('[name=home_focus_img]');
                _this.data('home_focus_img') && home_focus_img.next().attr('src',UPLOAD + _this.data('home_focus_img'));
            }
        });
    });

    // 上传图片
    $('body').on('change','[name=list_view_img],[name=home_focus_img]',function(e){
        var name = $(this).attr('name');
        var img = event.target.files[0];
        var obj=$(this).parent();

        // 判断是否图片
        if(!img){
            return false;
        }

        // 判断图片格式
        var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
        if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
            layer.open({
                content:'请上传：jpg、jpeg、png、gif格式图片',
                time:2
            }) ;
        }
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            $(obj).addClass('active');
            $(obj).find('img').attr('src',e.target.result);

            var postData = {
                img: e.target.result
            };
            if(name == 'list_view_img'){
                postData.imgWidth = 950;
                postData.imgHeight = 500;
            }else{
                postData.imgWidth = 1000;
                postData.imgHeight = 1000;
            }
            $.post('uploadImgToTemp',postData,function(msg){
                if(msg.status == 0){
                    layer.open({
                        content:msg.info,
                        time:2
                    });
                    return false;
                }else if(msg.status == 1){
                    $('.upload_project_picture').data(name,msg.info);
                    $(obj).find('img').attr('src',UPLOAD + msg.info);
                }
            },'json');
        }
    });

    //选择提供服务的门店
    $('body').on('click','.epy_store_layer li',function(){
        $(this).toggleClass('current');
    });
    $('body').on('click','.set_store',function(){
        var shopIdArr = $('.set_store').data('shopIdArr');
        layer.open({
            btn:['确定','取消'],
            title:['选择提供服务的门店','border-bottom:1px solid #d9d9d9;'],
            className:'epy_store_layer',
            content:employeeStore,
            success:function(){
                var length=objectLength(shopIdArr);
                $.each($('.epy_store_layer li'), function(){
                    for(var i=0;i<length;i++){
                        if( shopIdArr[i] == $(this).data('id') ){
                            $(this).addClass('current');
                        }
                    }
                });
            },
            yes:function(index){
                var arr = [];
                $.each($('.epy_store_layer li'), function(){
                    if($(this).hasClass('current')){
                        arr.push($(this).data('id'));
                        $('.set_store').data('shopIdArr',arr);
                    }
                });
                layer.close(index);
            }
        });
    });

    //编辑美容服务项目内容
    $('body').on('click','.edit_project_content',function(){
        var _thisLi = $(this).parent();
        layer.open({
            btn:['确定','取消'],
            title:['编辑美容服务项目内容'],
            className:'edit_pgt_layer',
            content:serviceProjectContent,
            success:function(){
                //列表值赋
                var layerContent = $('.edit_pgt_layer').find('.service_content');
                copyDataByName(_thisLi,layerContent);
            },
            yes:function(index){
                //列表值赋
                var layerContent = $('.edit_pgt_layer').find('.service_content');
                copyDataByName(layerContent,_thisLi);
                layer.close(index);
            }
        });
    });

    //编辑美容服务项目说明
    $('body').on('click','.edit_project_explain',function(){
        var _thisLi = $(this).parent();
        layer.open({
            btn:['确定','取消'],
            title:['编辑美容服务项目说明'],
            className:'edit_pgt_layer',
            content:serviceProjectDescription,
            success:function(){
                //列表值赋
                var layerContent = $('.edit_pgt_layer').find('.service_description');
                copyDataByName(_thisLi,layerContent);
            },
            yes:function(index){
                //列表值赋
                var layerContent = $('.edit_pgt_layer').find('.service_description');
                copyDataByName(layerContent,_thisLi);
                layer.close(index);
            }
        });
    });

    //上下线
    $('.onOff_btn').on('click',function(){
        $('.pg_status').css('display','flex');
        $('.on_off_line').css('display','block');
        $('.mod_pg').css('display','none');
    });
    $('body').on('click','.on_line,.off_line',function(){
        var _this = $(this);
        var _thisLi = _this.parent().parent().parent().parent().parent();
        var project_id = _thisLi.find('[name=project_id]').val();
        var online = _this.attr('data-v');
        $.post('onOffLine',{project_id:project_id,online:online},function(msg){
            layer.open({
                content:msg.info,
                skin:'msg',
                time:2
            });
            if(msg.status == 1){
                _thisLi.find('.current_online').text(_this.text());
            }
        });
    });

    //字数限定
    $('.project_name').on('keyup',function(){
        maximumWord(this,18);
    });
    $('.project_briefly_text').on('keyup',function(){
        maximumWord(this,28);
    });

    //加载项目
    getProjects();
});

//加载项目
function getProjects(){
    var url = 'getProjects';
    var postData = {};
    $.ajax({
        type: 'get',
        url: url,
        dataType: 'html',
        data: postData,
        beforeSend:function(xhr){
        },
        error:function(xhr){
        },
        success:function(msg){
            if(msg.status ==0){
                layer.open({
                    content:msg.info,
                    skin:'msg',
                    time:2
                });
            }else{
                $('.cs_pg_list').html(msg);
            }
        }
    });
}
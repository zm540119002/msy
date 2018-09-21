$(function(){
    var addTagForm=$('#addTagForm').html();
    var layerTagNum;
    var layerTagName;
    var tagListLen=$('.classify-label-content .tag-item').length;
        if(!tagListLen){
            $('.classify-label-content .set-tag-tipc').hide();
            $('.classify-label-content div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
            $('.classify-label-content div').eq(0).find('a:eq(3)').addClass('down-disabled-icons');
        }else{
            if(tagListLen>=1){
                $('.classify-label-content div').siblings().find('a:eq(2)').removeClass('move-disabled-icons').addClass('move-icons');
                $('.classify-label-content div').siblings().find('a:eq(3)').addClass('down-icons');
                $('.classify-label-content div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
                $('.classify-label-content div').eq(0).find('a:eq(3)').addClass('down-icons');

                $('.classify-label-content div').eq(tagListLen-1).find('a:eq(2)').removeClass('move-disabled-icons').addClass('move-icons');
                $('.classify-label-content div').eq(tagListLen-1).find('a:eq(3)').addClass('down-disabled-icons');
            }
        }
    //新增分类标签
    $('body').on('click','.add-type-tag',function(){
        layer.open({
            title:['新建商品分类标签','border-bottom:1px solid #d9d9d9;'],
            content:addTagForm,
            className:'addTagLayer',
            btn:['确定','取消'],
            yes:function(index){
                layerTagNum=$('.addTagLayer .layer-tag-num').val();
                layerTagName=$('.addTagLayer .layer-tag-name').val();

                $('input[class="classifyTagInfo'+layerTagNum+'"]').data('tag-id',layerTagNum);
                var postData = {};
                postData.name = layerTagName;
                $.post(controller+"edit",postData,function(msg){
                    if(msg.status == 0){
                        dialog.error(msg.info);
                    }
                    if(msg.status == 1){
                        var html='';
                        html+='<div class="columns_flex tag-item first-tag">';
                        html+='<span class="classify-tag-name">'+layerTagName+'</span>';
                        html+='<span class="classify-operate-btn">';
                        html+=' <a href="javascript:void(0);" class="edit-icons">编辑</a>';
                        html+=' <a href="javascript:void(0);" class="del-icons">删除</a>';
                        html+=' <a href="javascript:void(0);" class="move-btn" data-move="0">上移</a>';
                        html+=' <a href="javascript:void(0);" class="down-btn" data-move="1">下移</a>';
                        html+='</span>';
                        html+='<input type="hidden" value="" class="sort'+layerTagNum+'" data-tag-id=""/>';
                        html+='<input type="hidden" value="" class="series_id" data-series-id="">';
                        html+='</div>';
                        var tagListLen=$('.classify-label-content .tag-item').length;
                        if(!tagListLen){
                            $('.classify-label-content .set-tag-tipc').hide();
                            $('.classify-label-content').append(html);
                            $('.classify-label-content div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
                            $('.classify-label-content div').eq(0).find('a:eq(3)').addClass('down-disabled-icons');
                        }else{
                            $('.classify-label-content .tag-item:first').before(html);
                            if(tagListLen>=1){
                                $('.classify-label-content div').siblings().find('a:eq(2)').removeClass('move-disabled-icons').addClass('move-icons');
                                $('.classify-label-content div').siblings().find('a:eq(3)').addClass('down-icons');
                                $('.classify-label-content div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
                                $('.classify-label-content div').eq(0).find('a:eq(3)').addClass('down-icons');

                                $('.classify-label-content div').eq(tagListLen).find('a:eq(2)').removeClass('move-disabled-icons').addClass('move-icons');
                                $('.classify-label-content div').eq(tagListLen).find('a:eq(3)').addClass('down-disabled-icons');
                            }
                        }
                    }
                });
                layer.close(index);
            }
        })
    });
    //编辑分类标签
    $('body').on('click','.edit-icons',function(){
        var _this=$(this);
        // var classifyTagInfo=_this.parents('.classify-operate-btn').next('.classifyTagInfo'+layerTagNum).data('tag');
        var tagName=_this.parents('.classify-operate-btn').prev('.classify-tag-name');
        manageClassifyTag.initLayer(addTagForm,tagName,'修改商品分类标签',_this);
    });
    //删除分类标签
    $('body').on('click','.del-icons',function(){
        var _this=$(this);
        var tagName=_this.parents('.classify-operate-btn').prev('.classify-tag-name');
        manageClassifyTag.initLayer('你确定删除分类标签?',tagName,'删除商品分类标签',_this);
    })
    //上移
    $('body').on('click','.move-btn',function(){
        var _this=$(this);
        if(_this.hasClass('move-disabled-icons')){
            //_this.attr('disabled',true);
        }else{
            manageClassifyTag.upTag(_this);
        }
        
    })
    //下移
    $('body').on('click','.down-btn',function(){
        var _this=$(this);
        if(_this.hasClass('down-disabled-icons')){
            _this.attr('disabled',true);
        }else{
            manageClassifyTag.downTag(_this);
        }
       
    }) 
})
var manageClassifyTag={
    //初始化弹窗
    initLayer:function(content,name,title,obj){
        layer.open({
            title:[title,'border-bottom:1px solid #d9d9d9;'],
            content:content,
            className:'addTagLayer',
            btn:['确定','取消'],
            success:function(){
                // $('.addTagLayer .layer-tag-num').val(name.text());
                var abc=obj.parents('.classify-operate-btn').next().data('tag-id');
                $('.addTagLayer .layer-tag-name').val(name.text());
                $('.addTagLayer .layer-tag-num').val(abc);
                if(obj.hasClass('del-icons')){
                    $('.addTagLayer .layer-tag-name').attr('disabled',true);
                }
            },
            yes:function(index){
                if(obj.hasClass('edit-icons')){
                     manageClassifyTag.editTag(name);
                }else{
                    manageClassifyTag.deleteTag(name,obj);
                }
                
                layer.close(index);
            }
        });
    },
    addTag:function(){},
    editTag:function(editObj){
        layerTagName=$('.addTagLayer .layer-tag-name').val();
        layerTagNum=$('.addTagLayer .layer-tag-num').val();
        var seriesId = editObj.siblings('.series_id').data('series-id');
        editObj.text(layerTagName);
        var postData = {};
        postData.series_id = seriesId;
        postData.name = layerTagName;
        $.post(controller+"edit",postData,function(msg){
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                dialog.success(msg.info);
            }
        });
    },
    deleteTag:function(delObj){
        var tagListLen=$('.classify-label-content .tag-item').length;
        if(tagListLen>=1){
            delObj.parents('.tag-item').siblings().find('a:eq(2)').addClass('move-icons');
            delObj.parents('.tag-item').siblings().find('a:eq(3)').removeClass('down-disabled-icons').addClass('down-icons');
            delObj.parents('.tag-item').siblings('div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
            delObj.parents('.tag-item').siblings('div').eq(0).find('a:eq(3)').addClass('down-icons');

            delObj.parents('.tag-item').siblings('div').eq(tagListLen-2).find('a:eq(2)').removeClass('move-disabled-icons').addClass('move-icons');
            delObj.parents('.tag-item').siblings('div').eq(tagListLen-2).find('a:eq(3)').addClass('down-disabled-icons');
            if(tagListLen==2){
                delObj.parents('.tag-item').siblings('div').eq(0).find('a:eq(2)').addClass('move-disabled-icons');
                delObj.parents('.tag-item').siblings('div').eq(0).find('a:eq(3)').addClass('down-disabled-icons');
            }
        }
        if(tagListLen==1){
            $('.classify-label-content .set-tag-tipc').show();
        }
        var series_id = delObj.siblings('.series_id').data('series-id');
       var _this = $(this);
        _this.addClass("nodisabled");
        $.post(controller+"delete",{series_id:series_id},function(msg){
            _this.removeClass("nodisabled");
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                dialog.success(msg.info);
            }
        });
        delObj.parents('.tag-item').remove();
    },
    upTag:function(upObj){
        var swith=upObj.parents('.tag-item').siblings('.isClick').val();
        var postData = {};
        postData.move = upObj.data('move');
        postData.series_id = upObj.parent().siblings('.series_id').data('series-id');
        postData.sort = upObj.parent().siblings('.sort').data('tag-id');
        console.log(postData);
        
        if(swith==1){
            console.log(swith);
            //upObj.addClass('disabled');
            // console.log(swith);
            upObj.parents('.tag-item').siblings('.isClick').val(0);
            $.ajax({
                url: controller+"move",
                data: postData,
                type: 'post',
                beforeSend: function(){
                    //swith=true;
                    //$('.loading').show();
                },
                success: function(info){
                    if(info.status == 1){
                        var currentIndex=upObj.parents('.tag-item').index();
                        var upperIndex=upObj.parents('.tag-item').prev().index();
                        var currentTagName=upObj.parents('.tag-item').find('.classify-tag-name');
                        var upperTagName=upObj.parents('.tag-item').prev().find('.classify-tag-name');
                        var currentTagId=upObj.parents('.tag-item').find('.sort');
                        var upperTagId=upObj.parents('.tag-item').prev().find('.sort');
                        var currentSeriesTagId=upObj.parents('.tag-item').find('.series_id');
                        var upperSeriesTagId=upObj.parents('.tag-item').prev().find('.series_id');
                        var temp,tempId,tempSeriesId;
                            temp=upperTagName.text();
                            tempId=upperTagId.data('tag-id');
                            tempSeriesId=upperSeriesTagId.data('series-id');
                        if(currentIndex>upperIndex){
                            upperTagName.text(currentTagName.text());
                            currentTagName.text(temp);
                            
                            currentTagId.data('tag-id',upperTagId.data('tag-id'));
                            upperTagId.data('tag-id',currentTagId.data('tag-id'));
                            
                            currentSeriesTagId.data('series-id',upperSeriesTagId.data('series-id'));  
                            upperSeriesTagId.data('series-id',currentSeriesTagId.data('series-id'));
                            //upObj.addClass('disabled');
                            
                        }
                        //window.location=module+'Series/edit'
                        
                    }
                    
                },
                complete:function(){
                    setTimeout(function() {  
                        upObj.parents('.tag-item').siblings('.isClick').val(1);
                    }, 1500); 
                        
                },
                error:function (xhr) {
                    dialog.error('AJAX错误'+xhr);
                },
            });
        }else{
            return false;
        }
        // $.post(controller+"move",postData,function(msg){
        //     if(msg.status == 1){
        //         if(currentIndex>upperIndex){
        //             upperTagName.text(currentTagName.text());
        //             currentTagName.text(temp);
        //             upperTagId.data('tag-id',currentTagId.data('tag-id'));
        //             currentTagId.data('tag-id',tempId);
        //             upperSeriesTagId.data('series-id',currentSeriesTagId.data('series-id'));
        //             currentSeriesTagId.data('series-id',tempSeriesId);
        //         }
        //     }
        // });
        
    },
    downTag:function(downObj){
        var currentIndex=downObj.parents('.tag-item').index();
        var nextIndex=downObj.parents('.tag-item').next().index();
        var currentTagName=downObj.parents('.tag-item').find('.classify-tag-name');
        var nextTagName=downObj.parents('.tag-item').next().find('.classify-tag-name');
        var currentTagId=downObj.parents('.tag-item').find('.sort');
        var nextTagId=downObj.parents('.tag-item').next().find('.sort');
        var currentSeriesTagId=downObj.parents('.tag-item').find('.series_id');
        var nextSeriesTagId=downObj.parents('.tag-item').next().find('.series_id');
        var temp,tempId,tempSeriesId;
            temp=nextTagName.text();
            tempId=nextTagId.data('tag-id');
            tempSeriesId=nextSeriesTagId.data('series-id');
        var postData = {};
        postData.move = downObj.data('move');
        postData.series_id = downObj.parent().siblings('.series_id').data('series-id');
        postData.sort = downObj.parent().siblings('.sort').data('tag-id');
        $.post(controller+"move",postData,function(msg){
            if(msg.status == 1){
                if(currentIndex<nextIndex){
                    nextTagName.text(currentTagName.text());
                    currentTagName.text(temp);
                    nextTagId.data('tag-id',currentTagId.data('tag-id'));
                    currentTagId.data('tag-id',tempId);
                    nextSeriesTagId.data('series-id',currentSeriesTagId.data('series-id'));
                    currentSeriesTagId.data('series-id',tempSeriesId);
                }
            }

        });
    }
}
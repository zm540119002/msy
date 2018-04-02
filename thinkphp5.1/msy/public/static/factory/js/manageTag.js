$(function(){
    var addTagForm=$('#addTagForm').html();
    var layerTagNum;
    var layerTagName;
    
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
                var html='';
                    html+='<div class="columns_flex tag-item first-tag">';
                    html+='<span class="classify-tag-name">'+layerTagName+'</span>';
                    html+='<span class="classify-operate-btn">';
                    html+=' <a href="javascript:void(0);" class="edit-icons">编辑</a>';
                    html+=' <a href="javascript:void(0);" class="del-icons">删除</a>';
                    html+=' <a href="javascript:void(0);" class="move-btn">上移</a>';
                    html+=' <a href="javascript:void(0);" class="down-btn">下移</a>';
                    html+='</span>';
                    html+='<input type="hidden" value="" class="classifyTagInfo'+layerTagNum+'" data-tag-id=""/>'
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
                // tagData={
                //     layerTagNum:layerTagNum,
                //     layerTagName:layerTagName
                // }
                $('input[class="classifyTagInfo'+layerTagNum+'"]').data('tag-id',layerTagNum);
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
        manageClassifyTag.initLayer(addTagForm,tagName,'删除商品分类标签',_this);
    })
    //上移
    $('body').on('click','.move-btn',function(){
        var _this=$(this);
        if(_this.hasClass('move-disabled-icons')){
            _this.attr('disabled',true);
        }else{
            manageClassifyTag.moveTag(_this);
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
        editObj.text(layerTagName);
        editObj.siblings('input').data('tag-id',layerTagNum);
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
        delObj.parents('.tag-item').remove();
    },
    moveTag:function(moveObj){
        var currentIndex=moveObj.parents('.tag-item').index();
        var upperIndex=moveObj.parents('.tag-item').prev().index();
        var currentTagName=moveObj.parents('.tag-item').find('.classify-tag-name');
        var upperTagName=moveObj.parents('.tag-item').prev().find('.classify-tag-name');
        var currentTagId=moveObj.parents('.tag-item').find('input');
        var upperTagId=moveObj.parents('.tag-item').prev().find('input');

        var temp,tempId;
            temp=upperTagName.text();
            tempId=upperTagId.data('tag-id');
            if(currentIndex>upperIndex){
                upperTagName.text(currentTagName.text());
                currentTagName.text(temp);
                upperTagId.data('tag-id',currentTagId.data('tag-id')).attr('class','classifyTagInfo'+currentTagId.data('tag-id'));
                currentTagId.data('tag-id',tempId).attr('class','classifyTagInfo'+tempId);
            }
    },
    downTag:function(downObj){
        var currentIndex=downObj.parents('.tag-item').index();
        var nextIndex=downObj.parents('.tag-item').next().index();
        var currentTagName=downObj.parents('.tag-item').find('.classify-tag-name');
        var nextTagName=downObj.parents('.tag-item').next().find('.classify-tag-name');
        var currentTagId=downObj.parents('.tag-item').find('input');
        var nextTagId=downObj.parents('.tag-item').next().find('input');
        var temp,tempId;
            temp=nextTagName.text();
            tempId=nextTagId.data('tag-id');
            if(currentIndex<nextIndex){
                nextTagName.text(currentTagName.text());
                currentTagName.text(temp);
                nextTagId.data('tag-id',currentTagId.data('tag-id')).attr('class','classifyTagInfo'+currentTagId.data('tag-id'));
                currentTagId.data('tag-id',tempId).attr('class','classifyTagInfo'+tempId);
            }
    }
}
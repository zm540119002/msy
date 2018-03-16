$(function(){
    //上传缩略图
    var uploadThumbnail=$('#uploadThumbnail').html();
    $('body').on('click','.upload-thumbnail',function(){
        layer.open({
            title:['上传缩略图','border-bottom:1px solid #d9d9d9;'],
            className:'uploadThumbnailLayer',
            content:uploadThumbnail,
            btn:['确定','取消'],
            success:function(){
                var thumbnailSrc=$('.thumbnail-picture').val();
                $('.uploadThumbnailLayer img').attr('src',thumbnailSrc);

            },
            yes:function(index){
                var layerThumbnailSrc=$('.uploadThumbnailLayer .img').val();
                $('.thumbnail-picture').val(layerThumbnailSrc);
                layer.close(index);
            }
        })
    });
    //选择商品分类标签
    var editGoodsLabel=$('#editGoodsLabel').html();
    $('body').on('click','.editGoodsLabel',function(){
        var factoryLayerName,factoryLayerId;
        layer.open({
            title:['商品分类标签','border-bottom:1px solid #d9d9d9'],
            className:'editGoodsLayer',
            content:editGoodsLabel,
            btn:['确定','取消'],
            success:function(){
                var factoryId=$('.goods-tag').data('id');
                $('.editGoodsLayer li').on('click',function(){
                    var _this=$(this);
                    _this.addClass('current').siblings().removeClass('current');
                });
                $.each($('.editGoodsLayer li'),function(){
                    var currentId=$(this).data('id');
                    if(factoryId==currentId){
                        $(this).addClass('current');
                    }
                })
            },
            yes:function(index){
                var isCurrent=$('.editGoodsLayer li.current');
                goodsTagLayerName=isCurrent.text();
                goodsTagLayerId  =isCurrent.data('id');
                $('.goods-tag').val(goodsTagLayerName);
                $('.goods-tag').data('id',goodsTagLayerId);
                layer.close(index);
            }
        })
    });
    //上传首焦图
    $('body').on('click','.uploadFocusPicture',function(){
        layer.open({
            title:['上传商品首焦图'],
            className:'uploadFocusLayer',
            content:uploadThumbnail,
            btn:['确定','取消'],
            success:function(){
                var focusPicture=$('.focus-picture').val();
                $('.uploadFocusLayer img').attr('src',focusPicture);

            },
            yes:function(index){
                var layerFocusSrc=$('.uploadFocusLayer .img').val();
                $('.focus-picture').val(layerFocusSrc);
                layer.close(index);
            }
        })
    })

    //编辑商品参数
    var editGoodsParameter=$('#editGoodsParameter').html();
    $('body').on('click','.editGoodsParameter',function(){
        layer.open({
            title:['编辑商品参数','border-bottom:1px solid #d9d9d9;'],
            className:'editParameterLayer',
            content:editGoodsParameter,
            btn:['确定','取消'],
            success:function(){
                var goodsParameter=$('.goods-parameter').val();
                $('.editParameterLayer .editGoodsText').val(goodsParameter);
            },
            yes:function(index){
                var editGoodsText=$('.editParameterLayer .editGoodsText').val();
                $('.goods-parameter').val(editGoodsText);
                layer.close(index);
            }
        })
    })
    
    //增加商品
    $('body').on('click','.identifyNewGoods',function(){
        var goodsDetail=$('.goods-detail').data('src');
        var postData={};
        var postData=$('.addProductContent').serializeObject();
        postData.goodsDetail=goodsDetail;
        console.log(postData);
        var content='';
        if(!postData.goodsName){
            content='请填写商品名称';
        }else if(!postData.goodsCharacter){
            content='请填写商品特点';
        }else if(!isNumber(postData.settlePrice)){
            content='请填写结算价';
        }else if(!isNumber(postData.retailPrice)){
            content='请填写零售价';
        }else if(!postData.thumbnailPicture){
            content='请上传缩略图';
        }else if(!postData.goodsTag){
            content='请选择标签';
        }else if(!postData.focusPicture){
            content='请上传首焦图';
        }else if(!postData.goodsParameter){
            content='请填写商品参数';
        }else if(!goodsDetail){
            content='请上传商品详情图';
        }
        if(content){
            dialog.error(content);
        }
        // $.post('',postData,function(){

        // })
    })

});


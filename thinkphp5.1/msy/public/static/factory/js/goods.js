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
                var layerThumbnailSrc=$('.uploadThumbnailLayer img').attr('src');
                $('.thumbnail-picture').val(layerThumbnailSrc);
                layer.close(index);
            }
        })
    });
    $('body').on('change','.uploadImg',function(){
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
            var imgUrl=e.target.result;
            $(obj).addClass('active');
            var postData = {img: e.target.result};
            postData.imgWidth = 145;
            postData.imgHeight = 100;
            $(obj).find('img').attr('src',imgUrl);
        }
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
                var layerFocusSrc=$('.uploadFocusLayer img').attr('src');
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
    //编辑商品详情
    var editDetail=$('#editDetail').html();
    $('body').on('click','.editDetail',function(){
        layer.open({
            // title:['商品分类标签','border-bottom:1px solid #d9d9d9'],
            className:'editDetailLayer',
            content:editDetail,
            btn:['确定','取消'],
            success:function(){
                var html='';
                html+='<li>';
                html+='<div class="picture-module active">';
                html+='<input type="file" class="uploadImg" name="">';
                html+='<span class="delete-picture">X</span>';
                html+='<img src="" class="upload_img">';
                html+='</div>'
                html+='</li>';
                var multiImgAttr=$('.goods-detail').data('src');
                console.log(multiImgAttr.length);
                for(var i=0;i<multiImgAttr.length;i++){
                    $('.editDetailLayer .multi-picture-module').append(html);
                    $('.editDetailLayer .upload_img').eq(i).attr('src',multiImgAttr[i]);
                }
            },
            yes:function(index){
                var layermultiImgAttr=[];
                $.each($('.editDetailLayer li'),function(i,val){
                    var _this=$(this);
                    var imgSrc=_this.find('img').attr('src');
                    layermultiImgAttr.push(imgSrc);
                })
                $('.goods-detail').data('src',layermultiImgAttr);
                layer.close(index);
            }
        })
    });

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


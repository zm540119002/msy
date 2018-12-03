

$(function(){
    //上传缩略图
    $('body').on('click','.upload-thumbnail',function(){
        var _this = $(this);
        uploadsImg(_this,'上传商品缩略图','uploadThumbnailLayer');
    });
    //上传单视频
    $('body').on('click','.uploadVideo',function(){
        var _this = $(this);
        uploadsVideo(_this,'上传商品小视频','uploadSingleVideoLayer');
    });
    //上传首焦图
    var editDetail=$('#editDetail').html();
    $('body').on('click','.uploadFocusPicture',function(){
        var _this = $(this);
        var storageDataObj=_this.siblings('input[type="hidden"]');
        var num=_this.siblings('input[type="hidden"]').data('picture-num');
        // uploadsImg(_this,'上传商品首焦图','uploadFocusLayer');
        uploadsMultiImg(editDetail,storageDataObj,num,'上传商品首焦图');
    });
    //商品分类
    $('body').on('click','.form_goods_type a',function () {
        $(this).addClass('current').siblings().removeClass('current');
    })

    //归属店铺分类(系列)
    var editGoodsLabel=$('#editGoodsLabel').html();
    $('body').on('click','.editGoodsLabel',function(){
        var factoryLayerName,factoryLayerId;
        layer.open({
            title:['归属店铺分类(系列)','border-bottom:1px solid #d9d9d9'],
            className:'editGoodsLayer',
            content:editGoodsLabel,
            btn:['确定','取消'],
            success:function(){
                var factoryId=$('.goods-tag').val();
                $('.editGoodsLayer li').on('click',function(){
                    var _this=$(this);
                    _this.addClass('current').siblings().removeClass('current');
                });
                $.each($('.editGoodsLayer li'),function(){
                    var currentId=$(this).data('id');
                    if(factoryId==currentId){
                        $(this).addClass('current');
                    }
                });
            },
            yes:function(index){
                var isCurrent=$('.editGoodsLayer li.current');
                goodsTagLayerName=isCurrent.text();
                goodsTagLayerId  =isCurrent.data('id');
                $('.goods-tag').val(goodsTagLayerId);
                $('.goods-tag').data('name',goodsTagLayerName);
                layer.close(index);
            }
        })
    });

    //设置品类
    var categoryContent=$('#categoryContent').html();
    $('body').on('click','.set-category',function(){
        layer.open({
            title:['设置商品的检索品类','border-bottom:1px solid #d9d9d9'],
            className:'categoryContentLayer',
            content:categoryContent,
            btn:['确定','取消'],
            success:function(){
                var categoryId=$('.select-category').data('category-id');
                var categoryIdArr=categoryId.split(',');
                var category_id_1;
                if(categoryIdArr[0]==0){
                    $('.categoryContentLayer').find('li:first').addClass('current');
                    category_id_1 =  $('.categoryContentLayer .category-tab').find('li:first a').data('id');
                }else{
                    category_id_1=categoryIdArr[0];
                }
                $.ajax({
                    url: module+'GoodsCategory/getSecondCategoryById',
                    data: {category_id_1:category_id_1},
                    type: 'get',
                    beforeSend: function(){
                        //$('.loading').show();
                    },
                    success: function(msg){
                        $('.category-content-wrapper').empty();
                        $('.category-content-wrapper').append(msg);
                        $.each($('.categoryContentLayer .category-tab li'),function(){
                            var _this=$(this);
                            var _thisId=_this.find('a.first_category').data('id');
                                if(_thisId==categoryIdArr[0]){
                                    _this.addClass('current').siblings().removeClass('current');
                                    return false;
                                }
                        });
                        $.each($('.categoryContentLayer .category-type li'),function(){
                            var _this=$(this);
                            var _thisId=_this.find('a.second_category').data('id');
                                if(_thisId==categoryIdArr[1]){
                                    _this.addClass('current').siblings().removeClass('current');
                                    return false;
                                }
                        })
                    },
                    complete:function(){
                        
                    },
                    error:function (xhr) {
                        dialog.error('AJAX错误');
                    },
                });

                

            },
            yes:function(index){
                var categoryArr='';
                $.each($('.category-tab li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        var first_category_id=$('.categoryContentLayer .category-tab>li.current').find('a').data('id');
                        categoryArr+=first_category_id+',';
                        return false;
                    }
                });
                $.each($('.category-type li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        second_category_id=$('.categoryContentLayer .category-type>li.current').find('a').data('id');
                        categoryArr+=second_category_id;
                        return false;
                    }
                });
                $('.select-category').data('category-id',categoryArr);
                layer.close(index);
            }
        })
    });
    //编辑商品参数
    var editGoodsParameter=$('#editGoodsParameter').html();
    $('body').on('click','.editGoodsParameter',function(){
        var winH=$(window).height();
        layer.open({
            title:['编辑商品参数','border-bottom:1px solid #d9d9d9;'],
            className:'editParameterLayer',
            type:1,
            content:editGoodsParameter,
            anim:'up',
            style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;',
            btn:['确定','取消'],
            success:function(){
                $('.editParameterLayer .editGoodsText').css('height',(winH-112)+'px');
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
    //获取二级分类
    $('body').on('click','.first_category',function () {
        var _this = $(this);
        var category_id_1 = _this.data('id');
        $.get(module+'GoodsCategory/getSecondCategoryById',{category_id_1:category_id_1},function(msg){
            $('.category-content-wrapper').empty();
           $('.category-content-wrapper').append(msg);
        });

    })
    
    //增加商品
    $('body').on('click','.identifyNewGoods',function(){
        var mainImg=$('.main_img').data('src');
        var goodsDetail=$('.goods-detail').data('src');
        var category=$('.select-category').data('category-id');
        var categoryArray = category.split(',');
        var type = $('.goods-type.current').data('type');
        var postData={};
        var postData=$('.addProductContent').serializeObject();
        postData.type = type;
        postData.main_img = mainImg;
        postData.details_img=goodsDetail;
        postData.category_id_1=categoryArray[0];
        postData.category_id_2=categoryArray[1];
        var content='';
        if(!postData.name){
            content='请填写商品名称';
        }else if(!postData.trait){
            content='请填写商品特点';
        }else if(!postData.type){
            content='请选择商品类型';
        }else if(!postData.sale_price){
            content='请填写优惠价';
        }else if(!isMoney(postData.sale_price)){
            content='价格格式有误';
        }else if(!postData.retail_price){
            content='请填写零售价';
        }else if(!isMoney(postData.retail_price)){
            content='价格格式有误';
        }else if(!postData.thumb_img){
            content='请上传缩略图';
        }else if(!postData.category_id_1){
            content='请选择分类标签';
        }else if(!postData.main_img){
            content='请上传首焦图';
        }else if(!postData.parameters){
            content='请填写商品参数';
        }else if(!postData.details_img){
            content='请上传商品详情图';
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var _this = $(this);
        _this.addClass("nodisabled");
        $.post(controller+'edit',postData,function(msg){
            _this.removeClass("nodisabled");
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                dialog.success(msg.info,controller+'manage');
            }
        })
    });
    $('body').on('click','.category-tab li,.category-type li',function(){
        var _this = $(this);
        _this.addClass('current').siblings().removeClass('current');
    });
    

});
//单图片上传弹窗
function uploadsImg(obj,tilt,className) {
    // alert(1);
    var uploadSingleImgHtml=$('#uploadSingleImgHtml').html();
    layer.open({
        title:[tilt,'border-bottom:1px solid #d9d9d9;'],
        className:className,
        content:uploadSingleImgHtml,
        btn:['确定','取消'],
        success:function(){
            var imgSrc=obj.siblings('.hidden_img').val();
            var echoedImg = '';
            if(imgSrc.indexOf("uploads") == -1 && imgSrc != ''){
                echoedImg= uploads+imgSrc;
            }
            //显示图片
            $('.'+ className).find('img').attr('src',echoedImg);
            //赋值回
            $('.'+ className).find('.img').val(imgSrc);
        },
        yes:function(index){
            var layerImgSrc= $('.'+ className).find('.img').val();
            obj.siblings('.hidden_img').val(layerImgSrc);
            if(layerImgSrc.indexOf("data:") !=-1){
                errorTipc('文件还没上传完毕')
                return false;
            }
            layer.close(index);
        }
    })
}

//单视频上传弹窗
function uploadsVideo(obj,tilt,className) {
    var uploadSingleVideoHtml=$('#uploadSingleVideoHtml').html();
    layer.open({
        title:[tilt,'border-bottom:1px solid #d9d9d9;'],
        className:className,
        content:uploadSingleVideoHtml,
        btn:['确定','取消'],
        success:function(){
            var imgSrc=obj.siblings('.hidden_video').val();
            var echoedImg = '';
            if(imgSrc.indexOf("uploads") == -1 && imgSrc != ''){
                echoedImg= uploads+imgSrc;
            }
            //显示图片
            $('.'+ className).find('video').attr('src',echoedImg);
            //赋值回
            $('.'+ className).find('.img').val(imgSrc);
        },
        yes:function(index){
            var layerImgSrc= $('.'+ className).find('.img').val();
            obj.siblings('.hidden_video').val(layerImgSrc);
            if(layerImgSrc.indexOf("data:") !=-1){
                errorTipc('文件还没上传完毕')
                return false;
            }
            layer.close(index);
        }
    })
}




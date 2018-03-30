$(function(){

    //上传缩略图
    $('body').on('click','.upload-thumbnail',function(){
        var _this = $(this);
        uploadsImg(_this,'上传商品首焦图','uploadThumbnailLayer');
    });
    //上传首焦图
    $('body').on('click','.uploadFocusPicture',function(){
        var _this = $(this);
        uploadsImg(_this,'上传商品首焦图','uploadFocusLayer');
    });

    //初始化
    $('#categoryContent').find('li :first').addClass('current');
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
                });
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

    //设置品类
    var categoryContent=$('#categoryContent').html();
    $('body').on('click','.set-category',function(){
        layer.open({
            title:['设置商品的检索品类','border-bottom:1px solid #d9d9d9'],
            className:'categoryContentLayer',
            content:categoryContent,
            btn:['确定','取消'],
            success:function(){
                var cat_id_1 =  $('#categoryContent').find('li :first a').data('id');
                var categoryIdArr=$('.select-category').data('category-id');
                //console.log(typeof categoryIdArr[0]);
                // $.get(domain+'index_admin/Category/getSecondCategoryById',{cat_id_1:cat_id_1},function(msg){
                //     $('.category-content-wrapper').empty();
                //     $('.category-content-wrapper').append(msg);
                // });

                $.each($('.category-tab li'),function(){
                    var _this=$(this);
                    var _thisId=_this.find('a').data('id');
                    console.log(_thisId);
                    if(_thisId==categoryIdArr[0]){
                        alert(1);
                        _this.addClass('current');
                        return false;
                    }
                });
                // $.each($('.category-type li'),function(){
                //     var _this=$(this);
                //     var _thisId=_this.find('a').data('id');
                //     alert(_thisId);
                //     if(_thisId==categoryIdArr[1]){
                //         alert(2);
                //         _this.addClass('current').siblings().removeClass('current');
                //        // return false;
                //     }
                // })

            },
            yes:function(index){
                var categoryArr=[];
                $.each($('.category-tab li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        var first_category_id=$('.categoryContentLayer .category-tab>li.current').find('a').data('id');
                        categoryArr.push(first_category_id);
                        return false;
                    }
                });
                $.each($('.category-type li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        var second_category_id=$('.categoryContentLayer .category-type>li.current').find('a').data('id');
                        categoryArr.push(second_category_id);
                        return false;
                    }
                });
                console.log(categoryArr);
                $('.select-category').data('category-id',categoryArr);
                layer.close(index);
            }
        })
    });
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
        postData.details_img=goodsDetail;
        var content='';
        if(!postData.name){
            content='请填写商品名称';
        }else if(!postData.trait){
            content='请填写商品特点';
        }else if(!isNumber(postData.settle_price)){
            content='请填写结算价';
        }else if(!isNumber(postData.retail_price)){
            content='请填写零售价';
        }else if(!postData.thumb_img){
            content='请上传缩略图';
        }else if(!postData.tag){
            content='请选择分类标签';
        }else if(!postData.main_img){
            content='请上传首焦图';
        }else if(!postData.parameters){
            content='请填写商品参数';
        }else if(!!postData.details_img){
            content='请上传商品详情图';
        }
        if(content){
            dialog.error(content);
            return false;
        }
        $.post(controller+'add',postData,function(msg){
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                dialog.success(msg.info);
            }
        })
    });
    $('body').on('click','.category-tab li,.category-type li',function(){
        var _this = $(this);
        _this.addClass('current').siblings().removeClass('current');
    });


    //获取二级分类
    $('body').on('click','.first_category',function () {
        var _this = $(this);
        var cat_id_1 = _this.data('id');
        $.get(domain+'index_admin/Category/getSecondCategoryById',{cat_id_1:cat_id_1},function(msg){
            $('.category-content-wrapper').empty();
           $('.category-content-wrapper').append(msg);
        });

    })



});
//单图片上传的弹窗
function uploadsImg(obj,tilt,className) {
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
            layer.close(index);
        }
    })
}



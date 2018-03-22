$(function(){
    $('body').on('change','#file',function(){
        var file = $(this);
        var fileList = $(this).get(0).files;
        var imgContainer = $('.multi-picture-module');
        var imgArr = [];
        for (var i = 0; i < fileList.length; i++) {                
            var img = event.target.files[i];
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
                var html=$('#img_list').html();
                //imgArr.push(imgUrl);
                var img=  $('<img src="" class="upload_img">');
                img.attr("src", imgUrl);
                var imgAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleEditImg" name=""><span class="delete-picture">X</span></div></li>');
                imgAdd.find('.picture-module').append(img);
                imgContainer.append(imgAdd);
            }
        };
    });
    //删除
    $('body').on('click','.delete-picture',function(){
        $(this).parents('li').remove();
    })
    //编辑商品详情 上传多张图片 确认上传
    var editDetail=$('#editDetail').html();
    $('body').on('click','.editDetail',function(){
        // var array = ["/uploads/temp/15214424210.jpeg", "/uploads/temp/15214424211.jpeg"];
        //  $('.goods-detail').data('src',array);
        uploadsMultiImg(editDetail);
    }); 
})
function uploadsMultiImg(content){
    layer.open({
            // title:['商品分类标签','border-bottom:1px solid #d9d9d9'],
            className:'editDetailLayer',
            content:content,
            btn:['确定','取消'],
            success:function(){
                var html=$('#img_list').html();
                var multiImgAttr=$('.goods-detail').data('src');
                console.log(multiImgAttr)
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
                if(layermultiImgAttr.length==0){
                    layer.close(index);
                    return false;
                }
                var postDate = {};
                postDate.imgs = layermultiImgAttr;
                $.post('uploadMultiImgToTemp',postDate,function(info){
                   if(info.status == 0){
                       dialog.error(info.msg);
                       return false;
                   }
                    var imgArray = [];
                    $.each(info.info,function(index,img){
                        if(img.indexOf("uploads") == -1 && img !=''){
                            img = uploads+img;
                        }
                        imgArray.push(img);
                    });

                    $('.goods-detail').data('src',imgArray);
                    layer.close(index);
                })
            },
            no:function(){
                $('.editDetailLayer li').remove();
            }
        })
}


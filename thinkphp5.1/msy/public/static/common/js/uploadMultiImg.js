$(function(){
    //多图片上传
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
    //选择视频上传
    $('body').on('change','#video',function(){
        var file = $(this);
        var fileList = $(this).get(0).files;
        var imgContainer = $('.multi-picture-module');
        var imgArr = [];
        for (var i = 0; i < fileList.length; i++) {                
            var video = event.target.files[i];
            console.log(video);
            var obj=$(this).parent();
            // 判断是否图片
            // if(!video){
            //     return false;
            // }
            // // 判断图片格式
            // var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
            // if(!(video.type.indexOf('image')==0 && video.type && imgRegExp.test(video.name)) ){
            //     layer.open({
            //         content:'请上传：jpg、jpeg、png、gif格式图片',
            //         time:2
            //     }) ;
            // }

            var reader = new FileReader();
            reader.readAsDataURL(video);

            reader.onload = function(e){
                var videoUrl=e.target.result;
                var html=$('#img_list').html();
                //imgArr.push(imgUrl);
                var video=  $('<video src="" class="upload_img" accept="video/*" autoplay="autoplay"></video>');
                video.attr("src", videoUrl);
                var videoAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleVideo" name=""><span class="delete-picture">X</span></div></li>');
                videoAdd.find('.picture-module').append(video);
                imgContainer.append(videoAdd);             
            }
        };
    });
    //上传视频
    var goodsVideoList=$('#goodsVideoList').html();
    $('body').on('click','.uploadGoodsVideo',function(){
        uploadsMultiVideo(goodsVideoList);
    });
     //上传单图片和描述
    $('body').on('change','.uploadImgDescribe',function () {
        var img = event.target.files[0];
        var obj=$(this).parent();
        var imgContainer = $('.multi-picture-module');
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
            // $(obj).addClass('active');
            var oLiLen=imgContainer.find('li').length;
            var img=  $('<img src="" class="upload_img">');
            img.attr("src", imgUrl);
            var imgAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleEditImg" name=""><a class="delete-picture">X</a></div><a href="javascript:void(0);" class="edit-describe">编辑照片描述</a><textarea name="" id="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea></li>');
            imgAdd.find('.picture-module').append(img);
            imgContainer.append(imgAdd);
           
            
            //提交
            // $.post("uploadImgToTemp",postData,function(msg){
            //     if(msg.status == 1){
            //         $(obj).find('.img').val(msg.info);
            //         $(obj).find('img').attr('src','/uploads/'+msg.info);
            //     }else{
            //         dialog.error(msg.info)
            //     }
            // })
        }
    });
    //上传单视频和描述
    $('body').on('change','.uploadVideoDescribe',function () {
        var img = event.target.files[0];
        var obj=$(this).parent();
        var imgContainer = $('.multi-picture-module');
        // 判断是否图片
        // if(!img){
        //     return false;
        // }
        // // 判断图片格式
        // var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
        // if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
        //     layer.open({
        //         content:'请上传：jpg、jpeg、png、gif格式图片',
        //         time:2
        //     }) ;
        // }
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            var videoUrl=e.target.result;
            // $(obj).addClass('active');
            var video=  $('<video src="" class="upload_img" autoplay="autoplay"></video>');
            video.attr("src", videoUrl);
            var videoAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleEditImg" name=""><span class="delete-picture">X</span></div><a href="javascript:void(0);" class="edit-describe">编辑视频描述</a><textarea name="" id="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea></li>');
            videoAdd.find('.picture-module').append(video);
            imgContainer.append(videoAdd);
           
            
            //提交
            // $.post("uploadImgToTemp",postData,function(msg){
            //     if(msg.status == 1){
            //         $(obj).find('.img').val(msg.info);
            //         $(obj).find('img').attr('src','/uploads/'+msg.info);
            //     }else{
            //         dialog.error(msg.info)
            //     }
            // })
        }
    });
    //编辑商品详情 上传多张图片 确认上传
    var editDetail=$('#editDetail').html();
    $('body').on('click','.editDetail',function(){
        // var array = ["/uploads/temp/15214424210.jpeg", "/uploads/temp/15214424211.jpeg"];
        //  $('.goods-detail').data('src',array);
        uploadsMultiImg(editDetail);
    }); 
    //删除
    $('body').on('click','.delete-picture',function(){
        $(this).parents('li').remove();
    })

    // 修改单个图片
    $('body').on('change','.uploadSingleEditImg',function () {
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
            //提交
            $.post(controller + "uploadImgToTemp",postData,function(msg){
                if(msg.status == 1){
                    $(obj).find('.img').val(msg.info);
                    $(obj).find('img').attr('src','/uploads/'+msg.info);
                }else{
                    dialog.error(msg.info)
                }
            })
            $(obj).find('img').attr('src',imgUrl);
            $(obj).find('.img').val(imgUrl);
        }

    });
    //上传企业图片
    var uploadCompanyPic=$('#uploadCompanyPic').html();
    $('body').on('click','.uploadCompanyPic',function(){
        var _this=$(this);
        var storageDataObj=_this.next('input[type="hidden"]');
        uploadsImgDescribe(uploadCompanyPic,storageDataObj);
    });
    //上传企业视频
    var companyVideoList=$('#companyVideoList').html();
    $('body').on('click','.companyVideoList',function(){
        var _this=$(this);
        var storageDataObj=_this.next('input[type="hidden"]');
        uploadsVideoDescribe(companyVideoList,storageDataObj);
    }); 
    //编辑描述
    $('body').on('click','.edit-describe',function () {
        var _this=$(this);
        _this.next('.edit-text').toggleClass('active');
    })
})
//图片弹窗
function uploadsMultiImg(content){
    layer.open({
            title:['编辑商品详情','border-bottom:1px solid #d9d9d9'],
            className:'editDetailLayer',
            content:content,
            btn:['确定','取消'],
            success:function(){
                var html=$('#img_list').html();
                var multiImgSrc=$('.goods-detail').data('src');
                var multiImgAttr=multiImgSrc.split(',');
                for(var i=0;i<multiImgAttr.length-1;i++){
                    if(multiImgAttr[i].indexOf("uploads") == -1 && multiImgAttr[i] !=''){
                        multiImgAttr[i] = uploads+multiImgAttr[i];
                    }
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
                //$('.goods-detail').data('src',layermultiImgAttr);
                if(layermultiImgAttr.length==0){
                    $('.goods-detail').data('src','');
                    layer.close(index);
                    return false;
                }
                var postData = {};
                postData.imgs = layermultiImgAttr;
                $.post(controller + 'uploadMultiImgToTemp',postData,function(info){
                   if(info.status == 0){
                       dialog.error(info.msg);
                       return false;
                   }
                    var imgArray ='';
                    $.each(info.info,function(index,img){
                        if(img.indexOf("uploads") == -1 && img !=''){
                            img = uploads+img;
                        }
                        imgArray+=img+',';
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
//视频弹窗
function uploadsMultiVideo(content){
    layer.open({
            title:['上传商品视频','border-bottom:1px solid #d9d9d9'],
            className:'editVideoLayer',
            content:content,
            btn:['确定','取消'],
            success:function(){
                var html=$('#video_list').html();
                var multiVideoSrc=$('.goods-video').data('src');
                if(multiVideoSrc){
                    var multiVideoAttr=multiVideoSrc.split(',');
                    for(var i=0;i<multiVideoAttr.length-1;i++){
                        if(multiVideoAttr[i].indexOf("uploads") == -1 && multiVideoAttr[i] !=''){
                            multiVideoAttr[i] = uploads+multiVideoAttr[i];
                        }
                        $('.editVideoLayer .multi-picture-module').append(html);
                        $('.editVideoLayer video').eq(i).attr('src',multiVideoAttr[i]);
                    }
                }
            },
            yes:function(index){
                var layermultiVideoAttr=[];
                $.each($('.editVideoLayer li'),function(i,val){
                    var _this=$(this);
                    var videoSrc=_this.find('video').attr('src');
                    layermultiVideoAttr.push(videoSrc);
                })

                //$('.goods-video').data('src',layermultiVideoAttr);
                if(layermultiVideoAttr.length==0){
                    $('.goods-video').data('src','');
                    layer.close(index);
                    return false;
                }
                var postData = {};
                postData.imgs = layermultiVideoAttr;
                $.ajax({
                    url: controller + 'uploadMultiImgToTemp',
                    data: postData,
                    type: 'post',
                    beforeSend: function(){
                        //$('.loading').show();
                    },
                    success: function(info){
                        if(info.status == 0){
                            dialog.error(info.msg);
                            return false;
                        }
                        var videoArray = '';
                        $.each(info.info,function(index,img){
                            if(img.indexOf("uploads") == -1 && img !=''){
                                img = uploads+img;
                            }
                            videoArray+=img+',';
                        });
                            $('.goods-video').data('src',videoArray);
                            layer.close(index);
                    },
                    complete:function(){
                        
                    },
                    error:function (xhr) {
                        dialog.error('AJAX错误'+xhr);
                    },
                });
            }
        })


}
//图片描述弹窗
function uploadsImgDescribe(content,obj){
    layer.open({
            // title:['商品分类标签','border-bottom:1px solid #d9d9d9'],
            className:'editCompanyPicLayer',
            content:content,
            btn:['确定','取消'],
            success:function(){
                //var html=$('#img_list').html(); 模板
                var html='';
                    html+='<li>';
                    html+='<div class="picture-module active">';
                    html+='<input type="file" class="uploadImg uploadSingleEditImg" name="">';
                    html+='<a class="delete-picture">X</a>';
                    html+='<img src="" class="upload_img">';
                    html+='</div>';
                    html+='<a href="javascript:void(0);" class="edit-describe">编辑照片描述</a>';
                    html+='<textarea name="" id="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea>';
                    html+='</li>';                  
                var multiImgAttr=obj.data('src');
                for(var i=0;i<multiImgAttr.length;i++){
                    $('.editCompanyPicLayer .multi-picture-module').append(html);
                    $('.editCompanyPicLayer .upload_img').eq(i).attr('src',multiImgAttr[i].imgSrc);
                    $('.editCompanyPicLayer .edit-text').eq(i).val(multiImgAttr[i].imgText);
                }
            },
            yes:function(index){
                var layermultiImgAttr=[];
                var layerImgInfoData={};
                $.each($('.editCompanyPicLayer li'),function(i,val){
                    var _this=$(this);
                    var imgSrc=_this.find('img').attr('src');
                    var imgText=_this.find('textarea').val();
                    layerImgInfoData={
                        imgSrc:imgSrc,
                        imgText:imgText
                    }
                    layermultiImgAttr.push(layerImgInfoData);
                })
               
                // obj.data('src',layermultiImgAttr);
                obj.data('src',layermultiImgAttr);
                if(layermultiImgAttr.length==0){
                    layer.close(index);
                    return false;
                }
                var postDate = {};
                postDate.imgsWithDes = layermultiImgAttr;

                $.post(controller + 'uploadMultiImgToTempWithDes',postDate,function(info){
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
                layer.close(index);
            },
            no:function(){
                $('.editCompanyPicLayer li').remove();
            }
        })
}
//视频描述弹窗
function uploadsVideoDescribe(content,obj){
    layer.open({
            title:['上传企业视频','border-bottom:1px solid #d9d9d9'],
            className:'editCompanyPicLayer',
            content:content,
            btn:['确定','取消'],
            success:function(){
                //var html=$('#img_list').html(); 模板
                var html='';
                    html+='<li>';
                    html+='<div class="picture-module active">';
                    html+='<input type="file" class="uploadImg uploadSingleEditImg" name="">';
                    html+='<a class="delete-picture">X</a>';
                    html+='<video src="" class="upload_img"></video>';
                    html+='</div>';
                    html+='<a href="javascript:void(0);" class="edit-describe">编辑照片描述</a>';
                    html+='<textarea name="" id="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea>';
                    html+='</li>';                  
                var multiImgAttr=obj.data('src');
                console.log(multiImgAttr);
                for(var i=0;i<multiImgAttr.length;i++){
                    $('.editCompanyPicLayer .multi-picture-module').append(html);
                    $('.editCompanyPicLayer .upload_img').eq(i).attr('src',multiImgAttr[i].imgSrc);
                    $('.editCompanyPicLayer .edit-text').eq(i).val(multiImgAttr[i].imgText);
                }
            },
            yes:function(index){
                var layermultiImgAttr=[];
                var layerImgInfoData={};
                $.each($('.editCompanyPicLayer li'),function(i,val){
                    var _this=$(this);
                    var imgSrc=_this.find('video').attr('src');
                    var imgText=_this.find('textarea').val();
                    layerImgInfoData={
                        imgSrc:imgSrc,
                        imgText:imgText
                    }
                    layermultiImgAttr.push(layerImgInfoData);
                });
               
                // obj.data('src',layermultiImgAttr);
                obj.data('src',layermultiImgAttr);
                if(layermultiImgAttr.length==0){
                    layer.close(index);
                    return false;
                }
                
                var postDate = {};
                postDate.imgsWithDes = layermultiImgAttr;
                $.post(controller +'uploadMultiImgToTempWithDes',postDate,function(info){
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
                layer.close(index);
            },
            no:function(){
                $('.editCompanyPicLayer li').remove();
            }
        })
}


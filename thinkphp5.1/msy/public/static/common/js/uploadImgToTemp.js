$(function(){
    // 选择单图片
    $('body').on('change','.uploadSingleImg',function () {
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
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            var imgUrl=e.target.result;
            $(obj).addClass('active');
            var postData = {img: e.target.result};
            // postData.imgWidth = 145;
            // postData.imgHeight = 100;
            $(obj).find('img').attr('src',imgUrl);
            $(obj).find('.img').val(imgUrl);
            //提交
            $.post(controller+"uploadImgToTemp",postData,function(msg){
                if(msg.status == 1){
                    $(obj).find('.img').val(msg.info);
                    $(obj).find('img').attr('src','/uploads/'+msg.info);
                }else{
                    dialog.error(msg.info)
                }
            })
        }

    });
    // 选择单视频
    $('body').on('change','.uploadSingleVideo',function () {
        var img = event.target.files[0];
        var obj=$(this).parent();
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
            var postData = {img: e.target.result};
            var videoUrl=e.target.result;
             $(obj).find('video').attr('src',videoUrl);
            $(obj).find('.img').val(videoUrl);
            //提交
            $.post(controller+"uploadImgToTemp",postData,function(msg){
                if(msg.status == 1){
                    console.log(msg.info);
                    $(obj).find('.img').val(msg.info);
                    $(obj).find('img').attr('src','/uploads/'+msg.info);
                }else{
                    dialog.error(msg.info)
                }
            })
        }
    })
  
});

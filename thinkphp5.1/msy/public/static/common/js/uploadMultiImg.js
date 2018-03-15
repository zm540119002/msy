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
                    imgArr.push(imgUrl);
                    console.log(imgArr);
                    var img=  $('<img src="" class="upload_img">');
                    img.attr("src", imgUrl);
                    console.log(imgUrl);
                    var imgAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg" name=""><span class="delete-picture">X</span></div></li>');
                    imgAdd.find('.picture-module').append(img);
                    imgContainer.append(imgAdd);             
                    }

                
            };
        });
        //删除
         $('body').on('click','.delete-picture',function(){
            $(this).parents('li').remove();
        })


    
})



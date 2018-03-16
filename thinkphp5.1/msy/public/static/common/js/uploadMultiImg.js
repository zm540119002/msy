$(function(){
    $('body').on('change','#file',function(){
        var file = $(this);
        var fileList = $(this).get(0).files;
        var imgContainer = $('.multi-picture-module');
        var imgArr = [];
        for (var i = 0; i < fileList.length; i++) {                
            var img111 = event.target.files[i];
            console.log(img111);
            var obj=$(this).parent();
            // 判断是否图片
            if(!img111){
                return false;
            }
            // 判断图片格式
            var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
            if(!(img111.type.indexOf('image')==0 && img111.type && imgRegExp.test(img111.name)) ){
                layer.open({
                    content:'请上传：jpg、jpeg、png、gif格式图片',
                    time:2
                }) ;
            }

            var reader = new FileReader();
            reader.readAsDataURL(img111);

            reader.onload = function(e){
                var imgUrl=e.target.result;
                var html=$('#img_list').html();
                //imgArr.push(imgUrl);
                var img=  $('<img src="" class="upload_img">');
                alert(imgUrl);
                img.attr("src", imgUrl);
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
    //编辑商品详情 上传多张图片 确认上传
   
    $('body').on('click','.editDetail',function(){
        alert($('.goods-detail').data('src').length)
        uploadsMultiImg();
    }); 
})
function uploadsMultiImg(){
    // alert(1);
    var editDetail=$('#editDetail').html();
    layer.open({
            // title:['商品分类标签','border-bottom:1px solid #d9d9d9'],
            className:'editDetailLayer',
            content:editDetail,
            btn:['确定','取消'],
            success:function(){
                // var html=$('#img_list').html();
                // html+='<li>';
                // html+='<div class="picture-module active">';
                // html+='<input type="file" class="uploadImg" name="">';
                // html+='<span class="delete-picture">X</span>';
                // html+='<img src="" class="upload_img">';
                // html+='</div>'
                // html+='</li>';
                
                // var multiImgAttr=$('.goods-detail').data('src');
                
                // for(var i=0;i<multiImgAttr.length;i++){
                //     console.log(i)
                //     $('.editDetailLayer .multi-picture-module').append(html);
                //     $('.editDetailLayer .upload_img').eq(i).attr('src',multiImgAttr[i]);
                // }
            },
            yes:function(index){
                var layermultiImgAttr=[];
                alert($('.editDetailLayer li').length);
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
                    $('.goods-detail1').data('src',layermultiImgAttr);
                    layer.close(index);
                })
            }
        })
}


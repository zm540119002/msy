$(document).ready(function(){
    // 选择图片
    $('[type=file]').on('change',function(){
        var _this = $(this);
        var img = event.target.files[0];
        // 判断是否图片
        if(!img){
            return false;
        }
        // 判断图片格式
        if(!(img.type.indexOf('image')==0 && img.type && /\.(?:jpg|png|gif|jpeg)$/.test(img.name)) ){
            alert('图片只能是jpg,jpeg,gif,png');
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(img);

        reader.onload = function(e){
            var postData = {};
            postData.img = e.target.result;
            //提交
            $.post(CONTROLLER + '/uploadImgToTemp',postData,function(msg){
                if(msg.status == 1){
                    _this.parents('td').find('.img').attr("src",UPLOAD + msg.info);
                    _this.attr('src',msg.info);
                }else{
                    parent.layer.open({
                        content:msg.info,
                        time:2
                    });
                    return false;
                }
            },'json');
        }
    });
});
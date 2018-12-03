/**
 * Created by Administrator on 2018/3/26.
 */
$(function(){
    tab_down('.apply-data-nav li','.apply-module','click');
    //填写商标资料
    $('body').on('click','.one-step',function(){
        var tardemark=$('.trademark-info').data('trademark');
        var postData={};
        var postData=$('.trademarkform').serializeObject();
        postData.cat_id_1=tardemark;
        var content='';
        if(!postData.name){
            content='请填写商标全称';
        }else if(!postData.brand_img){
            content='请上传商标图片';
        }
        if(content){
            dialog.error(content);
            return false;
        }else{
            $('.weui-flex-item:eq(0)').removeClass('current');
            $('.weui-flex-item:eq(1)').addClass('current');
            $('.apply-module:eq(0)').hide();
            $('.apply-module:eq(1)').show();
        }
    });
    //商标所属分类
    var edittradeMark=$('#edittradeMark').html();
    $('body').on('click','.trademark-type',function(){

        layer.open({
            title:['选择商标所属分类','border-bottom:1px solid #d9d9d9;'],
            className:'trademarkTypeLayer',
            content:edittradeMark,
            btn:['确定','取消'],
            success:function(){
                var tardeMarkId=$('.trademark-info').data('trademark');
                var tardeMarkIdArr=tardeMarkId.split(',');
                $('.trademarkTypeLayer li').on('click',function(){
                    var _this=$(this);
                    var tradeMarkLen=$('.trademarkTypeLayer .current').length;
                    if(_this.hasClass('current')||tradeMarkLen<3){
                        _this.toggleClass('current');

                    }else{
                        return false;
                    }

                });
                for(var i=0;i<tardeMarkIdArr.length;i++){
                    $.each($('.trademarkTypeLayer li'),function(){
                        var _this=$(this);
                        var currentId=_this.data('id');
                        if(tardeMarkIdArr[i]==currentId){
                            _this.addClass('current');
                        }

                    })
                }
            },
            yes:function(index){
                var tardeMarkArr='';
                $.each($('.trademarkTypeLayer li'),function(){
                    var _this=$(this);
                    if(_this.hasClass('current')){
                        tardeMarkLayerId  =_this.data('id');
                        //tardeMarkArr.push(tardeMarkLayerId);
                        tardeMarkArr += tardeMarkLayerId+',';
                    }

                });
                $('.trademark-info').data('trademark',tardeMarkArr);
                console.log($('.trademark-info').data('trademark'));
                layer.close(index);
            }
        })
    })
    
    //递交商标资料
    $('body').on('click','.two-step',function(){
        var trademark=$('.trademark-info').data('trademark');
        var postData={};
        var postData=$('.trademarkform').serializeObject();
        postData.category_id_1=trademark;
        var content='';
        if(!postData.name){
            content='请填写商标名称';
        }else if(!postData.brand_img){
            content='请上传商标图片';
        }else if(!postData.certificate){
            content='请上传商标证书图片';
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var _this = $(this);
        _this.addClass("nodisabled");//防止重复提交
        $.post(controller+"record",postData,function(msg){
            _this.removeClass("nodisabled");
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                dialog.success(msg.info,controller+'manage');
            }
        })
    })

})


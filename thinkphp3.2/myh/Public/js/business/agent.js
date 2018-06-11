//我的代理商
function myAgent(config) {
    var postData = $.extend({},config);
    $.ajax({
        url: ACTION,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function (xhr) {
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            $('section.agentList').html(data);
        }
    });
}




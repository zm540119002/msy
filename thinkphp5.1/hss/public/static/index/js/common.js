
$(function(){
    // 分享二维码
    $('body').on('click', '.url_share', function () {
        var param_url = window.location.href;

        if (param_url) {
            $.ajax({
                url: module + '/TwoDimensionalCode/getUrlQRcode',
                data: {param_url:param_url},
                type: 'post',
                success: function (data) {

                    layer.open({
                        skin: 'shareqrCodeLayer',
                        content: '<img src=' + data.img + ' />',
                        btn: ['X']
                    })
                }
            });
        }
    });

});

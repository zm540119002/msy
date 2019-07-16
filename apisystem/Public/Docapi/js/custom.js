$(function() {

    $('#side-menu').metisMenu();

});

// +----------------------------------------------------------------------
// | ApiSystem接口文档管理系统 让沟通更简单
// | Copyright (c) 2015 http://www.apisystem.cn
// | Author: Texren  QQ: 174463651
// |         Smith77 QQ: 3246932472
// | 交流QQ群 577693968 交流QQ群2 460098419
// +----------------------------------------------------------------------
// Full height of right content
/*function fix_height() {
    //var heightWithoutNavbar = $("body > #page-wrapper").height() - 77.25;
    //$(".main-page").css("min-height", heightWithoutNavbar + "px");
    //alert(heightWithoutNavbar);
    //var navbarHeigh = $('nav.navbar-default').height();
    var wrapperHeigh = $('#page-wrapper').height();
    //var headerHeight = $('#header-wrapper').height();
    //var rightHeight = $(window).height()-headerHeight-5;
    ////if(navbarHeigh > wrapperHeigh){
    alert(wrapperHeigh);
    $('#page-wrapper').css("min-height", wrapperHeigh + "px");
    ////}
    //
    //$('#left-wrapper').css("min-height", rightHeight  + "px");
    //$('#page-wrapper').css("min-height", rightHeight  + "px");
    //$('#apiSystemContent').css("min-height", rightHeight  + "px");


}
fix_height();*/

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        //height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
            $("#apiSystemContent").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});

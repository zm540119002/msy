/**
 * jquery.area.js
 * 移动端省市区三级联动选择插件
 * author: zgf
 * date: 2018-1-11
**/

/*定义三级省市区数据*/

//获取省市数据
function getCity(areaData){
    PROVINCE_CITY_AREA=areaData;
    console.log(PROVINCE_CITY_AREA);
    intProvince(PROVINCE_CITY_AREA);
}
//设置省市
function setCity(backData){
    $("#expressArea .area_address").html(backData);
}
var expressArea='',expressCity='',expressCounty='', areaCont, areaList = $("#areaList");
	// , areaTop = areaList.offset().top;

/*初始化省份*/
    function intProvince(PROVINCE_CITY_AREA){
        areaCont = "";
        $.each(PROVINCE_CITY_AREA, function (p_k,p_v) {
            areaCont += "<li value='" + p_k + "' onClick='selectP(" + p_k + ")'>" + p_v.name + "</li>";
            areaList.html(areaCont);
            $("#areaBox").scrollTop(0);
            $("#backUp").removeAttr("onClick").hide();
        });
    }
   
    /*选择省份*/
    function selectP(p) {
        //expressArea+=provinceValue;
        console.log(p);
        var oLi=areaList.find('li');
        $.each(oLi,function(){
            if($(this).attr('value')==p){
                console.log($(this).text());
                if(!expressArea){
                    expressArea+=$(this).text();
                    //return false;
                }
                expressArea='';
                expressArea+=$(this).text();
            }
        });
        areaCont = "";
        areaList.html("");
        $.each(PROVINCE_CITY_AREA, function (p_k,p_v) {
            if(p==p_k){
                $.each(p_v.city, function (c_k, c_v) {
                    console.log(c_v);
                    areaCont += "<li  province_id='"+p_k+"' value='" + c_k + "' onClick='selectC(" + p_k + "," + c_k + ")'>" + c_v.name + "</li>";
                });
            }
        });	
        areaList.html(areaCont);
        $("#areaBox").scrollTop(0);
        $("#backUp").attr("onClick", "intProvince(PROVINCE_CITY_AREA);").show();
        //clockArea();
    }
    /*选择城市*/
    function selectC(p,cId) {
        var oLi=areaList.find('li');
        expressCity='';
        $.each(oLi,function(){
            if($(this).attr('value')==cId){
                expressCity+=$(this).text();    
            }
        });
        console.log(expressCity);
        expressArea+=expressCity;
        $("#expressArea .area_address").html(expressArea);
        areaCont = "";
        // $.each(PROVINCE_CITY_AREA, function (p_k,p_v) {
        //     if(p==p_k){
        //         $.each(p_v.city,function(c_k,c_v){
        //             console.log(c_v);
        //             if(cId==c_k){
        //                 $.each(c_v.area, function (a_k, a_v) {
        //                     //console.log(a_v);
        //                     areaCont += "<li  province_id='"+p_k+"' value='" + a_k + "' onClick='selectD(" + a_k + ");'>" + a_v + "</li>";
        //                 });
        //             }
        //         })
        //     }
        // });	
        //areaList.html(areaCont);
        $("#areaBox").scrollTop(0);
        $("#backUp").attr("onClick", "selectP(" + p + ");");
        clockArea();
    }

    /*选择区县*/
    // function selectD(p) {
    //     //expressArea += district[p][c][d];
    //     var oLi=areaList.find('li');
    //     expressCounty='';
    //     $.each(oLi,function(){
    //         var id=$(this).attr('value');
    //         if(id==p){
    //             expressCounty+=$(this).text();
    //         }
    //     })
    //     expressArea+=expressCity+expressCounty;
    //     $("#expressArea .area_address").html(expressArea);
    //     clockArea();
    // }

 /*关闭省市区选项*/
function clockArea() {
    $("#areaMask").fadeOut();
    $("#areaLayer").hide().animate({"bottom": "-100%"});
    intProvince(PROVINCE_CITY_AREA);
}

$(function() {
    /*打开省市区选项*/
    $("#expressArea").click(function() {
        $("#areaMask").fadeIn();
        $("#areaLayer").show().animate({"bottom": 0});
    });
    /*关闭省市区选项*/
    $("#areaMask, #closeArea").click(function() {
        clockArea();
    });
});
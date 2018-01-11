/**
 * jquery.area.js
 * 移动端省市区三级联动选择插件
 * author: zgf
 * date: 2018-1-11
**/

//获取省市数据
var expressArea='',expressCity='',expressCounty='', areaCont,areaBackCont, areaList = $("#areaList");
	// , areaTop = areaList.offset().top;
var areaObject={
    provinceCityD:[],
    //获取数据
    getCity:function(areaData){
        PROVINCE_CITY_AREA=areaData;
        areaObject.intProvince(PROVINCE_CITY_AREA);
    },
    /*初始化省份*/
    intProvince:function (PROVINCE_CITY_AREA){
        areaCont = "";
        $.each(PROVINCE_CITY_AREA, function (p_k,p_v) {
            areaCont += "<li value='" + p_k + "' onClick='areaObject.selectP(" + p_k + ")'>" + p_v.name + "</li>";
            areaList.html(areaCont);
            $("#areaBox").scrollTop(0);
            $("#backUp").removeAttr("onClick").hide();
        });
    },
    
    /*选择省份*/
    selectP:function(p) {
        //expressArea+=provinceValue;
        console.log(p);
        var oLi=areaList.find('li');
        $.each(oLi,function(){
            if($(this).attr('value')==p){
                //console.log($(this).text());
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
                    //console.log(c_v);
                    areaCont += "<li  province_id='"+p_k+"' value='" + c_k + "' onClick='areaObject.selectC(" + p_k + "," + c_k + ")'>" + c_v.name + "</li>";
                });
            }
        });	
        areaList.html(areaCont);
        $("#areaBox").scrollTop(0);
        $("#backUp").attr("onClick", "areaObject.intProvince(PROVINCE_CITY_AREA);").show();
        //clockArea();
    },
    /*选择城市*/
    selectC:function (p,cId) {
        var oLi=areaList.find('li');
        expressCity='';
        $.each(oLi,function(){
            if($(this).attr('value')==cId){
                expressCity+=$(this).text();    
            }
        });
        expressArea+=expressCity;
        $("#expressArea .area_address").html(expressArea);
        areaObject.provinceCityD=[];
        areaObject.provinceCityD.push(p,cId);
        $('.detail_address').val(expressArea).data('key',areaObject.provinceCityD);
        //console.log(areaObject.provinceCityD);
        areaCont = "";
        $("#areaBox").scrollTop(0);
        $("#backUp").attr("onClick", "areaObject.selectP(" + p + ");");
        clockArea();
    },
     //设置省市
    setArea:function (optionArr){
        areaBackCont='';
        // console.log(optionArr);
        // console.log(PROVINCE_CITY_AREA);
        //$("#expressArea .area_address").html(backData); +val.city[j].name
        $.each(PROVINCE_CITY_AREA, function (i,val) {
            //console.log(val);
            for(var j=0;j<optionArr.length;j++){
                if(i==optionArr[j]){
                    areaBackCont+=val.name;
                    //console.log(areaBackCont);
                    for(var k=0;k<val.city.length;k++){
                        if(k==optionArr[j+1]){
                            //console.log(optionArr[j+1]);
                            areaBackCont+=val.city[k].name;
                            //console.log(areaBackCont);
                            $("#expressArea .area_address").html(areaBackCont);
                            return false;
                        }
                    }
                    
                    
                }
            }
        })
    },
    areaInit:function(json){
        areaObject.getCity(json);
    }
}
 /*关闭省市区选项*/
function clockArea() {
    $("#areaMask").fadeOut();
    $("#areaLayer").hide().animate({"bottom": "-100%"});
    areaObject.intProvince(PROVINCE_CITY_AREA);
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
    $.fn.extend({
		areaInit:function(opt){
             areaObject.areaInit(opt);
        },
        setArea:function (options) {
            var address = areaObject.setArea(options);
            $(this).text(address);
        },
        getArea:function(){
            return $(this).data('key');
        }
	});
});
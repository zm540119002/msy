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
            areaCont += "<li value='" + p_v.id + "' onClick='areaObject.selectP(" + p_v.id + ")'>" + p_v.name + "</li>";
            areaList.html(areaCont);
            $("#areaBox").scrollTop(0);
            $("#backUp").removeAttr("onClick").hide();
        });
    },
    
    /*选择省份*/
    selectP:function(p) {
        //expressArea+=provinceValue;
        var oLi=areaList.find('li');
        $.each(oLi,function(){
            if($(this).attr('value')==p){
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
            
                $.each(p_v.city, function (c_k, c_v) {
                    if(p==c_v.province_id){
                        areaCont += "<li  province_id='"+c_v.province_id+"' value='" + c_v.id + "' onClick='areaObject.selectC(" + c_v.province_id + "," + c_v.id + ")'>" + c_v.name + "</li>";
                     }
             });
           
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
        $("#expressArea .province_city").html(expressArea);
        areaObject.provinceCityD=[];
        areaObject.provinceCityD.push(p,cId);
        $('input.province_city').val(expressArea).data('key',areaObject.provinceCityD);
        areaCont = "";
        $("#areaBox").scrollTop(0);
        $("#backUp").attr("onClick", "areaObject.selectP(" + p + ");");
        clockArea();
    },
     //设置省市
    setArea:function (optionArr){
        areaBackCont='';
        $.each(PROVINCE_CITY_AREA, function (i,val) {
            for(var j=0;j<optionArr.length;j++){
                if(val.id==optionArr[j]){
                    areaBackCont+=val.name;
                    $.each(val.city,function(k,obj){
                        if(obj.id==optionArr[j+1]){
                            areaBackCont+=obj.name;
                            $("div.province_city").html(areaBackCont);
                            $('input.province_city').data('key',optionArr);
                            return false;
                        }
                    })
                    
                    
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
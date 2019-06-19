
//借用上展的json转换器
;(function(){
    var data = [],data2 = [];

    //var url = './area.json';
    //var str = jQuery.getJSON(url);
    var js = document.scripts;
//js[js.length - 1] 就是当前的js文件的路径
    var areaJson = js[js.length - 1].src.substring(0, js[js.length - 1].src.lastIndexOf("/") + 1)+'area.json';
    //var dataJson;
    var Data;
    var cityData = $.get(areaJson).done(function(cityData){
        //cityData = data;

        Data = cityData;

        //console.log(cityData);
        //return cityData;

        //console.log(data);
        //console.log(data2);
    });

    //console.info(areaData);
    //console.log(areaData);
    //console.info(document.currentScript.src);
    //return false;
    //console.log(dataJson);
    console.log(cityData.responseText);
    return false;
    //return false;
    cityData = JSON.stringify(cityData).replace(/\"id\":/g, "\"value\":");
	cityData = JSON.parse(cityData);

    for (var i = 0,length = cityData.length; i < length; i++) {
        if (cityData[i].parentId !== '100000') {
            data2.push(cityData[i]);
        }else{
            data.push(cityData[i]);
        }
    }
    $.each(data, function(index, val) {
        var parentId = val.value;
        var _val = val;
        _val.child = [];
        $.each(data2, function(index, val) {
            if (val.parentId === parentId) {
                _val.child.push(val);
            };
        });
    });
    window.dataJson = data;

    console.log(window.dataJson);
    return false;
})();
/**
 * jquery.area.js
 * 移动端省市区三级联动选择插件
 * author: 锐不可挡
 * date: 2016-06-17
**/

/*定义三级省市区数据*/
var province = 	["北京市", "天津市", "河北省", "河南省", "山西省", "山东省", "内蒙古自治区", "辽宁省", "吉林省", "黑龙江省", "上海市", "江苏省", "浙江省", "福建省", "江西省", "安徽省", "湖北省", "湖南省", "广东省", "广西壮族自治区", "海南省", "重庆市", "四川省", "贵州省", "云南省", "西藏自治区", "陕西省", "甘肃省", "青海省", "宁夏回族自治区", "新疆维吾尔自治区", "台湾省", "香港特别行政区", "澳门特别行政区"];
var city = 		[
					["市辖区"],
					["市辖区", "市辖县"],
					["石家庄市", "唐山市", "秦皇岛市", "邯郸市", "邢台市", "保定市", "张家口市", "承德市", "沧州市", "廊坊市", "衡水市", "省直辖县级行政单位"],
					["郑州市", "开封市", "洛阳市", "平顶山市", "安阳市", "鹤壁市", "新乡市", "焦作市", "濮阳市", "许昌市", "漯河市", "三门峡市", "商丘市", "周口市", "驻马店市", "南阳市", "信阳市", "省直辖县级行政单位"],
					["太原市", "大同市", "阳泉市", "长治市", "晋城市", "朔州市", "晋中市", "运城市", "忻州市", "临汾市", "吕梁市"],
					["济南市", "青岛市", "淄博市", "枣庄市", "东营市", "烟台市", "潍坊市", "济宁市", "泰安市", "威海市", "日照市", "滨州市", "德州市", "聊城市", "临沂市", "菏泽市", "莱芜市"],
					["呼和浩特市", "包头市", "乌海市", "赤峰市", "通辽市", "鄂尔多斯市", "呼伦贝尔市", "巴彦淖尔市", "乌兰察布市", "兴安盟", "锡林郭勒盟", "阿拉善盟"],
					["沈阳市", "大连市", "鞍山市", "抚顺市", "本溪市", "丹东市", "锦州市", "营口市", "阜新市", "辽阳市", "盘锦市", "铁岭市", "朝阳市", "葫芦岛市", "省直辖县级行政单位"],
					["长春市", "吉林市", "四平市", "辽源市", "通化市", "白山市", "白城市", "松原市", "延边朝鲜族自治州", "长白山保护开发区", "省直辖县级行政单位"],
					["哈尔滨市", "齐齐哈尔市", "鸡西市", "鹤岗市", "双鸭山市", "大庆市", "伊春市", "佳木斯市", "七台河市", "牡丹江市", "黑河市", "绥化市", "大兴安岭地区", "省直辖县级行政单位"],
					["市辖区", "市辖县"],
					["南京市", "无锡市", "徐州市", "常州市", "苏州市", "南通市", "连云港市", "淮安市", "盐城市", "扬州市", "镇江市", "泰州市", "宿迁市", "省直辖县级行政单位"],
					["杭州市", "宁波市", "温州市", "绍兴市", "湖州市", "嘉兴市", "金华市", "衢州市", "台州市", "丽水市", "舟山市", "省直辖县级行政单位"],
					["福州市", "厦门市", "莆田市", "泉州市", "漳州市", "龙岩市", "三明市", "南平市", "宁德市"],
					["南昌市", "赣州市", "宜春市", "吉安市", "上饶市", "抚州市", "九江市", "景德镇市", "萍乡市", "新余市", "鹰潭市", "省直辖县级行政单位"],
					["合肥市", "芜湖市", "蚌埠市", "淮南市", "马鞍山市", "淮北市", "铜陵市", "安庆市", "黄山市", "阜阳市", "宿州市", "滁州市", "六安市", "宣城市", "池州市", "亳州市", "省直辖县级行政单位"],
					["武汉市", "黄石市", "十堰市", "荆州市", "宜昌市", "襄阳市", "鄂州市", "荆门市", "黄冈市", "孝感市", "咸宁市", "随州市", "恩施土家族苗族自治州", "省直辖县级行政单位"],
					["长沙市", "株洲市", "湘潭市", "衡阳市", "邵阳市", "岳阳市", "张家界市", "益阳市", "常德市", "娄底市", "郴州市", "永州市", "怀化市", "湘西土家族苗族自治州", "省直辖县级行政单位"],
					["广州市", "深圳市", "珠海市", "汕头市", "佛山市", "韶关市", "湛江市", "肇庆市", "江门市", "茂名市", "惠州市", "梅州市", "汕尾市", "河源市", "阳江市", "清远市", "东莞市", "中山市", "潮州市", "揭阳市", "云浮市"],
					["南宁市", "柳州市", "桂林市", "梧州市", "北海市", "崇左市", "来宾市", "贺州市", "玉林市", "百色市", "河池市", "钦州市", "防城港市", "贵港市"],
					["海口市", "三亚市", "三沙市", "儋州市", "省直辖县级行政单位"],
					["市辖区", "市辖县"],
					["成都市", "绵阳市", "自贡市", "攀枝花市", "泸州市", "德阳市", "广元市", "遂宁市", "内江市", "乐山市", "资阳市", "宜宾市", "南充市", "达州市", "雅安市", "广安市", "巴中市", "眉山市", "阿坝藏族羌族自治州", "甘孜藏族自治州", "凉山彝族自治州"],
					["贵阳市", "六盘水市", "遵义市", "安顺市", "毕节市", "铜仁市", "黔西南布依族苗族自治州", "黔东南苗族侗族自治州", "黔南布依族苗族自治州", "省直辖县级行政单位"],
					["昆明市", "昭通市", "曲靖市", "玉溪市", "普洱市", "保山市", "丽江市", "临沧市", "楚雄彝族自治州", "红河哈尼族彝族自治州", "文山壮族苗族自治州", "西双版纳傣族自治州", "大理白族自治州", "德宏傣族景颇族自治州", "怒江傈僳族自治州", "迪庆藏族自治州"],
					["拉萨市", "昌都市", "日喀则市", "林芝市", "山南市", "那曲地区", "阿里地区"],
					["西安市", "铜川市", "宝鸡市", "咸阳市", "渭南市", "汉中市", "安康市", "商洛市", "延安市", "榆林市", "杨凌农业高新技术产业示范区", "省直辖县级行政单位"],
					["兰州市", "嘉峪关市", "金昌市", "白银市", "天水市", "酒泉市", "张掖市", "武威市", "定西市", "陇南市", "平凉市", "庆阳市", "临夏回族自治州", "甘南藏族自治州"],
					["西宁市", "海东市", "海北藏族自治州", "海南藏族自治州", "海西蒙古族藏族自治州", "黄南藏族自治州", "果洛藏族自治州", "玉树藏族自治州"],
					["银川市", "石嘴山市", "吴忠市", "固原市", "中卫市", "省直辖县级行政单位"],
					["乌鲁木齐市", "克拉玛依市", "吐鲁番市", "哈密市", "阿克苏地区", "喀什地区", "和田地区", "伊犁哈萨克自治州", "昌吉回族自治州", "博尔塔拉蒙古自治州", "巴音郭楞蒙古自治州", "克孜勒苏柯尔克孜自治州", "省直辖县级行政单位"],
					["台北市", "新北市", "桃园市", "台中市", "台南市", "高雄市", "基隆市", "新竹市", "嘉义市", "省直辖县级行政单位", "钓鱼岛列岛"],
					["香港岛", "九龙半岛", "新界"],
					["澳门半岛", "离岛", "无堂区划分区域"]
				];

var expressArea, areaCont, areaList = $("#areaList");
	// , areaTop = areaList.offset().top;

/*初始化省份*/
function intProvince() {
	areaCont = "";
	for (var i=0; i<province.length; i++) {
		areaCont += '<li onClick="areaObject.selectP(' + i + ');">' + province[i] + '</li>';
	}
	areaList.html(areaCont);
	$("#areaBox").scrollTop(0);
	$("#backUp").removeAttr("onClick").hide();
}
intProvince();
var areaObject={
	provinceCityD:'',
	/*选择省份*/
	selectP:function(p) {
		areaCont = "";
		areaList.html("");
		for (var j=0; j<city[p].length; j++) {
			areaCont += '<li onClick="areaObject.selectC(' + p + ',' + j + ');">' + city[p][j] + '</li>';
		}
		areaList.html(areaCont);
		$("#areaBox").scrollTop(0);
		//expressArea = province[p] + " > ";
		expressArea = province[p];
		$("#backUp").attr("onClick", "intProvince();").show();
		return expressArea;
	},

	/*选择城市*/
	selectC:function(p,c) {
		areaCont = "";
		for (var k=0; k<district[p][c].length; k++) {
			areaCont += '<li onClick="areaObject.selectD(' + p + ',' + c + ',' + k + ');">' + district[p][c][k] + '</li>';
		}
		areaList.html(areaCont);
		$("#areaBox").scrollTop(0);
		var sCity = city[p][c];
		if (sCity != "省直辖县级行政单位") {
			if (sCity == "东莞市" || sCity == "中山市" || sCity == "儋州市" || sCity == "嘉峪关市") {
				expressArea += sCity;
				$("#expressArea dl dd").html(expressArea);
				clockArea();
			} else if (sCity == "市辖区" || sCity == "市辖县" || sCity == "香港岛" || sCity == "九龙半岛" || sCity == "新界" || sCity == "澳门半岛" || sCity == "离岛" || sCity == "无堂区划分区域") {
				expressArea += "";
			} else {
				//expressArea += sCity + " > ";
				expressArea += sCity;
			}
		}
		$("#backUp").attr("onClick", "areaObject.selectP(" + p + ");");
		return sCity;
	},

	
	setArea:function(optionArr){
		var provinceName=areaObject.selectP(optionArr[0]);
		var cityName=areaObject.selectC(optionArr[0],optionArr[1]);
		var areaName=areaObject.selectD(optionArr[0],optionArr[1],optionArr[2]);
		return areaName;
	},
	getArea:function(){
		//return areaObject.provinceCityD.split(',');
		
		if(!areaObject.provinceCityD.length){
			areaObject.provinceCityD.length=[];
			return areaObject.provinceCityD
		}
		return areaObject.provinceCityD.substr(0, areaObject.provinceCityD.length - 1).split(',');
		
	}
}
/*关闭省市区选项*/
function clockArea() {
	$("#areaMask").fadeOut();
	$("#areaLayer").hide().animate({"bottom": "-100%"});
	intProvince();
}

$(function() {
	/*打开省市区选项*/
	$('body').on('click','.express-area a',function(){
		$("#areaMask").fadeIn();
		$("#areaLayer").show().animate({"bottom": 0});
	});
	/*关闭省市区选项*/
	$('body').on('click','#areaMask, #closeArea',function(){
		clockArea();
	});
	//加入到jquery对象空间下
	/*示例：
	var arr = [14,10,2];
    $('.area-address-name').setArea(arr);
    $('.area-address-name').getArea();
    */
	$.fn.extend({
		getArea:areaObject.getArea,
        setArea:function (options) {
			//console.log(options);
            var address = areaObject.setArea(options);
            $(this).text(address);
        }
	});
});

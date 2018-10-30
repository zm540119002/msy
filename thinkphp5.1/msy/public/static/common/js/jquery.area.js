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
var district = 	[
					/*北京市*/
					[["东城区", "西城区", "朝阳区", "海淀区", "石景山区", "丰台区", "通州区", "顺义区", "昌平区", "门头沟区", "房山区", "大兴区", "平谷区", "怀柔区", "密云区", "延庆区"]],
					/*天津市*/
					[
						["和平区", "河东区", "河西区", "河北区", "南开区", "红桥区", "滨海新区", "东丽区", "西青区", "津南区", "北辰区", "武清区", "宝坻区", "宁河区", "静海区"],
						["蓟县"]
					],
					/*河北省*/
					[
						["长安区", "桥西区", "新华区", "井陉矿区", "裕华区", "藁城区", "鹿泉区", "栾城区", "晋州市", "新乐市", "井陉县", "正定县", "行唐县", "灵寿县", "高邑县", "深泽县", "赞皇县", "无极县", "平山县", "元氏县", "赵县"],
						["路北区", "路南区", "古冶区", "开平区", "丰南区", "丰润区", "遵化市", "迁安市", "曹妃甸区", "滦县", "滦南县", "乐亭县", "迁西县", "玉田县"],
						["海港区", "山海关区", "抚宁区", "北戴河区", "青龙满族自治县", "昌黎县", "卢龙县"],
						["邯山区", "丛台区", "复兴区", "峰峰矿区", "武安市", "邯郸县", "临漳县", "成安县", "大名县", "涉县", "磁县", "肥乡县", "永年县", "邱县", "鸡泽县", "广平县", "馆陶县", "曲周县", "魏县"],
						["桥东区", "桥西区", "南宫市", "沙河市", "邢台县", "临城县", "内丘县", "柏乡县", "隆尧县", "任县", "南和县", "巨鹿县", "新和县", "广宗县", "平乡县", "威县", "清河县", "临西县", "宁晋县"],
						["竞秀区", "莲池区", "满城区", "清苑区", "徐水区", "安国市", "高碑店市", "涿州市", "涞水县", "阜平县", "定兴县", "唐县", "高阳县", "容城县", "涞源县", "望都县", "安新县", "易县", "曲阳县", "蠡县", "顺平县", "博野县", "雄县"],
						["桥东区", "桥西区", "宣化区", "下花园区", "崇礼区", "万全区", "张北县", "康保县", "沽源县", "尚义县", "蔚县", "阳原县", "怀安县", "涿鹿县", "赤城县", "怀来县"],
						["双桥区", "双滦区", "鹰手营子矿区", "承德县", "兴隆县", "滦平县", "隆化县", "平泉县", "丰宁满族自治县", "宽城满族自治县", "围场满族蒙古族自治县"],
						["运河区", "新华区", "泊头市", "黄骅市", "河间市", "任丘市", "沧县", "青县", "东光县", "海兴县", "盐山县", "肃宁县", "南皮县", "吴桥县", "献县", "孟村回族自治县"],
						["广阳区", "安次区", "霸州市", "三河市", "固安县", "永清县", "香河县", "大城县", "文安县", "大厂回族自治县"],
						["桃城区", "冀州市", "深州市", "枣强县", "武邑县", "武强县", "饶阳县", "安平县", "故城县", "阜城县", "景县"],
						["定州市", "辛集市"]
					],
					/*河南省*/
					[
						["中原区", "二七区", "金水区", "惠济区", "管城区", "上街区", "新郑市", "登封市", "荥阳市", "新密市", "中牟县"],
						["龙亭区", "鼓楼区", "禹王台区", "顺河区", "祥符区", "通许县", "杞县", "尉氏县"],
						["涧西区", "西工区", "老城区", "瀍河区", "洛龙区", "吉利区", "偃师市", "宜阳县", "孟津县", "新安县", "洛宁县", "栾川县", "伊川县", "汝阳县", "嵩县"],
						["新华区", "卫东区", "石龙区", "湛河区", "舞钢市", "鲁山县", "宝丰县", "叶县", "郏县"],
						["文峰区", "北关区", "殷都区", "龙安区", "林州市", "安阳县", "汤阴县", "内黄县"],
						["鹤山区", "山城区", "淇滨区", "浚县", "淇县"],
						["红旗区", "卫滨区", "牧野区", "凤泉区", "卫辉市", "辉县市", "新乡县", "获嘉县", "原阳县", "延津县", "封丘县"],
						["山阳区", "中站区", "解放区", "马村区", "沁阳市", "孟州市", "修武县", "博爱县", "武陟县", "温县"],
						["华龙区", "濮阳县", "清丰县", "南乐县", "台前县", "范县"],
						["魏都区", "禹州市", "长葛市", "许昌县", "鄢陵县", "襄城县"],
						["郾城区", "源汇区", "召陵区", "舞阳县", "临颍县"],
						["湖滨区", "陕州区", "灵宝市", "义马市", "渑池县", "卢氏县"],
						["睢阳区", "梁园区", "民权县", "宁陵县", "柘城县", "虞城县", "夏邑县", "睢县"],
						["川汇区", "项城市", "扶沟县", "西华县", "商水县", "沈丘县", "淮阳县", "郸城县", "太康县"],
						["驿城区", "西平县", "遂平县", "平舆县", "上蔡县", "正阳县", "泌阳县", "确山县", "汝南县"],
						["宛城区", "卧龙区", "南召县", "西峡县", "方城县", "镇平县", "内乡县", "淅川县", "社旗县", "唐河县", "新野县", "桐柏县"],
						["浉河区", "平桥区", "罗山县", "光山县", "潢川县", "淮滨县", "商城县", "新县", "息县"],
						["济源市", "巩义市", "汝州市", "永城市", "邓州市", "兰考县", "滑县", "长垣县", "鹿邑县", "新蔡县", "固始县"]
					],
					/*山西省*/
					[
						["迎泽区", "杏花岭区", "万柏林区", "尖草坪区", "小店区", "晋源区", "清徐县", "阳曲县", "娄烦县", "古交市"],
						["城区", "矿区", "南郊区", "新荣区", "左云县", "大同县", "天镇县", "浑源县", "广灵县", "灵丘县", "阳高县"],
						["城区", "矿区", "郊区", "盂县", "平定县"],
						["城区", "郊区", "潞城市", "长治县", "襄垣县", "屯留县", "平顺县", "黎城县", "壶关县", "长子县", "武乡县", "沁县", "沁源县"],
						["城区", "高平市", "泽州县", "阳城县", "陵川县", "沁水县"],
						["朔城区", "平鲁区", "山阴县", "应县", "怀仁县", "右玉县"],
						["榆次区", "介休市", "榆社县", "左权县", "和顺县", "昔阳县", "寿阳县", "太谷县", "祁县", "平遥县", "灵石县"],
						["盐湖区", "河津市", "永济市", "临猗县", "芮城县", "万荣县", "新绛县", "稷山县", "闻喜县", "夏县", "绛县", "平陆县", "垣曲县"],
						["忻府区", "原平市", "定襄县", "五台县", "代县", "繁峙县", "宁武县", "静乐县", "神池县", "五寨县", "岢岚县", "偏关县", "河曲县", "保德县"],
						["尧都区", "侯马市", "霍州市", "曲沃县", "翼城县", "襄汾县", "洪洞县", "古县", "浮山县", "吉县", "乡宁县", "蒲县", "大宁县", "永和县", "汾西县", "隰县", "安泽县"],
						["离石区", "孝义市", "汾阳市", "文水县", "交城县", "兴县", "临县", "柳林县", "岚县", "石楼县", "交口县", "方山县", "中阳县"]
					],
					/*山东省*/
					[
						["历下区", "市中区", "槐荫区", "天桥区", "历城区", "长清区", "章丘市", "平阴县", "济阳县", "商河县"],
						["市南区", "市北区", "李沧区", "城阳区", "崂山区", "黄岛区", "即墨市", "胶州市", "平度市", "莱西市"],
						["张店区", "淄川区", "周村区", "博山区", "临淄区", "桓台县", "高青县", "沂源县"],
						["薛城区", "市中区", "峄城区", "山亭区", "台儿庄区", "滕州市"],
						["东营区", "河口区", "广饶县", "垦利县", "利津县"],
						["莱山区", "芝罘区", "福山区", "牟平区", "龙口市", "莱阳市", "莱州市", "蓬莱市", "招远市", "栖霞市", "海阳市", "长岛县"],
						["奎文区", "潍城区", "寒亭区", "坊子区", "诸城市", "青州市", "寿光市", "安丘市", "昌邑市", "高密市", "临朐县", "昌乐县"],
						["任城区", "兖州区", "邹城市", "曲阜市", "嘉祥县", "汶上县", "梁山县", "微山县", "鱼台县", "金乡县", "泗水县"],
						["泰山区", "岱岳区", "新泰市", "肥城市", "宁阳县", "东平县"],
						["环翠区", "文登区", "荣成市", "乳山市"],
						["东港区", "岚山区", "五莲县", "莒县"],
						["滨城区", "沾化区", "惠民县", "阳信县", "无棣县", "博兴县", "邹平县"],
						["德城区", "陵城区", "乐陵市", "禹城市", "临邑县", "平原县", "夏津县", "武城县", "庆云县", "宁津县", "齐河县"],
						["东昌府区", "聊城开发区", "临清市", "茌平县", "东阿县", "高唐县", "阳谷县", "冠县", "莘县"],
						["兰山区", "河东区", "罗庄区", "兰陵县", "郯城县", "莒南县", "沂水县", "蒙阴县", "平邑县", "沂南县", "临沭县", "费县"],
						["牡丹区", "定陶区", "曹县", "单县", "巨野县", "成武县", "郓城县", "鄄城县", "东明县"],
						["莱城区", "钢城区"]
					],
					/*内蒙古自治区*/
					[
						["回民区", "新城区", "玉泉区", "赛罕区", "土默特左旗", "托克托县", "和林格尔县", "武川县", "清水河县"],
						["昆都仑区", "东河区", "青山区", "石拐区", "九原区", "白云鄂博矿区", "土默特右旗", "固阳县", "达尔罕茂明安联合旗"],
						["海勃湾区", "海南区", "乌达区"],
						["红山区", "元宝山区", "松山区", "阿鲁科尔沁旗", "巴林左旗", "巴林右旗", "林西县", "克什克腾旗", "翁牛特旗", "喀喇沁旗", "宁城县", "敖汉旗"],
						["科尔沁区", "霍林郭勒市", "科尔沁左翼中旗", "科尔沁左翼后旗", "开鲁县", "库伦旗", "奈曼旗", "扎鲁特旗"],
						["东胜区", "达拉特旗", "准格尔旗", "鄂托克前旗", "鄂托克旗", "杭锦旗", "乌审旗", "伊金霍洛旗"],
						["海拉尔区", "满洲里市", "牙克石市", "扎兰屯市", "额尔古纳市", "根河市", "阿荣旗", "鄂伦春自治旗", "莫力达瓦达斡尔族自治旗", "鄂温克族自治旗", "陈巴尔虎旗", "新巴尔虎左旗", "新巴尔虎右旗"],
						["临河区", "五原县", "磴口县", "乌拉特前旗", "乌拉特中旗", "乌拉特后旗", "杭锦后旗"],
						["集宁区", "丰镇市", "卓资县", "化德县", "商都县", "兴和县", "凉城县", "察哈尔右翼前旗", "察哈尔右翼中旗", "察哈尔右翼后旗", "四子王旗"],
						["乌兰浩特市", "阿尔山市", "科尔沁右翼前旗", "科尔沁右翼中旗", "扎赉特旗", "突泉县"],
						["锡林浩特市", "二连浩特市", "阿巴嘎旗", "苏尼特左旗", "苏尼特右旗", "东乌珠穆沁旗", "西乌珠穆沁旗", "太仆寺旗", "镶黄旗", "正镶白旗", "正蓝旗", "多伦县"],
						["阿拉善左旗", "阿拉善右旗", "额济纳旗"]
					],
					/*辽宁省*/
					[
						["和平区", "沈河区", "大东区", "皇姑区", "铁西区", "苏家屯区", "浑南区", "沈北新区", "于洪区", "新民市", "辽中县", "康平县", "法库县"],
						["西岗区", "中山区", "沙河口区", "甘井子区", "旅顺口区", "金州区", "瓦房店市", "普兰店市", "庄河市", "长海县"],
						["铁东区", "铁西区", "立山区", "千山区", "海城市", "台安县", "岫岩满族自治县"],
						["新抚区", "东洲区", "望花区", "顺城区", "抚顺县", "新宾满族自治县", "清原满族自治县"],
						["平山区", "溪湖区", "明山区", "南芬区", "本溪满族自治县", "桓仁满族自治县"],
						["元宝区", "振兴区", "振安区", "东港市", "凤城市", "宽甸满族自治县"],
						["古塔区", "凌河区", "太和区", "松山新区", "凌海市", "北镇市", "黑山县", "义县"],
						["站前区", "西市区", "老边区", "鲅鱼圈区", "大石桥市", "盖州市"],
						["海州区", "太平区", "新邱区", "细河区", "清河门区", "阜新蒙古族自治县", "彰武县"],
						["白塔区", "文圣区", "宏伟区", "弓长岭区", "太子河区", "灯塔市", "辽阳县"],
						["双台子区", "兴隆台区", "盘山县", "大洼县"],
						["银州区", "清河区", "调兵山市", "开原市", "铁岭县", "西丰县"],
						["双塔区", "龙城区", "北票市", "凌源市", "朝阳县", "建平县", "喀喇沁左翼蒙古族自治县"],
						["连山区", "南票区", "龙港区", "兴城市", "建昌县"],
						["昌图县", "绥中县"]
					],
					/*吉林省*/
					[
						["南关区", "朝阳区", "绿园区", "二道区", "双阳区", "宽城区", "九台区", "榆树市", "德惠市", "农安县"],
						["船营区", "龙潭区", "昌邑区", "丰满区", "磐石市", "桦甸市", "蛟河市", "舒兰市", "永吉县"],
						["铁西区", "铁东区", "双辽市", "梨树县", "伊通满族自治县", "辽河农垦管理区"],
						["龙山区", "西安区", "东丰县", "东辽县"],
						["东昌区", "二道江区", "集安市", "通化县", "辉南县", "柳河县"],
						["浑江区", "江源区", "临江市", "抚松县", "靖宇县", "长白朝鲜族自治县"],
						["洮北区", "洮南市", "大安市", "镇赉县", "通榆县"],
						["宁江区", "扶余市", "乾安县", "长岭县", "前郭尔罗斯蒙古族自治县"],
						["延吉市", "图们市", "敦化市", "和龙市", "珲春市", "龙井市", "汪清县", "安图县"],
						["池北区", "池西区", "池南区"],
						["公主岭市", "梅河口市"]
					],
					/*黑龙江省*/
					[
						["道里区", "道外区", "南岗区", "平房区", "松北区", "香坊区", "呼兰区", "阿城区", "双城区", "尚志市", "五常市", "依兰县", "方正县", "宾县", "巴彦县", "木兰县", "通河县", "延寿县"],
						["龙沙区", "建华区", "铁锋区", "昂昂溪区", "富拉尔基区", "碾子山区", "梅里斯达斡尔族区", "讷河市", "龙江县", "依安县", "泰来县", "甘南县", "富裕县", "克山县", "克东县", "拜泉县"],
						["鸡冠区", "恒山区", "滴道区", "梨树区", "城子河区", "麻山区", "虎林市", "密山市", "鸡东县"],
						["向阳区", "工农区", "南山区", "兴安区", "东山区", "兴山区", "萝北县", "绥滨县"],
						["尖山区", "岭东区", "四方台区", "宝山区", "集贤县", "友谊县", "宝清县", "饶河县"],
						["萨尔图区", "龙凤区", "让胡路区", "红岗区", "大同区", "肇州县", "肇源县", "林甸县", "杜尔伯特蒙古族自治县"],
						["伊春区", "南岔区", "友好区", "西林区", "翠峦区", "新青区", "美溪区", "金山屯区", "五营区", "乌马河区", "汤旺河区", "带岭区", "乌伊岭区", "红星区", "上甘岭区", "铁力市", "嘉荫县"],
						["向阳区", "前进区", "东风区", "郊区", "同江市", "富锦市", "桦南县", "桦川县", "汤原县"],
						["新兴区", "桃山区", "茄子河区", "勃利县"],
						["东安区", "阳明区", "爱民区", "西安区", "海林市", "宁安市", "穆棱市", "东宁市", "林口县"],
						["爱辉区", "北安市", "五大连池市", "嫩江县", "逊克县", "孙吴县"],
						["北林区", "安达市", "肇东市", "海伦市", "望奎县", "兰西县", "青冈县", "庆安县", "明水县", "绥棱县"],
						["加格达奇区", "松岭区", "新林区", "呼中区", "呼玛县", "塔河县", "漠河县"],
						["抚远市", "绥芬河市"]
					],
					/*上海市*/
					[
						["黄浦区", "徐汇区", "长宁区", "静安区", "普陀区", "虹口区", "杨浦区", "浦东新区", "闵行区", "宝山区", "嘉定区", "金山区", "松江区", "青浦区", "奉贤区"],
						["崇明县"]
					],
					/*江苏省*/
					[
						["玄武区", "秦淮区", "鼓楼区", "建邺区", "栖霞区", "雨花台区", "江宁区", "浦口区", "六合区", "溧水区", "高淳区"],
						["新吴区", "梁溪区", "锡山区", "惠山区", "滨湖区", "江阴市", "宜兴市"],
						["云龙区", "鼓楼区", "贾汪区", "泉山区", "铜山区", "邳州市", "新沂市", "丰县", "沛县", "睢宁县"],
						["天宁区", "钟楼区", "新北区", "武进区", "金坛区", "溧阳市"],
						["姑苏区", "虎丘区", "吴中区", "相城区", "吴江区", "张家港市", "太仓市", "常熟市"],
						["崇川区", "港闸区", "通州区", "如皋市", "海门市", "启东市", "海安县", "如东县"],
						["连云区", "海州区", "赣榆区", "东海县", "灌云县", "灌南县"],
						["清河区", "清浦区", "淮安区", "淮阴区", "涟水县", "洪泽县", "盱眙县", "金湖县"],
						["亭湖区", "盐都区", "大丰区", "东台市", "响水县", "滨海县", "阜宁县", "射阳县", "建湖县"],
						["广陵区", "邗江区", "江都区", "仪征市", "高邮市", "宝应县"],
						["京口区", "润州区", "丹徒区", "丹阳市", "扬中市", "句容市"],
						["海陵区", "高港区", "姜堰区", "兴化市", "靖江市"],
						["宿城区", "宿豫区", "泗阳县", "泗洪县"],
						["昆山市", "泰兴市", "沭阳县"]
					],
					/*浙江省*/
					[
						["上城区", "下城区", "江干区", "拱墅区", "西湖区", "滨江区", "余杭区", "萧山区", "富阳区", "建德市", "临安市", "桐庐县", "淳安县"],
						["海曙区", "江东区", "江北区", "北仑区", "镇海区", "鄞州区", "余姚市", "慈溪市", "奉化市", "象山县", "宁海县"],
						["鹿城区", "龙湾区", "瓯海区", "洞头区", "瑞安市", "乐清市", "永嘉县", "平阳县", "苍南县", "文成县", "泰顺县"],
						["越城区", "柯桥区", "上虞区", "诸暨市", "嵊州市", "新昌县"],
						["吴兴区", "南浔区", "德清县", "长兴县", "安吉县"],
						["南湖区", "秀洲区", "海宁市", "平湖市", "桐乡市", "嘉善县", "海盐县"],
						["婺城区", "金东区", "兰溪市", "东阳市", "永康市", "武义县", "浦江县", "磐安县"],
						["柯城区", "衢江区", "江山市", "常山县", "开化县", "龙游县"],
						["椒江区", "黄岩区", "路桥区", "临海市", "温岭市", "玉环县", "三门县", "天台县", "仙居县"],
						["莲都区", "龙泉市", "青田县", "缙云县", "遂昌县", "松阳县", "云和县", "庆元县", "景宁畲族自治县"],
						["定海区", "普陀区", "岱山县", "嵊泗县"],
						["义乌市"]
					],
					/*福建省*/
					[
						["鼓楼区", "台江区", "仓山区", "马尾区", "晋安区", "福清市", "长乐市", "平潭县", "闽侯县", "连江县", "罗源县", "闽清县", "永泰县"],
						["思明区", "海沧区", "湖里区", "集美区", "同安区", "翔安区"],
						["城厢区", "涵江区", "荔城区", "秀屿区", "仙游县"],
						["丰泽区", "鲤城区", "洛江区", "泉港区", "石狮市", "晋江市", "南安市", "惠安县", "安溪县", "永春县", "德化县", "金门县"],
						["芗城区", "龙文区", "龙海市", "漳浦县", "南靖县", "云霄县", "平和县", "华安县", "长泰县", "诏安县", "东山县"],
						["新罗区", "永定区", "漳平市", "长汀县", "上杭县", "武平县", "连城县"],
						["梅列区", "三元区", "永安市", "明溪县", "清流县", "宁化县", "大田县", "尤溪县", "沙县", "将乐县", "泰宁县", "建宁县"],
						["延平区", "建阳区", "邵武市", "武夷山市", "建瓯市", "顺昌县", "浦城县", "光泽县", "松溪县", "政和县"],
						["蕉城区", "东侨新区", "福安市", "福鼎市", "霞浦县", "古田县", "屏南县", "寿宁县", "周宁县", "柘荣县"]
					],
					/*江西省*/
					[
						["东湖区", "西湖区", "青云谱区", "湾里区", "青山湖区", "新建区", "南昌县", "安义县", "进贤县"],
						["章贡区", "南康区", "信丰县", "大余县", "赣县", "龙南县", "定南县", "全南县", "寻乌县", "安远县", "宁都县", "于都县", "会昌县", "石城县", "上犹县", "兴国县", "崇义县"],
						["袁州区", "高安市", "樟树市", "奉新县", "万载县", "上高县", "宜丰县", "靖安县", "铜鼓县"],
						["吉州区", "青原区", "井冈山市", "吉安县", "吉水县", "新干县", "永丰县", "泰和县", "遂川县", "万安县", "永新县", "峡江县"],
						["信州区", "广丰区", "德兴市", "上饶县", "玉山县", "铅山县", "横峰县", "弋阳县", "余干县", "万年县", "婺源县"],
						["临川区", "黎川县", "南丰县", "崇仁县", "乐安县", "宜黄县", "金溪县", "资溪县", "东乡县", "广昌县"],
						["浔阳区", "濂溪区", "庐山市", "瑞昌市", "九江县", "永修县", "德安县", "都昌县", "湖口县", "彭泽县", "武宁县", "修水县"],
						["昌江区", "珠山区", "乐平市", "浮梁县"],
						["安源区", "湘东区", "莲花县", "上栗县", "芦溪县"],
						["渝水区", "分宜县"],
						["月湖区", "贵溪市", "余江县"],
						["共青城市", "瑞金市", "丰城市", "鄱阳县", "安福县", "南城县"]
					],
					/*安徽省*/
					[
						["瑶海区", "庐阳区", "蜀山区", "包河区", "巢湖市", "肥东县", "肥西县", "长丰县", "庐江县"],
						["镜湖区", "弋江区", "鸠江区", "三山区", "无为县", "芜湖县", "繁昌县", "南陵县"],
						["龙子湖区", "蚌山区", "禹会区", "淮上区", "五河县", "固镇县", "怀远县"],
						["大通区", "田家庵区", "谢家集区", "八公山区", "潘集区", "凤台县", "寿县"],
						["花山区", "雨山区", "博望区", "含山县", "和县", "当涂县"],
						["相山区", "杜集区", "烈山区", "濉溪县"],
						["铜官区", "义安区", "郊区", "枞阳县"],
						["迎江区", "大观区", "宜秀区", "桐城市", "怀宁县", "潜山县", "太湖县", "望江县", "岳西县"],
						["黄山区", "徽州区", "屯溪区", "歙县", "休宁县", "黟县", "祁门县"],
						["颍州区", "颍泉区", "颍东区", "界首市", "颍上县", "临泉县", "阜南县", "太和县"],
						["埇桥区", "萧县", "砀山县", "灵璧县", "泗县"],
						["琅琊区", "南谯区", "天长市", "明光市", "全椒县", "来安县", "凤阳县", "定远县"],
						["金安区", "裕安区", "叶集区", "霍邱县", "霍山县", "金寨县", "舒城县"],
						["宣州区", "宁国市", "郎溪县", "泾县", "绩溪县", "旌德县"],
						["贵池区", "青阳县", "石台县", "东至县"],
						["谯城区", "蒙城县", "涡阳县", "利辛县"],
						["广德县", "宿松县"]
					],
					/*湖北省*/
					[
						["江岸区", "江汉区", "硚口区", "汉阳区", "武昌区", "青山区", "洪山区", "东西湖区", "黄陂区", "新洲区", "江夏区", "汉南区", "蔡甸区"],
						["黄石港区", "西塞山区", "铁山区", "下陆区", "大冶市", "阳新县"],
						["郧阳区", "茅箭区", "张湾区", "丹江口市", "房县", "竹山县", "竹溪县", "郧西县"],
						["荆州区", "沙市区", "洪湖市", "松滋市", "石首市", "江陵县", "公安县", "监利县"],
						["夷陵区", "猇亭区", "西陵区", "点军区", "伍家岗区", "宜都市", "枝江市", "当阳市", "秭归县", "远安县", "兴山县", "长阳土家族自治县", "五峰土家族自治县"],
						["襄城区", "樊城区", "襄州区", "宜城市", "枣阳市", "老河口市", "保康县", "谷城县", "南漳县"],
						["鄂城区", "华容区", "梁子湖区"],
						["掇刀区", "东宝区", "钟祥市", "京山县", "沙洋县"],
						["黄州区", "武穴市", "麻城市", "红安县", "黄梅县", "罗田县", "蕲春县", "团风县", "浠水县", "英山县"],
						["孝南区", "安陆市", "汉川市", "应城市", "大悟县", "孝昌县", "云梦县"],
						["咸安区", "赤壁市", "崇阳县", "嘉鱼县", "通城县", "通山县"],
						["曾都区", "广水市", "随县"],
						["恩施市", "利川市", "巴东县", "鹤峰县", "建始县", "来凤县", "宣恩县", "咸丰县"],
						["潜江市", "天门市", "仙桃市", "神农架林区"]
					],
					/*湖南省*/
					[
						["芙蓉区", "天心区", "岳麓区", "开福区", "雨花区", "望城区", "长沙县", "宁乡县"],
						["天元区", "荷塘区", "芦淞区", "石峰区", "云龙示范区", "醴陵市", "株洲县", "攸县", "炎陵县"],
						["雨湖区", "岳塘区", "韶山市", "湘潭县"],
						["雁峰区", "石鼓区", "珠晖区", "蒸湘区", "南岳区", "常宁市", "衡阳县", "衡山县", "衡南县", "衡东县", "祁东县"],
						["双清区", "大祥区", "北塔区", "邵阳县", "邵东县", "新邵县", "隆回县", "洞口县", "绥宁县", "武宁县", "城步苗族自治县"],
						["岳阳楼区", "君山区", "云溪区", "汨罗市", "临湘市", "岳阳县", "湘阴县", "华容县"],
						["永定区", "武陵源区", "桑植县"],
						["资阳区", "赫山区", "大通湖区", "沅江市", "桃江县", "南县"],
						["武陵区", "鼎城区", "津市市", "汉寿县", "安乡县", "澧县", "临澧县", "桃源县"],
						["娄星区", "涟源市", "冷水江市", "双峰县"],
						["苏仙区", "北湖区", "资兴市", "安仁县", "桂阳县", "桂东县", "永兴县", "嘉禾县", "临武县", "汝城县"],
						["零陵区", "冷水滩区", "道县", "祁阳县", "东安县", "双牌县", "江永县", "宁远县", "新田县", "江华瑶族自治县", "金洞管理区", "回龙圩管理区"],
						["鹤城区", "洪江市", "洪江管理区", "中方县", "沅陵县", "辰溪县", "会同县", "麻阳苗族自治县", "靖州苗族侗族自治县", "新晃侗族自治县", "芷江侗族自治县", "通道侗族自治县"],
						["吉首市", "泸溪县", "凤凰县", "花垣县", "保靖县", "古丈县", "永顺县", "龙山县"],
						["浏阳市", "湘乡市", "耒阳市", "武冈市", "茶陵县", "平江县", "慈利县", "安化县", "石门县", "新化县", "宜章县", "蓝山县", "溆浦县"]
					],
					/*广东省*/
					[
						["越秀区", "荔湾区", "海珠区", "天河区", "白云区", "黄埔区", "花都区", "番禺区", "南沙区", "增城区", "从化区"],
						["福田区", "罗湖区", "南山区", "盐田区", "宝安区", "龙岗区"],
						["香洲区", "斗门区", "金湾区"],
						["金平区", "龙湖区", "濠江区", "潮南区", "潮阳区", "澄海区", "南澳县"],
						["禅城区", "高明区", "三水区", "南海区", "顺德区"],
						["浈江区", "武江区", "曲江区", "乐昌市", "南雄市", "始兴县", "仁化县", "翁源县", "新丰县", "乳源瑶族自治县"],
						["赤坎区", "霞山区", "坡头区", "麻章区", "廉江市", "吴川市", "雷州市", "遂溪县", "徐闻县"],
						["端州区", "鼎湖区", "高要区", "四会市", "广宁县", "怀集县", "封开县", "德庆县"],
						["江海区", "蓬江区", "新会区", "台山市", "鹤山市", "开平市", "恩平市"],
						["茂南区", "电白区", "高州市", "化州市", "信宜市"],
						["惠城区", "惠阳区", "惠东县", "博罗县", "龙门县"],
						["梅江区", "梅县区", "兴宁市", "大埔县", "丰顺县", "五华县", "平远县", "蕉岭县"],
						["城区", "陆丰市", "海丰县", "陆河县"],
						["源城区", "龙川县", "连平县", "东源县", "和平县", "紫金县"],
						["江城区", "阳东区", "阳春市", "阳西县"],
						["清城区", "清新区", "英德市", "连州市", "佛冈县", "阳山县", "连山壮族瑶族自治县", "连南瑶族自治县"],
						["东莞市"],
						["中山市"],
						["湘桥区", "潮安区", "饶平县"],
						["榕城区", "揭东区", "普宁市", "揭西县", "惠来县"],
						["云城区", "云安区", "罗定市", "新兴县", "郁南县"]
					],
					/*广西壮族自治区*/
					[
						["青秀区", "兴宁区", "西乡塘区", "江南区", "良庆区", "邕宁区", "武鸣区", "隆安县", "马山县", "上林县", "宾阳县", "横县"],
						["柳北区", "柳南区", "城中区", "鱼峰区", "柳城县", "柳江县", "鹿寨县", "融安县", "融水苗族自治县", "三江侗族自治县"],
						["象山区", "秀峰区", "叠彩区", "七星区", "雁山区", "临桂区", "阳朔县", "灵川县", "全州县", "平乐县", "兴安县", "灌阳县", "荔浦县", "资源县", "永福县", "龙胜各族自治县", "恭城瑶族自治县"],
						["长洲区", "万秀区", "龙圩区", "岑溪市", "苍梧县", "蒙山县", "藤县"],
						["海城区", "银海区", "铁山港区", "合浦县"],
						["江州区", "凭祥市", "扶绥县", "宁明县", "龙州县", "大新县", "天等县"],
						["兴宾区", "合山市", "象州县", "武宣县", "忻城县", "金秀瑶族自治县"],
						["八步区", "昭平县", "钟山县", "富川瑶族自治县"],
						["玉州区", "福绵区", "北流市", "容县", "陆川县", "博白县", "兴业县"],
						["右江区", "靖西市", "田阳县", "田东县", "平果县", "德保县", "那坡县", "凌云县", "乐业县", "田林县", "西林县", "隆林各族自治县"],
						["金城江区", "宜州市", "南丹县", "天峨县", "凤山县", "东兰县", "巴马瑶族自治县", "都安瑶族自治县", "大化瑶族自治县", "罗城仫佬族自治县", "环江毛南族自治县"],
						["钦南区", "钦北区", "灵山县", "浦北县"],
						["港口区", "防城区", "东兴市", "上思县"],
						["港北区", "港南区", "覃塘区", "桂平市", "平南县"]
					],
					/*海南省*/
					[
						["秀英区", "龙华区", "琼山区", "美兰区"],
						["崖州区", "天涯区", "吉阳区", "海棠区"],
						["西沙群岛", "中沙群岛", "南沙群岛的岛礁及其海域"],
						["儋州市"],
						["五指山市", "文昌市", "琼海市", "万宁市", "东方市", "定安县", "屯昌县", "澄迈县", "临高县", "白沙黎族自治县", "昌江黎族自治县", "乐东黎族自治县", "陵水黎族自治县", "保亭黎族苗族自治县", "琼中黎族苗族自治县"]
					],
					/*重庆市*/
					[
						["渝北区", "渝中区", "江北区", "江津区", "南岸区", "北碚区", "巴南区", "涪陵区", "綦江区", "大足区", "长寿区", "合川区", "永川区", "南川区", "璧山区", "铜梁区", "潼南区", "荣昌区", "万州区", "黔江区", "大渡口区", "九龙坡区", "沙坪坝区"],
						["巫山县", "巫溪县", "丰都县", "垫江县", "城口县", "云阳县", "奉节县", "武隆县", "梁平县", "开县", "忠县", "石柱土家族自治县", "彭水苗族土家族自治县", "秀山土家族苗族自治县", "酉阳土家族苗族自治县"]
					],
					/*四川省*/
					[
						["锦江区", "青羊区", "金牛区", "武侯区", "成华区", "青白江区", "龙泉驿区", "新都区", "温江区", "双流区", "都江堰市", "彭州市", "崇州市", "邛崃市", "简阳市", "金堂县", "郫县", "大邑县", "蒲江县", "新津县"],
						["涪城区", "游仙区", "安州区", "江油市", "梓潼县", "三台县", "盐亭县", "平武县", "北川羌族自治县"],
						["自流井区", "贡井区", "大安区", "沿滩区", "荣县", "富顺县"],
						["东区", "西区", "仁和区", "米易县", "盐边县"],
						["江阳区", "龙马潭区", "纳溪区", "泸县", "合江县", "叙永县", "古蔺县"],
						["旌阳区", "广汉市", "什邡市", "绵竹市", "中江县", "罗江县"],
						["利州区", "昭化区", "朝天区", "旺苍县", "青川县", "剑阁县", "苍溪县"],
						["船山区", "安居区", "射洪县", "蓬溪县", "大英县"],
						["市中区", "东兴区", "资中县", "隆昌县", "威远县"],
						["市中区", "沙湾区", "五通桥区", "金口河区", "峨眉山市", "犍为县", "井研县", "夹江县", "沐川县", "峨边彝族自治县", "马边彝族自治县"],
						["雁江区", "安岳县", "乐至县"],
						["翠屏区", "南溪区", "宜宾县", "江安县", "长宁县", "高县", "筠连县", "珙县", "兴文县", "屏山县"],
						["顺庆区", "高坪区", "嘉陵区", "阆中市", "西充县", "南部县", "蓬安县", "营山县", "仪陇县"],
						["通川区", "达川区", "万源市", "宣汉县", "开江县", "大竹县", "渠县"],
						["雨城区", "名山区", "荥经县", "汉源县", "石棉县", "天全县", "芦山县", "宝兴县"],
						["广安区", "前锋区", "华蓥市", "邻水县", "武胜县", "岳池县"],
						["巴州区", "恩阳区", "平昌县", "通江县", "南江县"],
						["东坡区", "彭山区", "仁寿县", "丹棱县", "青神县", "洪雅县"],
						["马尔康市", "金川县", "小金县", "阿坝县", "若尔盖县", "红原县", "壤塘县", "汶川县", "理县", "茂县", "松潘县", "九寨沟县", "黑水县"],
						["康定市", "泸定县", "丹巴县", "九龙县", "雅江县", "道孚县", "炉霍县", "甘孜县", "新龙县", "德格县", "白玉县", "石渠县", "色达县", "理塘县", "巴塘县", "乡城县", "稻城县", "得荣县"],
						["西昌市", "德昌县", "会理县", "会东县", "宁南县", "普格县", "布拖县", "昭觉县", "金阳县", "雷波县", "美姑县", "甘洛县", "越西县", "喜德县", "冕宁县", "盐源县", "木里藏族自治县"]
					],
					/*贵州省*/
					[
						["南明区", "云岩区", "花溪区", "乌当区", "白云区", "观山湖区", "清镇市", "开阳县", "息烽县", "修文县"],
						["钟山区", "六枝特区", "水城县", "盘县"],
						["红花岗区", "汇川区", "播州区", "桐梓县", "绥阳县", "正安县", "凤冈县", "湄潭县", "余庆县", "习水县", "道真仡佬族苗族自治县", "务川仡佬族苗族自治县"],
						["西秀区", "平坝区", "普定县", "镇宁布依族苗族自治县", "关岭布依族苗族自治县", "紫云苗族布依族自治县"],
						["七星关区", "大方县", "黔西县", "金沙县", "织金县", "纳雍县", "赫章县"],
						["碧江区", "万山区", "江口县", "石阡县", "思南县", "德江县", "玉屏县", "松桃县", "沿河县", "印江县"],
						["兴义市", "兴仁县", "普安县", "晴隆县", "贞丰县", "望谟县", "册亨县", "安龙县"],
						["凯里市", "黄平县", "施秉县", "三穗县", "岑巩县", "天柱县", "锦屏县", "剑河县", "台江县", "榕江县", "从江县", "雷山县", "麻江县", "丹寨县"],
						["都匀市", "福泉市", "荔波县", "贵定县", "瓮安县", "独山县", "平塘县", "罗甸县", "长顺县", "龙里县", "惠水县", "三都水族自治县"],
						["仁怀市", "赤水市", "福泉市", "镇远县", "黎平县", "威宁彝族回族苗族自治县"]
					],
					/*云南省*/
					[
						["呈贡区", "盘龙区", "五华区", "官渡区", "西山区", "东川区", "安宁市", "晋宁县", "富民县", "宜良县", "嵩明县", "石林彝族自治县", "禄劝彝族苗族自治县", "寻甸回族彝族自治县"],
						["昭阳区", "鲁甸县", "巧家县", "盐津县", "大关县", "永善县", "绥江县", "镇雄县", "彝良县", "威信县", "水富县"],
						["麒麟区", "沾益区", "宣威市", "马龙县", "富源县", "罗平县", "师宗县", "陆良县", "会泽县"],
						["红塔区", "江川区", "澄江县", "通海县", "华宁县", "易门县", "峨山彝族自治县", "新平彝族傣族自治县", "元江哈尼族彝族傣族自治县"],
						["思茅区", "宁洱哈尼族彝族自治县", "墨江哈尼族自治县", "景东彝族自治县", "景谷傣族彝族自治县", "镇沅彝族哈尼族拉祜族自治县", "江城哈尼族彝族自治县", "孟连傣族拉祜族佤族自治县", "澜沧拉祜族自治县", "西盟佤族自治县"],
						["隆阳区", "腾冲市", "施甸县", "龙陵县", "昌宁县"],
						["古城区", "永胜县", "华坪县", "玉龙纳西族自治县", "宁蒗彝族自治县"],
						["临翔区", "凤庆县", "云县", "永德县", "镇康县", "双江拉祜族佤族布朗族傣族自治县", "耿马傣族佤族自治县", "沧源佤族自治县"],
						["楚雄市", "双柏县", "牟定县", "南华县", "姚安县", "大姚县", "永仁县", "元谋县", "武定县", "禄丰县"],
						["蒙自市", "个旧市", "开远市", "弥勒市", "建水县", "石屏县", "泸西县", "绿春县", "元阳县", "红河县", "金平苗族瑶族傣族自治县", "河口瑶族自治县", "屏边苗族自治县"],
						["文山市", "砚山县", "西畴县", "麻栗坡县", "马关县", "丘北县", "广南县", "富宁县"],
						["景洪市", "勐海县", "勐腊县"],
						["大理市", "祥云县", "宾川县", "弥渡县", "永平县", "云龙县", "洱源县", "剑川县", "鹤庆县", "漾濞彝族自治县", "南涧彝族自治县", "巍山彝族回族自治县"],
						["芒市", "瑞丽市", "梁河县", "盈江县", "陇川县"],
						["泸水县", "福贡县", "贡山独龙族怒族自治县", "兰坪白族普米族自治县"],
						["香格里拉市", "德钦县", "维西傈僳族自治县"]
					],
					/*西藏自治区*/
					[
						["城关区", "堆龙德庆区", "林周县", "达孜县", "尼木县", "当雄县", "曲水县", "墨竹工卡县"],
						["卡若区", "察雅县", "左贡县", "芒康县", "洛隆县", "边坝县", "江达县", "贡觉县", "丁青县", "八宿县", "类乌齐县"],
						["桑珠孜区", "南木林县", "江孜县", "定日县", "萨迦县", "拉孜县", "昂仁县", "谢通门县", "白朗县", "仁布县", "康马县", "定结县", "仲巴县", "亚东县", "吉隆县", "聂拉木县", "萨嘎县", "岗巴县"],
						["巴宜区", "工布江达县", "波密县", "朗县", "米林县", "察隅县", "墨脱县"],
						["乃东区", "扎囊县", "贡嘎县", "桑日县", "琼结县", "洛扎县", "加查县", "隆子县", "曲松县", "措美县", "错那县", "浪卡子县"],
						["那曲县", "申扎县", "班戈县", "聂荣县", "安多县", "嘉黎县", "巴青县", "比如县", "索县", "尼玛县"],
						["噶尔县", "普兰县", "札达县", "日土县", "革吉县", "改则县", "措勤县"]
					],
					/*陕西省*/
					[
						["新城区", "碑林区", "莲湖区", "灞桥区", "未央区", "雁塔区", "阎良区", "临潼区", "长安区", "高陵区", "蓝田县", "周至县", "户县"],
						["耀州区", "王益区", "印台区", "宜君县"],
						["渭滨区", "金台区", "陈仓区", "凤翔县", "岐山县", "扶风县", "眉县", "陇县", "千阳县", "麟游县", "凤县", "太白县"],
						["秦都区", "渭城区", "兴平市", "三原县", "泾阳县", "武功县", "乾县", "礼泉县", "永寿县", "彬县", "长武县", "旬邑县", "淳化县"],
						["临渭区", "华州区", "华阴市", "蒲城县", "富平县", "潼关县", "大荔县", "合阳县", "澄城县", "白水县"],
						["汉台区", "南郑县", "城固县", "洋县", "西乡县", "勉县", "宁强县", "略阳县", "镇巴县", "留坝县", "佛坪县"],
						["汉滨区", "旬阳县", "石泉县", "平利县", "汉阴县", "宁陕县", "紫阳县", "岚皋县", "镇坪县", "白河县"],
						["商州区", "洛南县", "丹凤县", "商南县", "山阳县", "镇安县", "柞水县"],
						["宝塔区", "延长县", "延川县", "子长县", "安塞县", "志丹县", "吴起县", "甘泉县", "富县", "洛川县", "宜川县", "黄龙县", "黄陵县"],
						["榆阳区", "横山区", "靖边县", "定边县", "绥德县", "米脂县", "佳县", "吴堡县", "清涧县", "子洲县"],
						["杨陵区"],
						["韩城市", "神木县", "府谷县"]
					],
					/*甘肃省*/
					[
						["城关区", "七里河区", "西固区", "安宁区", "红古区", "榆中县", "皋兰县", "永登县"],
						["嘉峪关市"],
						["金川区", "永昌县"],
						["白银区", "平川区", "会宁县", "靖远县", "景泰县"],
						["秦州区", "麦积区", "清水县", "秦安县", "甘谷县", "武山县", "张家川回族自治县"],
						["肃州区", "玉门市", "敦煌市", "瓜州县", "金塔县", "肃北蒙古族自治县", "阿克塞哈萨克族自治县"],
						["甘州区", "山丹县", "民乐县", "临泽县", "高台县", "肃南裕固族自治县"],
						["凉州区", "古浪县", "民勤县", "天祝藏族自治县"],
						["安定区", "通渭县", "陇西县", "漳县", "渭源县", "岷县", "临洮县"],
						["武都区", "宕昌县", "两当县", "徽县", "成县", "西和县", "礼县", "康县", "文县"],
						["崆峒区", "泾川县", "灵台县", "崇信县", "华亭县", "庄浪县", "静宁县"],
						["西峰区", "正宁县", "华池县", "合水县", "宁县", "庆城县", "镇原县", "环县"],
						["临夏市", "临夏县", "康乐县", "广河县", "永靖县", "和政县", "东乡族自治县", "积石山保安族东乡族撒拉族自治县"],
						["合作市", "夏河县", "玛曲县", "舟曲县", "碌曲县", "迭部县", "临潭县", "卓尼县"]
					],
					/*青海省*/
					[
						["城中区", "城东区", "城西区", "城北区", "湟中县", "湟源县", "大通回族土族自治县"],
						["乐都区", "平安区", "民和回族土族自治县", "互助土族自治县", "化隆回族自治县", "循化撒拉族自治县"],
						["海晏县", "祁连县", "刚察县", "门源回族自治县"],
						["共和县", "同德县", "贵德县", "贵南县", "兴海县"],
						["德令哈市", "格尔木市", "天峻县", "都兰县", "乌兰县"],
						["同仁县", "尖扎县", "泽库县", "河南蒙古族自治县"],
						["玛沁县", "班玛县", "甘德县", "达日县", "久治县", "玛多县"],
						["玉树市", "杂多县", "称多县", "治多县", "囊谦县", "曲麻莱县"]
					],
					/*宁夏回族自治区*/
					[
						["兴庆区", "金凤区", "西夏区", "灵武市", "永宁县", "贺兰县"],
						["大武口区", "惠农区", "平罗县"],
						["利通区", "红寺堡区", "青铜峡市"],
						["原州区", "西吉县", "隆德县", "泾源县", "彭阳县"],
						["沙坡头区", "中宁县", "海原县"],
						["同心县", "盐池县"]
					],
					/*新疆维吾尔自治区*/
					[
						["天山区", "沙依巴克区", "新市区", "水磨沟区", "头屯河区", "达坂城区", "米东区", "乌鲁木齐县"],
						["独山子区", "克拉玛依区", "白碱滩区", "乌尔禾区"],
						["高昌区", "鄯善县", "托克逊县"],
						["伊州区", "伊吾县", "巴里坤哈萨克自治县"],
						["阿克苏市", "温宿县", "库车县", "沙雅县", "新和县", "拜城县", "乌什县", "阿瓦提县", "柯坪县"],
						["喀什市", "疏附县", "疏勒县", "英吉沙县", "泽普县", "莎车县", "叶城县", "麦盖提县", "岳普湖县", "伽师县", "巴楚县", "塔什库尔干塔吉克自治县"],
						["和田市", "和田县", "墨玉县", "皮山县", "洛浦县", "策勒县", "于田县", "民丰县"],
						["伊宁市", "奎屯市", "霍尔果斯市", "尼勒克县", "伊宁县", "霍城县", "巩留县", "新源县", "昭苏县", "特克斯县", "察布查尔锡伯自治县", "塔城地区", "阿勒泰地区"],
						["昌吉市", "阜康市", "呼图壁县", "玛纳斯县", "奇台县", "吉木萨尔县", "木垒哈萨克自治县"],
						["博乐市", "阿拉山口市", "精河县", "温泉县"],
						["库尔勒市", "轮台县", "尉犁县", "若羌县", "且末县", "和静县", "和硕县", "博湖县", "焉耆回族自治县"],
						["阿图什市", "阿克陶县", "阿合奇县", "乌恰县"],
						["石河子市", "阿拉尔市", "图木舒克市", "五家渠市", "北屯市", "铁门关市", "双河市", "可克达拉市", "昆玉市"]
					],
					/*台湾省*/
					[
						["中正区", "大同区", "中山区", "松山区", "大安区", "万华区", "信义区", "士林区", "北投区", "内湖区", "南港区", "文山区"],
						["板桥区", "汐止区", "新店区", "永和区", "中和区", "土城区", "树林区", "三重区", "新庄区", "芦洲区", "瑞芳区", "三峡区", "莺歌区", "淡水区", "万里区", "金山区", "深坑区", "石碇区", "平溪区", "双溪区", "贡寮区", "坪林区", "乌来区", "泰山区", "林口区", "五股区", "八里区", "三芝区", "石门区"],
						["桃园区", "中坜区", "平镇区", "八德区", "杨梅区", "芦竹区", "大溪区", "龙潭区", "龟山区", "大园区", "观音区", "新屋区", "复兴区"],
						["中区", "东区", "南区", "西区", "北区", "北屯区", "西屯区", "南屯区", "太平区", "大里区", "雾峰区", "乌日区", "丰原区", "后里区", "潭子区", "大雅区", "神冈区", "石冈区", "东势区", "新社区", "和平区", "大肚区", "沙鹿区", "龙井区", "梧栖区", "清水区", "大甲区", "外埔区", "大安区"],
						["中西区", "东区", "南区", "北区", "安平区", "安南区", "永康区", "归仁区", "新化区", "左镇区", "玉井区", "楠西区", "南化区", "仁德区", "关庙区", "龙崎区", "官田区", "麻豆区", "佳里区", "西港区", "七股区", "将军区", "学甲区", "北门区", "新营区", "后壁区", "白河区", "东山区", "六甲区", "下营区", "柳营区", "盐水区", "善化区", "大内区", "山上区", "新市区", "安定区"],
						["楠梓区", "左营区", "鼓山区", "三民区", "盐埕区", "前金区", "新兴区", "苓雅区", "前镇区", "旗津区", "小港区", "凤山区", "大寮区", "鸟松区", "林园区", "仁武区", "大树区", "大社区", "冈山区", "路竹区", "桥头区", "梓官区", "弥陀区", "永安区", "燕巢区", "阿莲区", "茄萣区", "湖内区", "旗山区", "美浓区", "内门区", "杉林区", "甲仙区", "六龟区", "茂林区", "桃源区", "那玛夏区"],
						["中正区", "中山区", "七堵区", "暖暖区", "仁爱区", "安乐区", "信义区"],
						["东区", "北区", "香山区"],
						["东区", "西区"],
						["新竹县", "苗栗县", "彰化县", "南投县", "云林县", "嘉义县", "屏东县", "宜兰县", "花莲县", "台东县", "澎湖县", "金门县", "连江县"],
						["钓鱼岛", "南小岛", "北小岛", "赤尾屿", "黄尾屿", "北屿", "南屿", "飞濑岛"]
					],
					/*香港特别行政区*/
					[
						["中西区", "湾仔区", "东区", "南区"],
						["油尖旺区", "深水埗区", "九龙城区", "黄大仙区", "观塘区"],
						["北区", "大埔区", "沙田区", "西贡区", "荃湾区", "屯门区", "元朗区", "葵青区", "离岛区"]
					],
					/*澳门特别行政区*/
					[
						["花地玛堂区", "圣安多尼堂区", "大堂区", "望德堂区", "风顺堂区"],
						["嘉模堂区", "圣方济各堂区"],
						["路氹城"]
					]
				];
var expressArea, areaCont, areaList = $("#areaList")
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

	/*选择区县*/
	selectD:function(p,c,d) {
		var areaKey=$('.area-address-name').data('key');
		clockArea();
		expressArea += district[p][c][d];
		$("#expressArea .area_address").html(expressArea);
		if(!areaObject.provinceCityD.length){
			
			for(var i=0;i<arguments.length;i++){
				areaObject.provinceCityD+=arguments[i]+',';
			}
			$('.area-address-name').val(expressArea).data('key',areaObject.provinceCityD);
			// return false;
		}
		areaObject.provinceCityD=[];
		for(var i=0;i<arguments.length;i++){
			areaObject.provinceCityD+=arguments[i]+',';
		}
		$('.area-address-name').val(expressArea).data('key',areaObject.provinceCityD);
		return expressArea;
		
	},
	setArea:function(optionArr){
		var provinceName=areaObject.selectP(optionArr[0]);
		var cityName=areaObject.selectC(optionArr[0],optionArr[1]);
		var areaName=areaObject.selectD(optionArr[0],optionArr[1],optionArr[2]);
		return areaName;
	},
	getArea:function(){
		//return areaObject.provinceCityD.split(',');
		return areaObject.provinceCityD.substr(0, areaObject.provinceCityD.length - 1).split(',')
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
	$('body').on('click','#expressArea',function(){
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

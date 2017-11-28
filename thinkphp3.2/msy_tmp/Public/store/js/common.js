/**
 * Created by wendell on 2015/9/2.
 */

//Jquery date rangepicker 属性
var dateRangePickerConfig = {
    separator: '/',
    autoClose: true,
    shortcuts: {
        'prev-days': [1, 7, 30],
        'next-days': null,
        'prev': ['week', 'month', 'year'],
        'next': null
    },
    customShortcuts: [
        //if return an array of two dates, it will select the date range between the two dates
        {
            name: '&nbsp;本周',
            dates: function () {
                var start = moment().day(0).toDate();
                var end = moment().day(6).toDate();
                return [start, end];
            }
        },
        {
            name: '&nbsp;本月',
            dates: function () {
                var start = moment().startOf('month').toDate();
                var end = moment().endOf('month').toDate();
                return [start, end];
            }
        },
        {
            name: '&nbsp;本年',
            dates: function () {
                var start = moment().startOf('year').toDate();
                var end = moment().endOf('year').toDate();
                return [start, end];
            }
        }
    ]

};

/**
 * Ajax 加载页面
 * @param url
 * @param selector
 * @private
 */
function load_to_selector(url, selector, callback) {
    //var indexLoad = layer.load(0, {shade: [0.2, '#000']});
    var indexLoad = layer.load(0, {time: 3000});
    $(selector).empty().load(url, function (responseTxt, statusTxt, xhr) {
        layer.close(indexLoad);
        if (statusTxt == "error") {
            if (xhr.status == '999') {
                layer.open({
                    title: '登录超时',
                    type: 2,
                    area: ['450px', '440px'],
                    skin: 'layui-layer-rim', //加上边框
                    content: [APP + '/Home/Login/frame', 'no']
                });
            } else {
                layer.msg("页面加载失败，请刷新重试！<br />Error: " + xhr.status + ": " + xhr.statusText, {time: 3000});
            }
        } else {
            callback && callback();
        }
    });
}


/**
 * 验证是否手机号码
 * @param inputString
 * @returns {boolean}
 */
function isMobilePhone(inputString) {
    var reg = /^((13\d|14[57]|15[^4,\D]|17[678]|18\d)\d{8}|170[059]\d{7})$/;
    return reg.test(inputString);
}

/**
 * 密码长度检测及字符
 */
function checkValidPasswd(str) {
    var reg = /^[x00-x7f]+$/;
    if (!reg.test(str)) {
        return false;
    }
    if (str.length < 6 || str.length > 16) {
        return false;
    }
    return true;
}

/**
 * 数字或者字母
 * @param str
 * @param len 长度
 */
function isNumber(str,startLen,endLen){
	var reg =  /^[0-9a-zA-Z]+$/;
    if (!reg.test(str)) {
        return false;
    }
    if (str.length < (startLen ? startLen : 1) || str.length > (endLen ? endLen : 10) ) {
        return false;
    }
    return true;
}

/**
 * 验证邮箱
 * @param str
 * @returns {boolean}
 */
function isEmail(str) {
    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return reg.test(str);
}

/**
 * 验证固定电话
 * @param str
 * @returns {boolean}
 */
function isPhone(str) {
     // var reg = /^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
     var reg = /^(0\d{2,3})(\d{7,8})(-(\d{3,}))?$/;
    return reg.test(str);
}

/**
 * QQ号的验证
 * @param qq
 * @returns {boolean}
 */
function isQQ(qq) {
    var bValidate = RegExp(/^[1-9][0-9]{4,9}$/).test(qq);
    if (bValidate) return true;
    else return false;
}

/**
 * 正整数 (大于0的整数)
 * @returns {boolean}
 */
function isPosIntNumber(number) {
    //var g = /^[1-9]*[1-9][0-9]*$/;
    var reg = /^[1-9]\d*$/;
    return reg.test(number);
}

/**
 * 正数 (大于0的数,可以是小数)
 * @returns {boolean}
 */
function isPosNumber(number) {
    var reg = /^\d+(?=\.{0,1}\d+$|$)/;
    return reg.test(number);
}

/**
 * 自然数(非负整数)
 * @returns {boolean}
 */
function isNaturalNumber(number) {
    //var reg = /^([1-9]\d*|[0]{1,1})$/;
    var reg = /^\d+$/;
    return reg.test(number);
}

/**
 * 验证金额
 */
function isMoney(money) {
    var g = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
    return g.test(money);
}

//数字转为两位小数
function changeTwoDecimal(x) {
    var f_x = parseFloat(x);
    if (isNaN(f_x)) {
        return 0;
    }
    var f_x = Math.round(x * 100) / 100;
    var s_x = f_x.toString();
    var pos_decimal = s_x.indexOf('.');
    if (pos_decimal < 0) {
        pos_decimal = s_x.length;
        s_x += '.';
    }
    while (s_x.length <= pos_decimal + 2) {
        s_x += '0';
    }
    return s_x;
}

/**
 * 和PHP一样的时间戳格式化函数
 * @param  {string} format    格式
 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
 * @return {string}           格式化的时间字符串
 */
function date(format, timestamp) {
    var a, jsdate = ((timestamp) ? new Date(timestamp * 1000) : new Date());
    var pad = function (n, c) {
        if ((n = n + "").length < c) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var txt_ordin = {1: "st", 2: "nd", 3: "rd", 21: "st", 22: "nd", 23: "rd", 31: "st"};
    var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var f = {
        // Day
        d: function () {
            return pad(f.j(), 2)
        },
        D: function () {
            return f.l().substr(0, 3)
        },
        j: function () {
            return jsdate.getDate()
        },
        l: function () {
            return txt_weekdays[f.w()]
        },
        N: function () {
            return f.w() + 1
        },
        S: function () {
            return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'
        },
        w: function () {
            return jsdate.getDay()
        },
        z: function () {
            return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0
        },
        // Week
        W: function () {
            var a = f.z(), b = 364 + f.L() - a;
            var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
            if (b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b) {
                return 1;
            } else {
                if (a <= 2 && nd >= 4 && a >= (6 - nd)) {
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W", Math.round(nd2.getTime() / 1000));
                } else {
                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                }
            }
        },
        // Month
        F: function () {
            return txt_months[f.n()]
        },
        m: function () {
            return pad(f.n(), 2)
        },
        M: function () {
            return f.F().substr(0, 3)
        },
        n: function () {
            return jsdate.getMonth() + 1
        },
        t: function () {
            var n;
            if ((n = jsdate.getMonth() + 1) == 2) {
                return 28 + f.L();
            } else {
                if (n & 1 && n < 8 || !(n & 1) && n > 7) {
                    return 31;
                } else {
                    return 30;
                }
            }
        },
        // Year
        L: function () {
            var y = f.Y();
            return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0
        },
        //o not supported yet
        Y: function () {
            return jsdate.getFullYear()
        },
        y: function () {
            return (jsdate.getFullYear() + "").slice(2)
        },
        // Time
        a: function () {
            return jsdate.getHours() > 11 ? "pm" : "am"
        },
        A: function () {
            return f.a().toUpperCase()
        },
        B: function () {
            // peter paul koch:
            var off = (jsdate.getTimezoneOffset() + 60) * 60;
            var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
            var beat = Math.floor(theSeconds / 86.4);
            if (beat > 1000) beat -= 1000;
            if (beat < 0) beat += 1000;
            if ((String(beat)).length == 1) beat = "00" + beat;
            if ((String(beat)).length == 2) beat = "0" + beat;
            return beat;
        },
        g: function () {
            return jsdate.getHours() % 12 || 12
        },
        G: function () {
            return jsdate.getHours()
        },
        h: function () {
            return pad(f.g(), 2)
        },
        H: function () {
            return pad(jsdate.getHours(), 2)
        },
        i: function () {
            return pad(jsdate.getMinutes(), 2)
        },
        s: function () {
            return pad(jsdate.getSeconds(), 2)
        },
        //u not supported yet
        // Timezone
        //e not supported yet
        //I not supported yet
        O: function () {
            var t = pad(Math.abs(jsdate.getTimezoneOffset() / 60 * 100), 4);
            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
            return t;
        },
        P: function () {
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2))
        },
        //T not supported yet
        //Z not supported yet
        // Full Date/Time
        c: function () {
            return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()
        },
        //r not supported yet
        U: function () {
            return Math.round(jsdate.getTime() / 1000)
        }
    };
    return format.replace(/[\\]?([a-zA-Z])/g, function (t, s) {
        if (t != s) {
            // escaped
            ret = s;
        } else if (f[s]) {
            // a date function exists
            ret = f[s]();
        } else {
            // nothing special
            ret = s;
        }
        return ret;
    });
}

/**
 * 检测值
 * @param elementId
 * @param error
 * @returns {boolean}
 */
function checkInput(elementId, error) {
    if ($.trim($(elementId).val()) == '') {
        if (!error && $(elementId).is('[error]')) {
            error = $(elementId).attr('error');
        }
        if (error) layer.tips(error, elementId, {tips: 3});
        $(elementId).focus();
        return false;
    }
    return true;
}

/**
 * 将已序列化的表单数据转为Json对象
 * @param str
 * @returns {Object|*}
 */
function serializeToJson(str) {
    var serializeObj = {};
    $.each(str, function () {
        serializeObj[this.name] = this.value;
    });
    return serializeObj;
}

/**
 * 关闭frame弹窗
 */
function closeFrameWindow(all) {
    if (all) {
        parent.layer.closeAll('iframe');
    } else {
        var index_frame_window = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index_frame_window);
    }
}

function myParseFloat(val) {
    return val ? parseFloat(val) : 0;
}

/**
 * 数字月份转大写
 * @param mouth
 * @returns {*}
 */
function upperMonth(mouth) {
    var map = {
        1: '一月',
        2: '二月',
        3: '三月',
        4: '四月',
        5: '五月',
        6: '六月',
        7: '七月',
        8: '八月',
        9: '九月',
        10: '十月',
        11: '十一月',
        12: '十二月'
    };
    return map[mouth];
}

/**
 * 大写月份转数字
 * @param mouth
 * @returns {*}
 */
function lowerMonth(mouth) {
    var map = {
        '一月': 1,
        '二月': 2,
        '三月': 3,
        '四月': 4,
        '五月': 5,
        '六月': 6,
        '七月': 7,
        '八月': 8,
        '九月': 9,
        '十月': 10,
        '十一月': 11,
        '十二月': 12
    };
    return map[mouth];
}

/**
 * 数组元素转为数值
 * @param data
 * @returns {*}
 */
function mapToNumber(array_list){
    return array_list.map(
        function(item){
            return Number(item);
        });
}

/**清空表单
 * $obj jquery 对象
 */
function clearForm($obj){
	$obj
		.find(':input')  
		.not(':button, :submit, :reset')  
	 	.val('')  
	 	.removeAttr('checked')  
	 	.removeAttr('selected')
	 	.removeAttr('disabled');  
}

function create_code(callback){
    var code = Math.floor(Math.random() * 999999999999);
    callback(code);
}

//注册验证
var register={
    phoneCheck:function(phoneStr){
        var patrn = /^((?:13|15|18|14|17)\d{9}|0(?:10|2\d|[3-9]\d{2})[1-9]\d{6,7})$/;
        if(phoneStr!='' && patrn.test(phoneStr)){
            return true;
        }else{
            return false;
        }
    },
    vfyCheck:function(vfyStr){
        var vfy=/^\d{6}$/;
        if(vfyStr !='' && vfy.test(vfyStr)){
            return true;
        }else{
            return false;
        }
    },
    pswCheck:function(pswStr){
        var pswReg = /^[A-Za-z0-9]{6,16}$/;
        if(pswStr !='' && pswReg.test(pswStr)){
            return true;
        }else{
            return false;
        }
    }
}
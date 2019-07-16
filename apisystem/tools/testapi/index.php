<!doctype html>
<html lang="en">
<head>
    <title>在线HTTP GET/POST模拟请求测试工具 - Apisystem.cn</title>
    <meta name="keywords" content="HTTP接口测试,GET/POST模拟请求测试工具,APISystem文档管理系统,APISystem接口管理系统,Apisystem,接口管理系统,Texren,www.apisystem.cn" />
    <meta name="description" content="APISystem文档管理系统是一个开源API接口文档管理系统， ApiSystem将原来用word编写API文档流程中解放出来，只需要按照填写文本框即可生成接口文档，管理文档也很轻松，同时还可以配置可见及所得的调试工具，API接口也可以一键导出word文档让你既可以在线分权限分享也可线下分享，是中小企业IT团队开发的福音。" />
    <meta name="generator" content="www.ApiSystem.cn" />
    <meta name="author" content="Texren  QQ:174463651 Smith77 QQ:3246932472" />
    <meta name="copyright" content="ApiSystem文档管理系统 © 2014-2017 ApiSystem.cn V2.3.0118" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="bookmark" href="/favicon.ico" />

    <link rel="stylesheet" href="images/style.min.css" />
    <link rel="stylesheet" href="images/style.new.css" />


    <style type="text/css">
        thead {background-color: #f3f3f3;}
        .params_value, .headers_value {width: 75%}
        button.tiny{padding: 2px 2px;margin-bottom:5px;}
        .content-table td{word-wrap:break-word;white-space:pre-wrap;word-break: break-all;padding:3px 10px;}
        .content-table tr {line-height: 17px;}
        #params_table, #headers_table {margin-bottom: 15px;}
        #params_table thead tr, #headers_table thead tr, #response_table thead tr {line-height: 12px;}



        body > .navbar {
            padding: 5px;
            font-size: 13px;
            background-color: #1b1b1b;
        }
        .navbar-fixed-top {
            top: 0;
        }
        .navbar-fixed-top, .navbar-fixed-bottom {
            position: fixed;
            right: 0;
            left: 0;
            z-index: 1030;
            margin-bottom: 0;
        }
        .navbar {
            *position: relative;
            *z-index: 2;
            margin-bottom: 20px;
            overflow: visible;
        }
        .navbar-inverse .navbar-inner {
            background-color: #1b1b1b;
            background-image: -moz-linear-gradient(top, #222222, #111111);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#222222), to(#111111));
            background-image: -webkit-linear-gradient(top, #222222, #111111);
            background-image: -o-linear-gradient(top, #222222, #111111);
            background-image: linear-gradient(to bottom, #222222, #111111);
            background-repeat: repeat-x;
            border-color: #252525;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff222222', endColorstr='#ff111111', GradientType=0);
        }
        .navbar-fixed-top .navbar-inner, .navbar-static-top .navbar-inner {
            -webkit-box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
            -moz-box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-fixed-top .navbar-inner, .navbar-fixed-bottom .navbar-inner {
            padding-right: 0;
            padding-left: 0;
            -webkit-border-radius: 0;
            -moz-border-radius: 0;
            border-radius: 0;
        }
        .navbar-fixed-top .navbar-inner, .navbar-static-top .navbar-inner {
            border-width: 0 0 1px;
        }

        .navbar .brand {
            font-size: 20px;
            font-weight: 200;
            color: #777777;
            text-shadow: 0 1px 0 #ffffff;
        }
        .brand {
            float:left;
        }
        .brand img{
            max-width: 100%;
            vertical-align: middle;
            border: 0;
        }

        .nav-collapse.collapse {
            float: left;
            height: auto !important;
            overflow: visible !important;
        }
        .nav-collapse.collapse {
            height: auto;
            overflow: visible;
        }
        .nav li {
            font-size: 13px;
            line-height: 20px;
        }
        .nav li a{
            font-size: 13px;
            color: #FFF;
        }

    </style>
    <link rel="stylesheet" type="text/css" href="images/JSONFormatter.css" />
    <link rel="stylesheet" type="text/css" href="images/jquery.dialogbox.css" />
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="/index.php?s=/Home/index/index.html" title="ApiSystem文档管理系统"><img src="/Public/Docapi/images/logo-index.png"
                                                                                                     style="height: 25px;margin-top: 0"></a>

            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li>
                        <a href="/index.php?s=/docapi/index.html" target="_self">API文档管理系统</a>
                    </li>
                    <li>
                        <a href="/index.php?s=/Home/article/index/category/help.html" target="_self">开发帮助</a>
                    </li>
                    <li>
                        <a href="/index.php?s=/docapi/index.html" target="_self">API文档管理</a>
                    </li>
                    <li>
                        <a href="/index.php?s=/Home/index/toolsTestApi.html" target="_self">POST & GET测试工具</a>
                    </li>
                    <li>
                        <a href="http://bbs.apisystem.cn/" target="_self">交流论坛</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
<div class='container'>
    <h3 class='explainer'>在线HTTP POST/GET接口测试工具 -  Apisystem.cn</h3>



    <form style="border: thin solid #A4AA04;padding:0 20px;">
        <div class='row' id="params_start">
            <div class="span one xs-three">
                <select id="api_method" class="select">
                    <option value="GET">GET</option>
                    <option value="POST" selected="">POST</option>
                </select>
            </div>
            <div class='span five xs-six strong'>
                <input type="text" class="input-text" id="http_url_input" title="HTTP接口URL" alt="HTTP接口URL" value="" placeholder ="http://" />
            </div>
            <button class='button success small' id="http_test" type="button">发送请求</button>

            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="body_params" onclick="params_togg();" checked="checked">请求Body参数
            <input type="checkbox" name="body_headers" onclick="headers_togg();" checked="checked">请求Header
        </div>
        <table id="params_table" class="table table-bordered">
            <thead>
            <tr>
                <th width="35%">Body参数名称</th>
                <th>Body参数值</th>
            </tr>
            </thead>
            <tbody>
            <tr id="params_end">
                <td colspan="2">
                    <button class='button primary small' id="add_url_parameter" type="button">添加参数</button>
                    <button class='button success small' id="add_raw_url_parameter" type="button">RAW批量添加</button>
                </td>
            </tr>
            </tbody>
        </table>
        <table id="headers_table" class="table table-bordered">
            <thead>
            <tr>
                <th width="35%">Header名称</th>
                <th>Header值</th>
            </tr>
            </thead>
            <tbody>
            <tr id="headers_end">
                <td colspan="2">
                    <button class='button primary small' id="add_api_headers" type="button">添加Header</button>
                </td>
            </tr>
            </tbody>
        </table>
        <table id="response_table" class="table table-bordered">
            <thead>
            <tr>
                <th colspan="2">Response Header Body</th>

            </tr>

            </thead>
            <tbody>
            <tr>

                <td valign="top" colspan="2">
                <span valign="top" id="response_header" style="display:block;word-break: break-all;word-wrap: break-word;"></span>
                    <textarea id="output"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
<!--    <script src="//cdn.bootcss.com/jquery/1.9.1/jquery.min.js"></script>-->
    <script src="/Public/Docapi/js/jquery-1.11.1.min.js"></script>
    <script src="images/jquery.cookie.min.js" type="text/javascript"></script>
    <script src="images/JSONFormatter.js" type="text/javascript"></script>
    <script type="text/javascript" src="images/jquery.dialogbox.js"></script>
    <script>
        function params_togg() {
            if ($("input[name=body_params]:checked") && $("input[name=body_params]:checked").length > 0) {
                $("#params_table").show();
                $.cookie("body_on", "on")
            } else {
                $("#params_table").hide();
                $.cookie("body_on", "")
            }
        }

        function headers_togg() {
            if ($("input[name=body_headers]:checked").length && $("input[name=body_headers]:checked").length > 0) {
                $("#headers_table").show();
                $.cookie("header_on", "on")
            } else {
                $("#headers_table").hide();
                $.cookie("header_on", "")
            }
        }
        var result_text = "";


        function add_parameter(cnt, name, value) {

            if (name == "undefined" || name == null) {
                name = ""
            }
            if (value == "undefined" || value == null) {
                value = ""
            }
            $("#params_end").before('						<tr class="params_p" cnt="' + cnt + '">							<td>								<input class="params_name input-text" type="text" name="p_name_' + cnt + '" title="参数名称" alt="参数名称" value="' + name + '" maxlength="100"/>							</td>							<td>								<input class="params_value input-text" type="text" name="p_value_' + cnt + '" title="参数数值" alt="参数数值" value="' + value + '" maxlength="5000"/>								<button class="button danger tiny" onclick="javascript:del_param(this);" type="button">删除参数</button>							</td>						</tr>					')
        }

        function add_header(cnt, name, value) {
            if (name == "undefined" || name == null) {
                name = ""
            }
            if (value == "undefined" || value == null) {
                value = ""
            }
            $("#headers_end").before('						<tr class="headers_p" cnt="' + cnt + '">							<td>								<input class="headers_name input-text" type="text" name="h_name_' + cnt + '" title="header名称" alt="header名称" value="' + name + '" maxlength="100"/>							</td>							<td>								<input class="headers_value input-text" type="text" name="h_value_' + cnt + '" title="header数值" alt="header数值" value="' + value + '" maxlength="5000"/>								<button class="button danger tiny" onclick="javascript:del_param(this);" type="button">删除参数</button>							</td>						</tr>					')
        }

        function get_all_parameter() {
            var params = new Array();
            $(".params_p").each(function() {
                var cnt = $(this).attr("cnt");
                var name = $(this).find("input[name=p_name_" + cnt + "]").val();
                var value = $(this).find("input[name=p_value_" + cnt + "]").val();
                if (is_empty(name) || is_empty(value)) {
                    return false
                }
                params.push({
                    name: name,
                    value: value
                })
            });
            return params
        }

        function get_all_header() {
            var headers = new Array();
            $(".headers_p").each(function() {
                var cnt = $(this).attr("cnt");
                var name = $(this).find("input[name=h_name_" + cnt + "]").val();
                var value = $(this).find("input[name=h_value_" + cnt + "]").val();
                if (is_empty(name) || is_empty(value)) {
                    return false
                }
                headers.push({
                    name: name,
                    value: value
                })
            });
            return headers
        }
        var param_cnt = 0;
        $("#add_url_parameter").click(function() {
            var input_len = $("#params_table input").size();
            param_cnt++;
            add_parameter(param_cnt, "", "")
        });
        $('#add_raw_url_parameter').click(function() {
            $('body').dialogbox({type:"text",title:"批量添加Body参数",message:"输入Raw参数，例如：id=123&sale=yes&deleted=0"}, function($btn, $ans) {
                if($btn == "close") {
                    return;
                }
                else if($btn == "ok") {
                    var raw = $ans;
                    if (raw == null || raw == "") {
                        return;
                    }
                    var params = str_2_params(raw);
                    if (params) {
                        for (var i = 0; i < params.length; i++) {
                            param_cnt++;
                            add_parameter(param_cnt, params[i]['name'], params[i]['value']);
                        };
                    }
                }
            });
        });

        var header_cnt = 0;
        $("#add_api_headers").click(function() {
            var input_len = $("#headers_table input").size();
            header_cnt++;
            add_header(header_cnt, "", "")
        });

        function params_2_str(params) {
            var str = "";
            for (var i = 0; i < params.length; i++) {
                var p = params[i];
                if (p.name) {
                    str = str + "&" + p.name + "=" + p.value
                }
            }
            if (str.length > 1) {
                str = str.substring(1)
            }
            return str
        }

        function str_2_params(str) {
            try {
                var params = new Array();
                var ps = str.split("&");
                var pv = null;
                for (var i = 0; i < ps.length; i++) {
                    pv = ps[i].split("=");
                    params.push({
                        name: pv[0],
                        value: pv[1],
                        method: "post"
                    })
                }
                return params
            } catch (e) {
                return false
            }
        }


        $("#http_test").click(function() {
            var http_url = $("#http_url_input").val();
            var method = $("#api_method").val();
            var params = get_all_parameter();
            var headers = get_all_header();

            if (is_empty(http_url)) {
                alert("接口链接为空，请填写后再请求！");
                return
            }
            if (params === false) {
                alert("参数填写为空，请填写完整之后再试！");
                return
            }
            if (headers === false) {
                alert("Header填写为空，请填写完整之后再试！");
                return
            }
            $("#response_header").html("执行中，请等待...");
            $("#output").val("");
            $.post("postget.php", {
                url: http_url,
                params: params,
                method: method,
                headers: headers
            }, function(data) {
                if (data.success == 1) {
                    result_text = data.message;
                    if (typeof(result_text) == "object") {
                        result_text = JSON.stringify(result_text);
                    }
                    var json = null;
                    try {
                        json = JSON.parse(result_text)
                    } catch (e) {
                        json = null
                    }

                    //json = null; //add by hustcc
                    if (json == null) {
                        // $("#output").append("<pre></pre>");
                        $("#output").val(result_text);
                       // alert(1);
                    } else {
                        //$("#output").val(result_text);
                        //alert(json);
                        json = data;
                        JSONFormatter.format(json, {
                            appendTo: "#output",
                            collapse: true,
                            list_id: "json"
                        })
                    }
                    $("#response_header").html("执行时间：" + data.time);
                    $("#response_header").append("<pre></pre>");
                    $("#response_header pre").text(data.header)
                } else {
                    // $("#output").append("<pre></pre>");
                    $("#output").val(data.message);
                    $("#response_header").html("<span class='red'>执行失败</span>");
                }
            }, "json")
        });

        function del_param(obj) {

            $(obj).parent().parent().remove()
        }

        function is_empty(str) {
            if (str == null || str == "" || str == "undefined") {
                return true
            }
            return false
        };
    </script>

    <div class='divider'></div>
    <h4 class='explainer'>Requests Header | Http Header</h4>
    <table class="table table-bordered content-table">
        <thead>
        <tr>
            <th>Header</th>
            <th width="35%">解释</th>
            <th width="40%">示例</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Accept</td>
            <td>指定客户端能够接收的内容类型</td>
            <td>Accept: text/plain, text/html</td>
        </tr>
        <tr>
            <td>Accept-Charset</td>
            <td>浏览器可以接受的字符编码集。</td>
            <td>Accept-Charset: iso-8859-5</td>
        </tr>
        <tr>
            <td>Accept-Encoding</td>
            <td>指定浏览器可以支持的web服务器返回内容压缩编码类型。</td>
            <td>Accept-Encoding: compress, gzip</td>
        </tr>
        <tr>
            <td>Accept-Language</td>
            <td>浏览器可接受的语言</td>
            <td>Accept-Language: en,zh</td>
        </tr>
        <tr>
            <td>Accept-Ranges</td>
            <td>可以请求网页实体的一个或者多个子范围字段</td>
            <td>Accept-Ranges: bytes</td>
        </tr>
        <tr>
            <td>Authorization</td>
            <td>HTTP授权的授权证书</td>
            <td>Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==</td>
        </tr>
        <tr>
            <td>Cache-Control</td>
            <td>指定请求和响应遵循的缓存机制</td>
            <td>Cache-Control: no-cache</td>
        </tr>
        <tr>
            <td>Connection</td>
            <td>表示是否需要持久连接。（HTTP 1.1默认进行持久连接）</td>
            <td>Connection: close</td>
        </tr>
        <tr>
            <td>Cookie</td>
            <td>HTTP请求发送时，会把保存在该请求域名下的所有cookie值一起发送给web服务器。</td>
            <td>Cookie: $Version=1; Skin=new;</td>
        </tr>
        <tr>
            <td>Content-Length</td>
            <td>请求的内容长度</td>
            <td>Content-Length: 348</td>
        </tr>
        <tr>
            <td>Content-Type</td>
            <td>请求的与实体对应的MIME信息</td>
            <td>Content-Type: application/x-www-form-urlencoded</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>请求发送的日期和时间</td>
            <td>Date: Tue, 15 Nov&nbsp;2010 08:12:31 GMT</td>
        </tr>
        <tr>
            <td>Expect</td>
            <td>请求的特定的服务器行为</td>
            <td>Expect: 100-continue</td>
        </tr>
        <tr>
            <td>From</td>
            <td>发出请求的用户的Email</td>
            <td>From: user@email.com</td>
        </tr>
        <tr>
            <td>Host</td>
            <td>指定请求的服务器的域名和端口号</td>
            <td>Host: www.zcmhi.com</td>
        </tr>
        <tr>
            <td>If-Match</td>
            <td>只有请求内容与实体相匹配才有效</td>
            <td>If-Match: “737060cd8c284d8af7ad3082f209582d”</td>
        </tr>
        <tr>
            <td>If-Modified-Since</td>
            <td>如果请求的部分在指定时间之后被修改则请求成功，未被修改则返回304代码</td>
            <td>If-Modified-Since: Sat, 29 Oct 2010 19:43:31 GMT</td>
        </tr>
        <tr>
            <td>If-None-Match</td>
            <td>如果内容未改变返回304代码，参数为服务器先前发送的Etag，与服务器回应的Etag比较判断是否改变</td>
            <td>If-None-Match: “737060cd8c284d8af7ad3082f209582d”</td>
        </tr>
        <tr>
            <td>If-Range</td>
            <td>如果实体未改变，服务器发送客户端丢失的部分，否则发送整个实体。参数也为Etag</td>
            <td>If-Range: “737060cd8c284d8af7ad3082f209582d”</td>
        </tr>
        <tr>
            <td>If-Unmodified-Since</td>
            <td>只在实体在指定时间之后未被修改才请求成功</td>
            <td>If-Unmodified-Since: Sat, 29 Oct 2010 19:43:31 GMT</td>
        </tr>
        <tr>
            <td>Max-Forwards</td>
            <td>限制信息通过代理和网关传送的时间</td>
            <td>Max-Forwards: 10</td>
        </tr>
        <tr>
            <td>Pragma</td>
            <td>用来包含实现特定的指令</td>
            <td>Pragma: no-cache</td>
        </tr>
        <tr>
            <td>Proxy-Authorization</td>
            <td>连接到代理的授权证书</td>
            <td>Proxy-Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==</td>
        </tr>
        <tr>
            <td>Range</td>
            <td>只请求实体的一部分，指定范围</td>
            <td>Range: bytes=500-999</td>
        </tr>
        <tr>
            <td>Referer</td>
            <td>先前网页的地址，当前请求网页紧随其后,即来路</td>
            <td>Referer: http://www.zcmhi.com/archives/71.html</td>
        </tr>
        <tr>
            <td>TE</td>
            <td>客户端愿意接受的传输编码，并通知服务器接受接受尾加头信息</td>
            <td>TE: trailers,deflate;q=0.5</td>
        </tr>
        <tr>
            <td>Upgrade</td>
            <td>向服务器指定某种传输协议以便服务器进行转换（如果支持）</td>
            <td>Upgrade: HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11</td>
        </tr>
        <tr>
            <td>User-Agent</td>
            <td>User-Agent的内容包含发出请求的用户信息</td>
            <td>User-Agent: Mozilla/5.0 (Linux; X11)</td>
        </tr>
        <tr>
            <td>Via</td>
            <td>通知中间网关或代理服务器地址，通信协议</td>
            <td>Via: 1.0 fred, 1.1 nowhere.com (Apache/1.1)</td>
        </tr>
        <tr>
            <td>Warning</td>
            <td>关于消息实体的警告信息</td>
            <td>Warn: 199 Miscellaneous warning</td>
        </tr>
        </tbody>
    </table>
    <div class='divider'></div>
    <h4 class='explainer'>Responses 部分 | Http Header</h4>
    <table class="table table-bordered content-table">
        <thead>
        <tr>
            <th>Header</th>
            <th width="35%">解释</th>
            <th width="40%">示例</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Accept-Ranges</td>
            <td>表明服务器是否支持指定范围请求及哪种类型的分段请求</td>
            <td>Accept-Ranges: bytes</td>
        </tr>
        <tr>
            <td>Age</td>
            <td>从原始服务器到代理缓存形成的估算时间（以秒计，非负）</td>
            <td>Age: 12</td>
        </tr>
        <tr>
            <td>Allow</td>
            <td>对某网络资源的有效的请求行为，不允许则返回405</td>
            <td>Allow: GET, HEAD</td>
        </tr>
        <tr>
            <td>Cache-Control</td>
            <td>告诉所有的缓存机制是否可以缓存及哪种类型</td>
            <td>Cache-Control: no-cache</td>
        </tr>
        <tr>
            <td>Content-Encoding</td>
            <td>web服务器支持的返回内容压缩编码类型。</td>
            <td>Content-Encoding: gzip</td>
        </tr>
        <tr>
            <td>Content-Language</td>
            <td>响应体的语言</td>
            <td>Content-Language: en,zh</td>
        </tr>
        <tr>
            <td>Content-Length</td>
            <td>响应体的长度</td>
            <td>Content-Length: 348</td>
        </tr>
        <tr>
            <td>Content-Location</td>
            <td>请求资源可替代的备用的另一地址</td>
            <td>Content-Location: /index.htm</td>
        </tr>
        <tr>
            <td>Content-MD5</td>
            <td>返回资源的MD5校验值</td>
            <td>Content-MD5: dxfdr4dyaXR5IQ==</td>
        </tr>
        <tr>
            <td>Content-Range</td>
            <td>在整个返回体中本部分的字节位置</td>
            <td>Content-Range: bytes 21010-47021/47022</td>
        </tr>
        <tr>
            <td>Content-Type</td>
            <td>返回内容的MIME类型</td>
            <td>Content-Type: text/html; charset=utf-8</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>原始服务器消息发出的时间</td>
            <td>Date: Tue, 15 Nov 2010 08:12:31 GMT</td>
        </tr>
        <tr>
            <td>ETag</td>
            <td>请求变量的实体标签的当前值</td>
            <td>ETag: “rt4rdr5wrvfd4dzd”</td>
        </tr>
        <tr>
            <td>Expires</td>
            <td>响应过期的日期和时间</td>
            <td>Expires: Thu, 01 Dec 2010 16:00:00 GMT</td>
        </tr>
        <tr>
            <td>Last-Modified</td>
            <td>请求资源的最后修改时间</td>
            <td>Last-Modified: Tue, 15 Nov 2010 12:45:26 GMT</td>
        </tr>
        <tr>
            <td>Location</td>
            <td>用来重定向接收方到非请求URL的位置来完成请求或标识新的资源</td>
            <td>Location: http://www.xxx</td>
        </tr>
        <tr>
            <td>Pragma</td>
            <td>包括实现特定的指令，它可应用到响应链上的任何接收方</td>
            <td>Pragma: no-cache</td>
        </tr>
        <tr>
            <td>Proxy-Authenticate</td>
            <td>它指出认证方案和可应用到代理的该URL上的参数</td>
            <td>Proxy-Authenticate: Basic</td>
        </tr>
        <tr>
            <td>refresh</td>
            <td>应用于重定向或一个新的资源被创造，在5秒之后重定向（由网景提出，被大部分浏览器支持）</td>
            <td>
                Refresh: 5; url=http://www.apisystem.cn
            </td>
        </tr>
        <tr>
            <td>Retry-After</td>
            <td>如果实体暂时不可取，通知客户端在指定时间之后再次尝试</td>
            <td>Retry-After: 120</td>
        </tr>
        <tr>
            <td>Server</td>
            <td>web服务器软件名称</td>
            <td>Server: Apache/1.3.27 (Unix) (Red-Hat/Linux)</td>
        </tr>
        <tr>
            <td>Set-Cookie</td>
            <td>设置Http Cookie</td>
            <td>Set-Cookie: UserID=JohnDoe; Max-Age=3600; Version=1</td>
        </tr>
        <tr>
            <td>Trailer</td>
            <td>指出头域在分块传输编码的尾部存在</td>
            <td>Trailer: Max-Forwards</td>
        </tr>
        <tr>
            <td>Transfer-Encoding</td>
            <td>文件传输编码</td>
            <td><span style="font-family: monospace;"><span style="font-family: Georgia,'Times New Roman','Bitstream Charter',Times,serif;">Transfer-Encoding:chunked</span></span>
            </td>
        </tr>
        <tr>
            <td>Vary</td>
            <td>告诉下游代理是使用缓存响应还是从原始服务器请求</td>
            <td>Vary: *</td>
        </tr>
        <tr>
            <td>Via</td>
            <td>告知代理客户端响应是通过哪里发送的</td>
            <td>Via: 1.0 fred, 1.1 nowhere.com (Apache/1.1)</td>
        </tr>
        <tr>
            <td>Warning</td>
            <td>警告实体可能存在的问题</td>
            <td>Warning: 199 Miscellaneous warning</td>
        </tr>
        <tr>
            <td>WWW-Authenticate</td>
            <td>表明客户端请求实体应该使用的授权方案</td>
            <td>WWW-Authenticate: Basic</td>
        </tr>
        </tbody>
    </table>
    <div class='divider'></div>
    <h4 class='explainer'>在线接口测试工具 | Introduce</h4>
    <ul class="new_tools_list">
        <li>接口测试是测试系统组件间接口的一种测试。接口测试主要用于检测外部系统与系统之间以及内部各个子系统之间的交互点。测试的重点是要检查数据的交换，传递和控制管理过程，以及系统间的相互逻辑依赖关系等。</li>
        <li>接口测试一般以用于多系统间交互开发，或者拥有多个子系统的应用系统开发的测试。接口测试适用于为其他系统提供服务的底层框架系统和中心服务系统，主要测试这些系统对外部提供的接口，验证其正确性和稳定性。</li>
        <li>最简单的应用就是，使用Web http的方式，为APP提供数据接口，这些接口具有一定的动态性，采用传入一定的参数，接口通过参数获取不同的数据返回给使用者，参数传入的方式有GET和POST方式两种，使用浏览器可以直接模拟GET请求，但是POST请求却无能为力，只能编写脚本测试，这就导致接口测试非常麻烦。</li>
        <li>本工具提供任意接口的HTTP GET和POST测试，并且提供测试返回值，接口返回时间，并且已经对接口请求的异常状态进行获取，然后反馈给用户。</li>
        <li>备注：接口执行时间与本网站服务器有关，仅供参考。</li>
    </ul>
    <div class='divider'></div>

    <div class="row footer">
        <strong>Copyright</strong> <a target="_bank" href="http://www.apisystem.cn/">ApiSystem文档管理系统</a> &copy; 2014-2017 ApiSystem.cn V2.3.0118

    </div>


</body>
</html>

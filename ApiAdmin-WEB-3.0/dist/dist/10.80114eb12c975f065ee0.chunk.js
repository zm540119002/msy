webpackJsonp([10],{237:function(t,e,a){"use strict";function o(t){c||a(841)}Object.defineProperty(e,"__esModule",{value:!0});var n=a(460),r=a.n(n);for(var s in n)"default"!==s&&function(t){a.d(e,t,function(){return n[t]})}(s);var i=a(843),l=(a.n(i),a(4)),c=!1,d=o,m=Object(l.a)(r.a,i.render,i.staticRenderFns,!1,d,"data-v-2aab74d6",null);m.options.__file="src\\views\\interface\\list.vue",e.default=m.exports},460:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=a(33),n=function(t){return t&&t.__esModule?t:{default:t}}(o),r=function(t,e,a,o){return e("Button",{props:{type:"primary"},style:{margin:"0 5px"},on:{click:function(){t.formItem.id=a.id,t.formItem.apiClass=a.apiClass,t.formItem.info=a.info,t.formItem.method=a.method,t.formItem.hash=a.hash,t.formItem.groupHash=a.groupHash,t.formItem.accessToken=a.accessToken,t.formItem.isTest=a.isTest,t.formItem.needLogin=a.needLogin,t.modalSetting.show=!0,t.modalSetting.index=o,t.getInterfaceGroups(!1)}}},"编辑")},s=function(t,e,a,o){return e("Poptip",{props:{confirm:!0,title:"您确定要删除这条数据吗? ",transfer:!0},on:{"on-ok":function(){n.default.get("InterfaceList/del",{params:{hash:a.hash}}).then(function(e){a.loading=!1,1===e.data.code?(t.tableData.splice(o,1),t.$Message.success(e.data.msg)):t.$Message.error(e.data.msg)})}}},[e("Button",{style:{margin:"0 5px"},props:{type:"error",placement:"top",loading:a.isDeleting}},"删除")])},i=function(t,e,a,o){return e("Button",{style:{margin:"0 5px"},props:{type:"info",placement:"top",loading:a.isDeleting},on:{click:function(){var e=a.hash;t.$router.push({name:"interface_request",params:{hash:e}})}}},"请求参数")},l=function(t,e,a,o){return e("Button",{style:{margin:"0 5px"},props:{type:"warning",placement:"top",loading:a.isDeleting},on:{click:function(){var e=a.hash;t.$router.push({name:"interface_response",params:{hash:e}})}}},"返回参数")};e.default={name:"interface_list",data:function(){return{confirmRefresh:!1,columnsList:[{title:"序号",type:"index",width:65,align:"center"},{title:"接口名称",align:"center",key:"info"},{title:"真实类库",align:"center",key:"apiClass",width:190},{title:"接口映射",align:"center",key:"hash",width:130},{title:"请求方式",align:"center",key:"method",width:100},{title:"接口状态",align:"center",key:"status",width:90},{title:"操作",align:"center",key:"handle",width:355,handle:["edit","delete"]}],tableData:[],apiGroup:[],tableShow:{currentPage:1,pageSize:10,listCount:0},searchConf:{type:"",keywords:"",status:""},modalSetting:{show:!1,loading:!1,index:0},formItem:{apiClass:"",info:"",groupHash:"default",method:2,hash:"",accessToken:1,isTest:0,needLogin:0,id:0},ruleValidate:{apiClass:[{required:!0,message:"真实类库不能为空",trigger:"blur"}],info:[{required:!0,message:"接口名称不能为空",trigger:"blur"}]}}},created:function(){this.init(),this.getList()},methods:{init:function(){var t=this;this.columnsList.forEach(function(e){e.handle&&(e.render=function(e,a){var o=t.tableData[a.index];return e("div",[r(t,e,o,a.index),i(t,e,o,a.index),l(t,e,o,a.index),s(t,e,o,a.index)])}),"method"===e.key&&(e.render=function(e,a){var o=t.tableData[a.index];if(1===o.isTest)return e("Tag",{attrs:{color:"error"}},"测试");switch(o.method){case 1:return e("Tag",{attrs:{color:"green"}},"POST");case 2:return e("Tag",{attrs:{color:"red"}},"GET");case 0:return e("Tag",{attrs:{color:"success"}},"不限")}}),"status"===e.key&&(e.render=function(e,a){var o=t.tableData[a.index];return e("i-switch",{attrs:{size:"large"},props:{"true-value":1,"false-value":0,value:o.status},on:{"on-change":function(e){n.default.get("InterfaceList/changeStatus",{params:{status:e,hash:o.hash}}).then(function(e){var a=e.data;1===a.code?t.$Message.success(a.msg):-14===a.code?(t.$store.commit("logout",t),t.$router.push({name:"login"})):(t.$Message.error(a.msg),t.getList()),t.cancel()})}}},[e("span",{slot:"open"},"启用"),e("span",{slot:"close"},"禁用")])})})},alertAdd:function(){var t=this;t.getInterfaceGroups(!1),n.default.get("InterfaceList/getHash").then(function(e){var a=e.data;1===a.code?t.formItem.hash=a.data.hash:-14===a.code?(t.$store.commit("logout",t),t.$router.push({name:"login"})):t.$Message.error(a.msg)}),this.modalSetting.show=!0},submit:function(){var t=this,e=this;this.$refs.myForm.validate(function(a){if(a){e.modalSetting.loading=!0;var o="";o=0===t.formItem.id?"InterfaceList/add":"InterfaceList/edit",n.default.post(o,e.formItem).then(function(t){e.modalSetting.loading=!1,1===t.data.code?(e.$Message.success(t.data.msg),e.getList(),e.cancel()):e.$Message.error(t.data.msg)})}})},cancel:function(){this.modalSetting.show=!1},changePage:function(t){this.tableShow.currentPage=t,this.getList()},changeSize:function(t){this.tableShow.pageSize=t,this.getList()},search:function(){this.tableShow.currentPage=1,this.getList()},getList:function(){var t=this;n.default.get("InterfaceList/index",{params:{page:t.tableShow.currentPage,size:t.tableShow.pageSize,type:t.searchConf.type,keywords:t.searchConf.keywords,status:t.searchConf.status}}).then(function(e){var a=e.data;1===a.code?(t.tableData=a.data.list,t.tableShow.listCount=a.data.count):-14===a.code?(t.$store.commit("logout",t),t.$router.push({name:"login"})):t.$Message.error(a.msg)})},doCancel:function(t){t||(this.formItem.id=0,this.$refs.myForm.resetFields(),this.modalSetting.loading=!1,this.modalSetting.index=0)},refreshRoute:function(){var t=this;n.default.get("InterfaceList/refresh").then(function(e){var a=e.data;1===a.code?t.$Message.success(a.msg):-14===a.code?(t.$store.commit("logout",t),t.$router.push({name:"login"})):t.$Message.error(a.msg),t.confirmRefresh=!1})},getInterfaceGroups:function(t){var e=this;(t||null===e.apiGroup||0===e.apiGroup.length)&&n.default.get("InterfaceGroup/getAll").then(function(t){var a=t.data;1===a.code?e.apiGroup=a.data.list:-14===a.code?(e.$store.commit("logout",e),e.$router.push({name:"login"})):e.$Message.error(a.msg)})}}}},841:function(t,e,a){var o=a(842);"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);var n=a(15).default;n("2f4fef3e",o,!1,{})},842:function(t,e,a){e=t.exports=a(14)(!1),e.push([t.i,"\n.api-box[data-v-2aab74d6] {\n  max-height: 300px;\n  overflow: auto;\n  border: 1px solid #dddee1;\n  border-radius: 5px;\n  padding: 10px;\n}\n.demo-upload-list[data-v-2aab74d6] {\n  display: inline-block;\n  width: 60px;\n  height: 60px;\n  text-align: center;\n  line-height: 60px;\n  border: 1px solid transparent;\n  border-radius: 4px;\n  overflow: hidden;\n  background: #fff;\n  position: relative;\n  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);\n  margin-right: 4px;\n}\n.demo-upload-list img[data-v-2aab74d6] {\n  width: 100%;\n  height: 100%;\n}\n.demo-upload-list-cover[data-v-2aab74d6] {\n  display: none;\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background: rgba(0, 0, 0, 0.6);\n}\n.demo-upload-list:hover .demo-upload-list-cover[data-v-2aab74d6] {\n  display: block;\n}\n.demo-upload-list-cover i[data-v-2aab74d6] {\n  color: #fff;\n  font-size: 20px;\n  cursor: pointer;\n  margin: 0 2px;\n}\n",""])},843:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("Row",[a("Col",{attrs:{span:"24"}},[a("Card",{staticStyle:{"margin-bottom":"10px"}},[a("Form",{attrs:{inline:""}},[a("FormItem",{staticStyle:{"margin-bottom":"0"}},[a("Select",{staticStyle:{width:"100px"},attrs:{clearable:"",placeholder:"请选择状态"},model:{value:t.searchConf.status,callback:function(e){t.$set(t.searchConf,"status",e)},expression:"searchConf.status"}},[a("Option",{attrs:{value:1}},[t._v("启用")]),t._v(" "),a("Option",{attrs:{value:0}},[t._v("禁用")])],1)],1),t._v(" "),a("FormItem",{staticStyle:{"margin-bottom":"0"}},[a("Select",{staticStyle:{width:"100px"},attrs:{clearable:"",placeholder:"请选择类别"},model:{value:t.searchConf.type,callback:function(e){t.$set(t.searchConf,"type",e)},expression:"searchConf.type"}},[a("Option",{attrs:{value:1}},[t._v("接口标识")]),t._v(" "),a("Option",{attrs:{value:2}},[t._v("接口名称")]),t._v(" "),a("Option",{attrs:{value:3}},[t._v("真实类库")])],1)],1),t._v(" "),a("FormItem",{staticStyle:{"margin-bottom":"0"}},[a("Input",{attrs:{placeholder:""},model:{value:t.searchConf.keywords,callback:function(e){t.$set(t.searchConf,"keywords",e)},expression:"searchConf.keywords"}})],1),t._v(" "),a("FormItem",{staticStyle:{"margin-bottom":"0"}},[a("Button",{attrs:{type:"primary"},on:{click:t.search}},[t._v("查询/刷新")])],1)],1)],1)],1)],1),t._v(" "),a("Row",[a("Col",{attrs:{span:"24"}},[a("Card",[a("p",{staticStyle:{height:"32px"},attrs:{slot:"title"},slot:"title"},[a("Button",{attrs:{type:"primary",icon:"md-add"},on:{click:t.alertAdd}},[t._v("新增")]),t._v(" "),a("Button",{attrs:{type:"warning",icon:"md-refresh"},on:{click:function(e){t.confirmRefresh=!0}}},[t._v("刷新路由")])],1),t._v(" "),a("div",[a("Table",{attrs:{columns:t.columnsList,data:t.tableData,border:"","disabled-hover":""}})],1),t._v(" "),a("div",{staticClass:"margin-top-15",staticStyle:{"text-align":"center"}},[a("Page",{attrs:{total:t.tableShow.listCount,current:t.tableShow.currentPage,"page-size":t.tableShow.pageSize,"show-elevator":"","show-sizer":"","show-total":""},on:{"on-change":t.changePage,"on-page-size-change":t.changeSize}})],1)])],1)],1),t._v(" "),a("Modal",{attrs:{width:"668",styles:{top:"30px"}},on:{"on-visible-change":t.doCancel},model:{value:t.modalSetting.show,callback:function(e){t.$set(t.modalSetting,"show",e)},expression:"modalSetting.show"}},[a("p",{staticStyle:{color:"#2d8cf0"},attrs:{slot:"header"},slot:"header"},[a("Icon",{attrs:{type:"md-information-circle"}}),t._v(" "),a("span",[t._v(t._s(t.formItem.id?"编辑":"新增")+"接口")])],1),t._v(" "),a("Form",{ref:"myForm",attrs:{rules:t.ruleValidate,model:t.formItem,"label-width":80}},[a("FormItem",{attrs:{label:"接口名称",prop:"info"}},[a("Input",{attrs:{placeholder:"请输入接口名称"},model:{value:t.formItem.info,callback:function(e){t.$set(t.formItem,"info",e)},expression:"formItem.info"}})],1),t._v(" "),a("FormItem",{attrs:{label:"真实类库",prop:"apiClass"}},[a("Input",{attrs:{placeholder:"请输入真实类库"},model:{value:t.formItem.apiClass,callback:function(e){t.$set(t.formItem,"apiClass",e)},expression:"formItem.apiClass"}})],1),t._v(" "),a("FormItem",{attrs:{label:"接口分组",prop:"groupHash"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.groupHash,callback:function(e){t.$set(t.formItem,"groupHash",e)},expression:"formItem.groupHash"}},t._l(t.apiGroup,function(e,o){return a("Option",{key:e.hash,attrs:{value:e.hash}},[t._v(" "+t._s(e.name)+" ")])}),1),t._v(" "),a("Button",{attrs:{icon:"md-refresh"},on:{click:function(e){return t.getInterfaceGroups(!0)}}})],1),t._v(" "),a("FormItem",{attrs:{label:"请求方式",prop:"method"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.method,callback:function(e){t.$set(t.formItem,"method",e)},expression:"formItem.method"}},[a("Option",{key:0,attrs:{value:0}},[t._v(" 不限 ")]),t._v(" "),a("Option",{key:1,attrs:{value:1}},[t._v(" POST ")]),t._v(" "),a("Option",{key:2,attrs:{value:2}},[t._v(" GET ")])],1)],1),t._v(" "),a("FormItem",{attrs:{label:"接口映射",prop:"hash"}},[a("Input",{staticStyle:{width:"300px"},attrs:{disabled:""},model:{value:t.formItem.hash,callback:function(e){t.$set(t.formItem,"hash",e)},expression:"formItem.hash"}}),t._v(" "),a("Tag",{staticStyle:{"margin-left":"5px"},attrs:{color:"error"}},[t._v("系统自动生成，不允许修改")])],1),t._v(" "),a("FormItem",{attrs:{label:"AccessToken",prop:"accessToken"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.accessToken,callback:function(e){t.$set(t.formItem,"accessToken",e)},expression:"formItem.accessToken"}},[a("Option",{key:0,attrs:{value:0}},[t._v(" 忽略Token ")]),t._v(" "),a("Option",{key:1,attrs:{value:1}},[t._v(" 验证Token ")])],1)],1),t._v(" "),a("FormItem",{attrs:{label:"用户登录",prop:"needLogin"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.needLogin,callback:function(e){t.$set(t.formItem,"needLogin",e)},expression:"formItem.needLogin"}},[a("Option",{key:0,attrs:{value:0}},[t._v(" 忽略登录 ")]),t._v(" "),a("Option",{key:1,attrs:{value:1}},[t._v(" 验证登录 ")])],1)],1),t._v(" "),a("FormItem",{attrs:{label:"测试模式",prop:"isTest"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.isTest,callback:function(e){t.$set(t.formItem,"isTest",e)},expression:"formItem.isTest"}},[a("Option",{key:0,attrs:{value:0}},[t._v(" 生产模式 ")]),t._v(" "),a("Option",{key:1,attrs:{value:1}},[t._v(" 测试模式 ")])],1)],1)],1),t._v(" "),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("Button",{staticStyle:{"margin-right":"8px"},attrs:{type:"text"},on:{click:t.cancel}},[t._v("取消")]),t._v(" "),a("Button",{attrs:{type:"primary",loading:t.modalSetting.loading},on:{click:t.submit}},[t._v("确定")])],1)],1),t._v(" "),a("Modal",{attrs:{width:"360"},model:{value:t.confirmRefresh,callback:function(e){t.confirmRefresh=e},expression:"confirmRefresh"}},[a("p",{staticStyle:{color:"#f60","text-align":"center"},attrs:{slot:"header"},slot:"header"},[a("Icon",{attrs:{type:"md-information-circle"}}),t._v(" "),a("span",[t._v("确定要刷新路由么")])],1),t._v(" "),a("div",{staticStyle:{"text-align":"center"}},[a("p",[t._v("刷新路由是一个非常危险的操作，它有可能影响到您现有接口的访问，请确认无误后刷新！！")])]),t._v(" "),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("Button",{attrs:{type:"error",size:"large",long:""},on:{click:t.refreshRoute}},[t._v("确定刷新")])],1)])],1)},n=[];o._withStripped=!0,e.render=o,e.staticRenderFns=n}});
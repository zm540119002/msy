webpackJsonp([9],{228:function(t,e,a){"use strict";function o(t){d||a(809)}Object.defineProperty(e,"__esModule",{value:!0});var n=a(450),i=a.n(n);for(var r in n)"default"!==r&&function(t){a.d(e,t,function(){return n[t]})}(r);var s=a(811),l=(a.n(s),a(4)),d=!1,c=o,m=Object(l.a)(i.a,s.render,s.staticRenderFns,!1,c,"data-v-ff0e1830",null);m.options.__file="src\\views\\interface\\request.vue",e.default=m.exports},450:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=a(33),n=function(t){return t&&t.__esModule?t:{default:t}}(o),i=function(t,e,a,o){return e("Button",{props:{type:"primary"},style:{margin:"0 5px"},on:{click:function(){t.formItem.id=a.id,t.formItem.fieldName=a.fieldName,t.formItem.dataType=a.dataType.toString(),t.formItem.defaults=a.default,t.formItem.range=a.range,t.formItem.isMust=a.isMust.toString(),t.formItem.info=a.info,t.modalSetting.show=!0,t.modalSetting.index=o}}},"编辑")},r=function(t,e,a,o){return e("Poptip",{props:{confirm:!0,title:"您确定要删除这条数据吗? ",transfer:!0},on:{"on-ok":function(){n.default.get("Fields/del",{params:{id:a.id}}).then(function(e){a.loading=!1,1===e.data.code?(t.tableData.splice(o,1),t.$Message.success(e.data.msg)):t.$Message.error(e.data.msg)})}}},[e("Button",{style:{margin:"0 5px"},props:{type:"error",placement:"top",loading:a.isDeleting}},"删除")])};e.default={name:"interface_request",data:function(){return{hash:"",columnsList:[{title:"序号",type:"index",width:65,align:"center"},{title:"字段名称",align:"left",key:"showName",width:200},{title:"数据类型",align:"center",key:"dataType",width:100},{title:"是否必须",align:"center",key:"isMust",width:90},{title:"默认值",align:"center",key:"default",width:160},{title:"字段说明",align:"left",key:"info"},{title:"操作",align:"center",key:"handle",width:205,handle:["edit","delete"]}],tableData:[],tableShow:{currentPage:1,pageSize:10,listCount:0,dataType:{}},modalSetting:{show:!1,loading:!1,index:0},formItem:{fieldName:"",dataType:"2",defaults:"",range:"",isMust:"1",info:"",type:0,id:0},ruleValidate:{fieldName:[{required:!0,message:"字段名称不能为空",trigger:"blur"}]}}},created:function(){this.init()},activated:function(){this.hash=this.$route.params.hash.toString(),this.getList()},methods:{init:function(){var t=this;this.columnsList.forEach(function(e){e.handle&&(e.render=function(e,a){var o=t.tableData[a.index];return e("div",[i(t,e,o,a.index),r(t,e,o,a.index)])}),"isMust"===e.key&&(e.render=function(e,a){return 1===t.tableData[a.index].isMust?e("Tag",{attrs:{color:"error"}},"必填"):e("Tag",{attrs:{color:"success"}},"可选")}),"dataType"===e.key&&(e.render=function(e,a){var o=t.tableData[a.index].dataType;return e("Tag",{attrs:{color:"volcano"}},t.tableShow.dataType[o])})})},alertAdd:function(){this.modalSetting.show=!0},submit:function(){var t=this;this.formItem.hash=this.hash;var e=this;this.$refs.myForm.validate(function(a){if(a){e.modalSetting.loading=!0;var o="";o=0===t.formItem.id?"Fields/add":"Fields/edit",n.default.post(o,e.formItem).then(function(t){1===t.data.code?e.$Message.success(t.data.msg):e.$Message.error(t.data.msg),e.getList(),e.cancel()})}})},cancel:function(){this.modalSetting.show=!1},changePage:function(t){this.tableShow.currentPage=t,this.getList()},changeSize:function(t){this.tableShow.pageSize=t,this.getList()},search:function(){this.tableShow.currentPage=1,this.getList()},getList:function(){var t=this;n.default.get("Fields/request",{params:{page:t.tableShow.currentPage,size:t.tableShow.pageSize,hash:t.hash}}).then(function(e){var a=e.data;1===a.code?(t.tableData=a.data.list,t.tableShow.listCount=a.data.count,t.tableShow.dataType=a.data.dataType):-14===a.code?(t.$store.commit("logout",t),t.$router.push({name:"login"})):t.$Message.error(a.msg)})},doCancel:function(t){t||(this.formItem.id=0,this.formItem.defaults="",this.formItem.isMust="1",this.$refs.myForm.resetFields(),this.modalSetting.loading=!1,this.modalSetting.index=0)}}}},809:function(t,e,a){var o=a(810);"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);var n=a(15).default;n("5fa4493e",o,!1,{})},810:function(t,e,a){e=t.exports=a(14)(!1),e.push([t.i,"\n.api-box[data-v-ff0e1830] {\n  max-height: 300px;\n  overflow: auto;\n  border: 1px solid #dddee1;\n  border-radius: 5px;\n  padding: 10px;\n}\n.demo-upload-list[data-v-ff0e1830] {\n  display: inline-block;\n  width: 60px;\n  height: 60px;\n  text-align: center;\n  line-height: 60px;\n  border: 1px solid transparent;\n  border-radius: 4px;\n  overflow: hidden;\n  background: #fff;\n  position: relative;\n  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);\n  margin-right: 4px;\n}\n.demo-upload-list img[data-v-ff0e1830] {\n  width: 100%;\n  height: 100%;\n}\n.demo-upload-list-cover[data-v-ff0e1830] {\n  display: none;\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background: rgba(0, 0, 0, 0.6);\n}\n.demo-upload-list:hover .demo-upload-list-cover[data-v-ff0e1830] {\n  display: block;\n}\n.demo-upload-list-cover i[data-v-ff0e1830] {\n  color: #fff;\n  font-size: 20px;\n  cursor: pointer;\n  margin: 0 2px;\n}\n",""])},811:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("Row",[a("Col",{attrs:{span:"24"}},[a("Card",[a("p",{staticStyle:{height:"32px"},attrs:{slot:"title"},slot:"title"},[a("Button",{attrs:{type:"primary",icon:"md-add"},on:{click:t.alertAdd}},[t._v("新增")])],1),t._v(" "),a("div",[a("Table",{attrs:{columns:t.columnsList,data:t.tableData,border:"","disabled-hover":""}})],1),t._v(" "),a("div",{staticClass:"margin-top-15",staticStyle:{"text-align":"center"}},[a("Page",{attrs:{total:t.tableShow.listCount,current:t.tableShow.currentPage,"page-size":t.tableShow.pageSize,"show-elevator":"","show-sizer":"","show-total":""},on:{"on-change":t.changePage,"on-page-size-change":t.changeSize}})],1)])],1)],1),t._v(" "),a("Modal",{attrs:{width:"668",styles:{top:"30px"}},on:{"on-visible-change":t.doCancel},model:{value:t.modalSetting.show,callback:function(e){t.$set(t.modalSetting,"show",e)},expression:"modalSetting.show"}},[a("p",{staticStyle:{color:"#2d8cf0"},attrs:{slot:"header"},slot:"header"},[a("Icon",{attrs:{type:"md-information-circle"}}),t._v(" "),a("span",[t._v(t._s(t.formItem.id?"编辑":"新增")+"请求字段")])],1),t._v(" "),a("Form",{ref:"myForm",attrs:{rules:t.ruleValidate,model:t.formItem,"label-width":80}},[a("FormItem",{attrs:{label:"字段名称",prop:"fieldName"}},[a("Input",{attrs:{placeholder:"请输入字段名称"},model:{value:t.formItem.fieldName,callback:function(e){t.$set(t.formItem,"fieldName",e)},expression:"formItem.fieldName"}})],1),t._v(" "),a("FormItem",{attrs:{label:"数据类型",prop:"dataType"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.dataType,callback:function(e){t.$set(t.formItem,"dataType",e)},expression:"formItem.dataType"}},t._l(t.tableShow.dataType,function(e,o){return a("Option",{key:o,attrs:{value:o}},[t._v(" "+t._s(e)+" ")])}),1)],1),t._v(" "),a("FormItem",{attrs:{label:"是否必填"}},[a("RadioGroup",{model:{value:t.formItem.isMust,callback:function(e){t.$set(t.formItem,"isMust",e)},expression:"formItem.isMust"}},[a("Radio",{attrs:{label:"0"}},[t._v("不必填")]),t._v(" "),a("Radio",{attrs:{label:"1"}},[t._v("必填")])],1)],1),t._v(" "),"0"===t.formItem.isMust.toString()?a("FormItem",{attrs:{label:"默认值",prop:"default"}},[a("Input",{staticStyle:{width:"300px"},model:{value:t.formItem.defaults,callback:function(e){t.$set(t.formItem,"defaults",e)},expression:"formItem.defaults"}}),t._v(" "),a("Tag",{staticStyle:{"margin-left":"5px"},attrs:{color:"error"}},[t._v("仅在字段非必填的情况下生效")])],1):t._e(),t._v(" "),a("FormItem",{attrs:{label:"规则细节",prop:"range"}},[a("Input",{attrs:{type:"textarea",placeholder:"请输入符合要求的JSON字符串"},model:{value:t.formItem.range,callback:function(e){t.$set(t.formItem,"range",e)},expression:"formItem.range"}})],1),t._v(" "),a("FormItem",{attrs:{label:"字段说明",prop:"info"}},[a("Input",{attrs:{type:"textarea",placeholder:"请输入字段描述"},model:{value:t.formItem.info,callback:function(e){t.$set(t.formItem,"info",e)},expression:"formItem.info"}})],1)],1),t._v(" "),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("Button",{staticStyle:{"margin-right":"8px"},attrs:{type:"text"},on:{click:t.cancel}},[t._v("取消")]),t._v(" "),a("Button",{attrs:{type:"primary",loading:t.modalSetting.loading},on:{click:t.submit}},[t._v("确定")])],1)],1)],1)},n=[];o._withStripped=!0,e.render=o,e.staticRenderFns=n}});
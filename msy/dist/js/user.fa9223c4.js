(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["user"],{1511:function(a,t,s){"use strict";s.r(t);var e=function(){var a=this,t=a.$createElement,s=a._self._c||t;return s("div",[s("div",[s("van-button",{attrs:{type:"primary"},on:{click:a.showPopup}},[a._v(" 登录")]),s("van-popup",{staticClass:"aaa",model:{value:a.show,callback:function(t){a.show=t},expression:"show"}},[s("div",[s("img",{attrs:{src:"",alt:""}})]),s("van-tabs",[s("van-tab",{attrs:{title:"登录"}},[s("van-cell-group",[s("van-field",{attrs:{placeholder:"请输入用户名"},model:{value:a.postData.userName,callback:function(t){a.$set(a.postData,"userName",t)},expression:"postData.userName"}}),s("van-field",{attrs:{placeholder:"密码"},model:{value:a.postData.password,callback:function(t){a.$set(a.postData,"password",t)},expression:"postData.password"}})],1),s("van-button",{staticClass:"btn",attrs:{type:"info"},on:{click:a.loginHandle}},[a._v("登录")])],1),s("van-tab",{attrs:{title:"注册/重置密码"}},[s("van-cell-group",[s("van-field",{attrs:{placeholder:"请输入用户名"},model:{value:a.postData2.userName,callback:function(t){a.$set(a.postData2,"userName",t)},expression:"postData2.userName"}}),s("van-field",{attrs:{placeholder:"请输入收到的验证码"},model:{value:a.postData2.captcha,callback:function(t){a.$set(a.postData2,"captcha",t)},expression:"postData2.captcha"}}),s("van-field",{attrs:{placeholder:"设置密码"},model:{value:a.postData2.password,callback:function(t){a.$set(a.postData2,"password",t)},expression:"postData2.password"}})],1),s("van-button",{staticClass:"btn",attrs:{type:"info"},on:{click:a.registerHandle}},[a._v("注册")])],1)],1)],1)],1),s("Tabbar",{attrs:{msg:"Welcome to Your Vue.js App"}})],1)},o=[],n=s("3d39"),l={name:"home",components:{Tabbar:n["a"]},data:function(){return{show:!1,postData:{userName:"",password:""},postData2:{userName:"",password:"",captcha:""}}},methods:{showPopup:function(){this.show=!0},loginHandle:function(){console.log(this.postData)},registerHandle:function(){console.log(this.postData2)}}},p=l,r=(s("bb87"),s("2877")),c=Object(r["a"])(p,e,o,!1,null,"f2ea7f28",null);t["default"]=c.exports},7906:function(a,t,s){},bb87:function(a,t,s){"use strict";var e=s("7906"),o=s.n(e);o.a}}]);
//# sourceMappingURL=user.fa9223c4.js.map
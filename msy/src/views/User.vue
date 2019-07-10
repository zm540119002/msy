<template>
  <div>
    <div>     
      <van-button type="primary" @click="showPopup"> 登录</van-button>
       <van-popup class="card" v-model="show">
        <div><img src="../../static/images/ucenter_logo.png" alt=""></div>
        <van-tabs >
          <van-tab title="登录">
             <van-cell-group>
              <van-field v-model="loginData.userName" placeholder="请输入用户名" />
              <van-field v-model="loginData.password" placeholder="密码" />
            </van-cell-group>
            <van-button class="btn" @click="loginHandle" type="info">登录</van-button>
          </van-tab>
          <van-tab title="注册/重置密码">
            <van-cell-group>
              <van-field v-model="registerData.userName" placeholder="请输入用户名" />
              <van-field v-model="registerData.captcha" placeholder="请输入收到的验证码" />
              <van-field v-model="registerData.password" placeholder="设置密码" />
            </van-cell-group>
            <van-button class="btn" @click="registerHandle" type="info">注册</van-button>
          </van-tab>
        </van-tabs>        
      </van-popup>
    </div>
    <Tabbar msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import Tabbar from '@/components/Tabbar.vue'
import { sendIdentifyingCode, saveGoodsImageList, saveUser } from '@/api/util'
export default {
  name: 'home',
  components: {
    Tabbar
  },
  data(){
    return {
      show: false,
      loginData:{
        userName: "",
        password: ""
      },
      registerData:{
        userName: "",
        password: "",
        captcha:""
      }
    }
  },
  methods: {
    showPopup() {
      this.show = true;
    },
    loginHandle(){
      saveUser(this.loginData)
        .then(res => {
          console.log(res)
          const resData = res.data
          console.log(res.data)
        }) //接口调用成功返回的数据
        .catch(err => {
          console.log(err)
        }) //接口调用失败返回的数据
    },
    registerHandle(){
      console.log(this.registerData)
    }
  }
 
}
</script>

<style lang="less" scoped>

.card{
  width:92%;
  border-radius: 10px;
  padding: 5% 0;
  div{
    text-align:center;
    img{
      margin-top: 20px;
    }
  }
}
.btn{
  width: 92%;
  margin-bottom: 5%;
  margin-top: 10px;
  background-color: #FF7BAC;
  border:1px solid #FF7BAC;
  border-radius: 5px;
}
.van-field{
  padding-bottom: 0;
}

</style>

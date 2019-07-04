<template>
  <div>
    <div>     
      <van-button type="primary" @click="showPopup"> 登录</van-button>
      <van-popup class="aaa" v-model="show">
          <van-cell-group>
            <van-field v-model="postData.userName" placeholder="请输入用户名" />
            <van-field v-model="postData.password" placeholder="密码" />
          </van-cell-group>
        <van-button @click="loginHandle" type="info">登录</van-button>
      </van-popup>
    </div>
    <Tabbar msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import Tabbar from '@/components/Tabbar.vue'
export default {
  name: 'home',
  components: {
    Tabbar
  },
 data() {
    return {
      show: false,
        postData:{
            userName: "",
            password: ""
        }
    }
  },

  methods: {
    showPopup() {
      this.show = true;
    },
    loginHandle(){
      console.log(this.postData)
    }
  }
 
}
</script>

<style lang="less" scoped>
.aaa{
  width: 90%;
  border-radius: 10px;
}
</style>

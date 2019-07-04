<template>
  <div>
    <div>     
      <van-button type="primary" @click="showPopup"> 登录</van-button>
      <van-popup class="aaa" v-model="show">
        手机号<input type="text">
        密码<input type="password">
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
      show: false
    }
  },

  methods: {
    showPopup() {
      this.show = true;
    }
  }
 
}
</script>

<style lang="less" scoped>
.aaa{
  width: 80%;
}
</style>

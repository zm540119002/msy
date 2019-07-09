<template>
  <div>
    <div class="header">
      <div @click="back" class="back"><van-icon name="arrow-left" /></div>
      <div class="top">
        <div class="left"></div>
        <div class="center">
          <div>杨凤</div>
          <div>详情详情</div>
        </div>
        <div class="right">
          <div>关注</div>
          <div>1000人</div>
        </div>
      </div>
    </div>
    <div class="details">
      <van-tabs v-model="active">
        <van-tab title="所有">
          <List/>
        </van-tab>
        <van-tab title="消息">内容 2</van-tab>
        <van-tab title="小视频">内容 3</van-tab>
        <van-tab title="视频">内容 4</van-tab>
        <van-tab title="文章">内容 4</van-tab>
        <van-tab title="云店">内容 4</van-tab>
      </van-tabs>
    </div>
  </div>
</template>

<script>
import List from '@/components/List.vue'
export default {
  name: 'Creatorspace',
  components: {
    List
  },
 data() {
    return {
      active: 0
    };
  },
  methods:{
     back(){
         this.$router.back(-1)
     }
   }
}
</script>

<style lang="less" scoped>
  .header{
    background-color: #666;
    color: #fff;
    padding: 0.25rem;
    .back{
      margin-bottom: 0.4rem;
    }
   .top{
        display: flex;
        padding-top: 0.2rem;
        .left{
          width: 1.2rem;
          height: 1.2rem;
          border-radius: 50%;
          background: #999;
          margin-right: 0.3rem;
          background-image: url(../../static/images/11.png)
        }
        .center{
          flex: 1;
          div:nth-of-type(2){
            color: #ccc;
          }
        }
        .right{
          text-align: center;
          div:nth-of-type(1){
            padding: 0.06rem 0.4rem;
            background: red;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 0.06rem;
          }
        }
      }  
  }
  .details{
   .van-tab{
     background-color: red;
   }
  }
</style>
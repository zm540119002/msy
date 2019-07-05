<template>
  <div class="home">
    <div class="content">
      <div class="nav">
        <div><img src="../../static/images/index_top.png" alt=""></div>
        <div><input type="text" placeholder="搜索消息、小视频、视频或文章"></div>
        <div><span class="mui-icon mui-icon-chat"></span></div>
        <div><span class="mui-icon mui-icon-camera"></span></div>
      </div>
      <div class="top_img"><img src="../../static/images/home-banner1.jpg"></div>
      <!-- icon -->
      <div class="icon">
        <div><a href=""></a><span>入驻管理</span></div>
        <div><a href=""></a><span>商家云店</span></div>
        <div><a href=""></a><span>老板直聘</span></div>
        <div><a href=""></a><span>美尚课堂</span></div>
        <div><a href=""></a><span>美尚会</span></div>
      </div>
      <div class="wel">
        <van-tabs v-model="active">
          <van-tab title="关注">关注</van-tab>
          <van-tab title="推荐">
            <div>aaa</div>
          </van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
        </van-tabs>
      </div>
    </div>
    <Tabbar/>
  </div>
</template>

<script>
// @ is an alias to /src
import Tabbar from '@/components/Tabbar.vue'

export default {
  name: 'home',
  components: {
    Tabbar
  },
   data() {
    return {
      active: 1
    };
  }
}
</script>
<style lang="less" scoped>
  .content{
    width: 100%;
    height: 100%;
    position: relative;
    .top_img img{
      width: 100%;
    }
    .icon{
      position: relative;
      width: 100%;
      margin-top:-0.4rem;
      padding: 0.15rem;
      background: #F7F8F8;
      display: flex;
      border-radius: 20px 20px 0 0;
      border-bottom: 6px solid #f2f2f2;
       div{
         text-align: center;
                flex: 1;
        a{
          margin: 0 auto 0.05rem;
          display: block;
          width: 0.8rem;
          height: 0.8rem;
          background-image:url(../../static/images/store_purchase_type.png);
          background-repeat: no-repeat;
          background-size: 0.785rem 3.99rem;
        }
        span{
          font-size: 0.2rem;
        }
     }
     div:nth-of-type(2) a{
       background-position: 0px -39px;
     }
    div:nth-of-type(3) a{
       background-position: 0px -79px;
     }
     div:nth-of-type(4) a{
       background-position: 0px -119px;
     }
     div:nth-of-type(5) a{
       background-position: 0px -159px;
     }
    }

    .nav{
      width: 100%;
      position: fixed;
      top:0;
      padding: 0.16rem;
      display: flex;
      text-align: center;
      div:nth-of-type(1){
        text-align: center;
        width: 14%;
        img{
          width: 76%;
        }   
      }   
      div:nth-of-type(2){
        width:68%;
        input{
          height: 30px;
          border-radius: 6px;
        }  
      }   
      div:nth-of-type(3){
        width:9%; 
        color: #fff;
      }
       div:nth-of-type(4){
        width:9%;
        color: #fff;
      }    
    }
    .wel{
      height: 200px;
      background: #fff;
      .van-tab__pane{
        padding: 0 0.25rem;
      }
    }
  }
</style>

<template>
  <div class="home">
    <div class="content">
      <div class="nav">
        <div><img src="../../../static/images/index_top.png" alt=""></div>
        <div @click="search"><input type="text" placeholder="搜索消息、小视频、视频或文章"></div>
        <div><span class="mui-icon mui-icon-chat"></span></div>
        <div><span class="mui-icon mui-icon-camera"></span></div>
      </div>
      <div class="top_img"><img src="../../../static/images/home-banner1.jpg"></div>
      <!-- icon -->
      <div class="icon">
        <div><a href="javascript:;"></a><span>入驻管理</span></div>
        <div><a href=""></a><span>商家云店</span></div>
        <div><a href=""></a><span>老板直聘</span></div>
        <div><a href=""></a><span>美尚课堂</span></div>
        <div><a href=""></a><span>美尚会</span></div>
      </div>
      <div class="wel">
        <van-tabs v-model="active">
          <van-tab title="关注">关注</van-tab>
          <van-tab title="推荐">
            <div class="recommend">
              <div>推荐关注</div>
              <div>换一批></div>
            </div>
            <div class="content1">
              <div>
                <div  @click="clickspace"></div>
                <div>标题标题</div>
                <div>详情详情</div>
                <div>+关注</div>
              </div>
              <div>
                <div></div>
                <div>标题标题</div>
                <div>详情详情</div>
                <div>+关注</div>
              </div>
              <div>
                <div></div>
                <div>标题标题</div>
                <div>详情详情</div>
                <div>+关注</div>
              </div>
              <div>
                <div></div>
                <div>标题标题</div>
                <div>详情详情</div>
                <div>+关注</div>
              </div>
            </div>
          </van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
          <van-tab title="XX">XX</van-tab>
        </van-tabs>
      </div>
     <List1/>
     <Userinfo/>
     <List/>
      <div  class="video">
        <Userinfo/>
        <van-swipe :loop="false" :width="300">
          <van-swipe-item><img src="../../../static/images/video1.png" alt=""></van-swipe-item>
          <van-swipe-item><img src="../../../static/images/video1.png" alt=""></van-swipe-item>
          <van-swipe-item><img src="../../../static/images/video1.png" alt=""></van-swipe-item>
          <van-swipe-item><img src="../../../static/images/video1.png" alt=""></van-swipe-item>
        </van-swipe>
      </div>
    </div>
    <Tabbar/>
  </div>
</template>

<script>
// @ is an alias to /src
import Tabbar from '@/components/supplier/Tabbar.vue'
import List1 from '@/components/supplier/List1.vue'
import List from '@/components/supplier/List.vue'
import Userinfo from '@/components/supplier/Userinfo.vue'

export default {
  name: 'home',
  components: {
    Tabbar,
    List1,
    List,
    Userinfo
  },
   data() {
    return {
      active: 1
    };
  },
   methods:{
     search(){
        this.$router.push({
        name: "Search"
      })
     },
     clickspace(){
       this.$router.push({
         name:"Creatorspace"
       })
      }
   }
}
</script>
<style lang="less" scoped>
  .content{
    width: 100%;
    height: auto;
    position: relative;
    margin-bottom: 100px;
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
          background-image:url(../../../static/images/store_purchase_type.png);
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
      position: absolute;
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
      height: auto;
      margin-bottom: 10px;
      .van-hairline--top-bottom{
        background: red;
      }
      .van-tab__pane{
        padding: 0 0.25rem;      
        .recommend{
         display: flex;
         justify-content: space-between;
         padding: 0.2rem 0;
         div:nth-of-type(1){
           color: #999
         }
         div:nth-of-type(2){
           color: #595757
         }
        }
        .content1{
          display: flex;
          >div{
            border: 1px solid #ccc;
            flex: 1;
            margin-right: 6px;
            border-radius: 4px;
            text-align: center;
            padding: 10px 0;
            >div:nth-of-type(1){
              background: #ccc;
              width: 1.2rem;
              height: 1.2rem;
              margin: 0 auto;
              border-radius: 50%;
              background-image: url(../../../static/images/11.png)
            }
            >div:nth-of-type(2){
              margin: 6px 0 2px;
            }
            >div:nth-of-type(3){
             color: #999;
            }
            >div:nth-of-type(4){
             border: 1px solid #ccc;
             width: 60%;
             margin: 0 auto;
             border-radius: 6px;
             color: red;
            }
          }
          div:nth-of-type(4){
            margin-right: 0;
            
          }
        }
      }
    }
    .details{
      margin-bottom: 0.3rem;
      .wenzi{
        margin-top: 0.2rem;
      }
      .img{
          display: flex;
          flex-direction: row;
          flex-wrap:wrap;
          margin-top: 0.2rem;
        div{
          width: 32%;
          margin-right: 2%;
          img{
            width: 100%;
            
          }
        }
        div:nth-of-type(3n){
          margin-right: 0;
        }
        div:nth-of-type(-n+3){
          margin-bottom: 2%;
        }
      }
    }
    .video{
      .van-swipe{
        padding: 0 0.2rem;
      }
      .top{
        display: flex;
        justify-content: space-between;
      }
    } 
  }
</style>

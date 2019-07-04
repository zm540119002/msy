<template>
  <div class="home">
    <div class="content">
      <div class="nav">
        <ul>
          <li><a href="">采购</a></li>
          <li><a href="">分成</a></li>
          <li><a href="">加盟</a></li>
          <li><a href="">店家入驻</a></li>
          <li><a href="">智慧店务</a></li>
        </ul>
      </div>
      <div class="top_img"><img src="https://msy.meishangyun.com/static/common/img/banner/home-banner1.jpg"></div>
      <!-- icon -->
      <div class="icon">
        <div><a href=""></a><span>入驻部署</span></div>
        <div><a href=""></a><span>店长管理</span></div>
        <div><a href=""></a><span>店铺运营</span></div>
        <div><a href=""></a><span>订单客服</span></div>
        <div><a href=""></a><span>财务结算</span></div>
      </div>
      <div class="wel">
      欢迎来到供应商管理中心
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
      position: absolute;
      top: 10px;
      left: 10px;
      ul{
        display: flex;
        li{
          margin-right: 5px;
          a{
          color: #fff;
        }
        }
      }
    }
    .wel{
      height: 200px;
      background: #fff
    }
  }
</style>

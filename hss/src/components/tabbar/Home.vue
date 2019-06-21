<template>
  <div class="content">
    <div class="top_img"><img src="https://hss.meishangyun.com/uploads/2019051511074301713.jpeg"></div>
   <div class="content-in">
     <!-- 轮播图 -->
      <mt-swipe :auto="4000">
      <mt-swipe-item><a href=""><img src="https://hss.meishangyun.com/uploads/2019060415301545730.png"/></a></mt-swipe-item>
      <mt-swipe-item><a href=""><img src="https://hss.meishangyun.com/uploads/2019060415304663609.png"/></a></mt-swipe-item>
      <mt-swipe-item><a href=""><img src="https://hss.meishangyun.com/uploads/2019061315524548068.jpeg"/></a></mt-swipe-item>
    </mt-swipe>
    <!-- 入口 -->
    <div class="scene_wrapper">
      <div><a href=""><img src="https://hss.meishangyun.com/static/common/img/scene2.jpg"></a></div>
      <div><a href=""><img src="https://hss.meishangyun.com/static/common/img/scene3.jpg"></a></div>
      <div><a href=""><img src="https://hss.meishangyun.com/static/common/img/scene1.jpg"></a></div>
    </div>
    <!-- 快报 -->
    <div class="news_title">黑森森快报 最新 黑森森快报正式上线啦！</div>
    <div class="customer_list_wrapper">
      <ul style="list-style:none" id="marquee">
         <li v-for="(item,indx) in congratulationText" :key="indx">{{item}}</li>
     </ul>
    </div>
    <!-- 入口 -->
    <div class="scene_wrapper">
      <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014521821780.jpeg"></a></div>
      <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014533591170.jpeg"></a></div>
      <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019061511330114685.jpeg"></a></div>
    </div>
    <!-- 场景 -->
    <div class="columns_flex">
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
        <div><a href=""><img src="https://hss.meishangyun.com/uploads/hss_scene/2019051014545855931.jpeg"></a></div>
    </div>
   </div>
  </div>
  
</template>

<script>
export default {
  data(){
    return{
      congratulationText:[
            '王女士 11111111111111111',
            '王先生 22222222222222222',
            '王女士 333333333333333333',
            '王先生 444444444444444444',
            '王女士 5555555555555555555',
            '王先生 66666666666666666666',
            '王女士 77777777777777777777',
        ],
    }
  },
  methods:{
     //滚动条
    scrollAnimation(){
        let _this = this;
        clearTimeout(_this.timeout);
        var marquee = document.getElementById('marquee');
        var offset=0;
        var scrollheight =marquee.offsetHeight;//整个高度
        var firstNode = marquee.children[0].cloneNode(true);
        marquee.removeChild(marquee.children[0]);//还有这里
        marquee.appendChild(firstNode);//还有这里
        if(offset == scrollheight){
            offset = 0;
        }
        marquee.style.marginTop = "-"+offset+"px";
        offset += 1;
        _this.timeout = setTimeout(_this.scrollAnimation,4000);
    }
  },
  mounted(){
        let _this = this;
        _this.scrollAnimation();//祝贺信息滚动条加载
  }
}
</script>

<style lang="less" scoped>
  .content{
    margin-bottom: 50px;
    .content-in{
      padding: 0.15rem 0.15rem;
    }
    .top_img{
      img{
        width: 100%;
      }
    }
    .mint-swipe{
    width: 100%;
    height: 120px;
    a{
      height: 100%;
      display: block;
      img{
        width: 100%;
        height: 100%;
      }
    }
  }
  .scene_wrapper{
    display: flex;
    div{
      flex: 4;
      margin-top: 4px;
      img{
        width: 100%;
      }
    }
    div:nth-of-type(2){
      margin-left: 6px;
      margin-right: 6px;
    }
  }
  .news_title{
    background: #fff;
    padding: 0.1rem;
    margin-bottom: 0.1rem;
    margin-top: 0.1rem;
    border:1px solid #ccc;
    border-radius: 5px;    
  }
  .customer_list_wrapper{
      width: 100%;
      height:72px;
      background:#fff;
      overflow: hidden;
      padding: 0.15rem
    }
    .columns_flex{
      display: flex;
       flex-wrap:wrap;
     div{
       width: 49%;
       margin-top: 0.15rem;
       &:nth-of-type(2n){
         margin-left: 2%
       }
       img{
         width: 100%;
       }
     }
    }
  }
</style>

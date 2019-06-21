import VueRouter from 'vue-router'

// 导入相对应的组件
import Home from './components/tabbar/Home.vue' 
import Fran from './components/tabbar/Fran.vue' 
import City from './components/tabbar/City.vue' 
import Cart from './components/tabbar/Cart.vue' 
import User from './components/tabbar/User.vue' 

// 3. 创建路由对象
var router = new VueRouter({
  routes: [  // 配置路由规则
    {path:'/home',component: Home},
    {path:'/fran',component: Fran},
    {path:'/city',component: City},
    {path:'/cart',component: Cart},
    {path:'/user',component: User},
  ],
  linkActiveClass: 'mui-active' // 覆盖默认的路由高亮的 类
})

// 把路由对象暴露出去
export default router
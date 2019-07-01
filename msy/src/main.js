import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// 导入初始化文件
import '../static/common/reset.css'

// 导入mui的样式
import '../static/css/mui.min.css'
import '../static/css/icons-extra.css'

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
Vue.use(MintUI)

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')

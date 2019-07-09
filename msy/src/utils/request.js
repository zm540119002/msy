import axios from 'axios'
import { Message } from 'element-ui'

// import store from '../../node_modules/@/store'
// import { getStore } from '../../node_modules/@/utils'

const baseURL = process.env.NODE_ENV === 'development' ? '/apis' : process.env.BASE_API
console.log(process.env);
const service = axios.create({
  baseURL,
  timeout: 15 * 1000
})

service.interceptors.request.use(
  config => {
    // 在发送请求之前做些什么
    // if (store.state.user.token) {
    //   // 让每个请求携带token-- ['token']为自定义key
    //   config.headers.Authorization = getStore('token')
    //   config.headers.Token = getStore('token')
    // }
    return config
  },
  error => {
    console.log(error) // for debug
    Promise.reject(error)
  }
)
// 拦截响应
service.interceptors.response.use(
  response => response,
  error => {
    console.log('err' + error) // for debug
    Message({
      message: '请求异常',
      type: 'error'
    })
    return Promise.reject(error)
  }
)

service.all = axios.all
service.spread = axios.spread

export default service

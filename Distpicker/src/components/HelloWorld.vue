<template>
  <div>
    <mt-button type="primary" @click="select">选择省市区</mt-button>
    <p :value="address">{{address}}</p>
    <yz-select v-if="show" :ref="show" @changeValue='isShow' :province='province' :city='city' :county='county'></yz-select>
  </div>
</template>

<script>
import select from '@/components/select'
export default {
  name: 'HelloWorld',
  data () {
    return {
		  codeValue: '获取验证码',
		  validCode: true,
      show: false,
      address: '',
      province: '北京市',
      city: '北京市',
      county: '东城区'
    }
  },
  components:{
    'yz-select': select
  },
  methods: {
    select(){
			this.show = true;
    },
    isShow(msg){
			console.log(msg);
			this.show = msg.show;
			this.address = msg.result;
			this.province = msg.province;
			this.city = msg.city;
			this.county = msg.county;
		},
		getCode(){
			var time=60;
			var that = this;
			if(this.validCode){
				this.validCode = false;
				this.codeValue =  time + '秒';
				var t = setInterval(getTime,1000)
			}
			function getTime(){
				time--;
				that.codeValue =  time + '秒';
				if (time == 0) {
						clearInterval(t);
						that.codeValue = '重新获取';
						that.validCode=true;
				}
			}
		}
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>

<template>
  <div>
    <nav class="mui-bar mui-bar-tab">
			<router-link class="mui-tab-item" to="/" exact>
				<span class="mui-icon mui-icon-home"></span>
				<span class="mui-tab-label">采购商</span>
			</router-link>
			<router-link class="mui-tab-item" :to="{name:'about'}" exact>
				<span class="mui-icon mui-icon-personadd"></span>
				<span class="mui-tab-label">从业人员</span>
			</router-link>
			<router-link class="mui-tab-item"  to="/factory" exact>
				<span class="mui-icon mui-icon-contact"></span>
				<span class="mui-tab-label">供应商</span>
			</router-link>
      <router-link class="mui-tab-item" to="/cart" exact>
				<span class="mui-icon mui-icon-extra mui-icon-extra-cart"></span>
				<span class="mui-tab-label">采购车</span>
			</router-link>
      <router-link class="mui-tab-item" to="/user" exact>
				<span class="mui-icon mui-icon-contact"></span>
				<span class="mui-tab-label">我</span>
			</router-link>
		</nav>
  </div>
</template>

<script>
export default {
  name: 'Tabbar',
  props: {
    msg: String
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="less">
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>

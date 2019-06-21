<template>
  <div class="app-content">
   <router-view></router-view>
   <nav class="mui-bar mui-bar-tab">
			<router-link class="mui-tab-item" to="/home">
				<span class="mui-icon mui-icon-home"></span>
				<span class="mui-tab-label">采购商场</span>
			</router-link>
			<router-link class="mui-tab-item" to="/fran">
				<span class="mui-icon mui-icon-email"></span>
				<span class="mui-tab-label">加盟店家</span>
			</router-link>
			<router-link class="mui-tab-item" to="/city">
				<span class="mui-icon mui-icon-contact"></span>
				<span class="mui-tab-label">城市合伙人</span>
			</router-link>
      <router-link class="mui-tab-item" to="/cart">
				<span class="mui-icon mui-icon-extra mui-icon-extra-cart"><span class="mui-badge">9</span></span>
				<span class="mui-tab-label">采购车</span>
			</router-link>
			<router-link class="mui-tab-item" to="/user">
				<span class="mui-icon mui-icon-personadd"></span>
				<span class="mui-tab-label">我</span>
			</router-link>
		</nav>
  </div>
</template>

<script>
</script>


<style lang="less" scoped>
  
</style>

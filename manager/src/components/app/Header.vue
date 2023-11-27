<script setup lang="ts"> 
import { RouterLink,useRouter } from 'vue-router'; 
const router = useRouter() 
const logout = () =>{
	sessionStorage.removeItem("userInfo")
	router.push('/')
} 
</script>

<template>
	<div class="warap" style="display: none;">
			<div id="header" class="header" > 
		<h1>
					<img src="/assets/img/logo/logo.png" alt="" /> 
		</h1>
		<div class="part__buttons">
		<button class="btn_logout" @click="logout">로그아웃</button>
		</div>
		<!-- BEGIN header-nav -->
		 
	</div>
	</div>

</template>

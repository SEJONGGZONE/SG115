<script setup >
import SidebarNav from '@/components/app/SidebarNav.vue';
import { useRouter, RouterLink } from 'vue-router' 
import { useAlert } from '@/composables/showAlert'
const {showAlert} = useAlert()

const notEnterUrl = [
	'/map/carLocationManager'
]

const router = useRouter();
const props = defineProps({
	menu: {
		type : Object
		,default:{}
	}
}) 
function subIsActive(urls) {
	var currentRoute = router.currentRoute.value.path;
	var match = false;
	
	for (var x = 0; x < urls.length; x++) {
		if (urls[x].url == currentRoute) {
			match = true;
		}
	}
	
	return match;
}
function movePage(url){  
	if(notEnterUrl.includes(url)){
		   showAlert("메뉴 준비중입니다.")
		return
	}

	const now = new Date();

	const twoDigits = (num) => String(num).padStart(2, '0');

	const year = now.getFullYear();       // 년 
	const month = twoDigits(now.getMonth() + 1);     // 월 (0부터 시작하므로 1을 더해줍니다)
	const day = twoDigits(now.getDate());            // 일
	const hours = twoDigits(now.getHours());         // 시
	const minutes = twoDigits(now.getMinutes());     // 분
	const seconds = twoDigits(now.getSeconds());     // 초
	const randomThreeDigits = twoDigits(Math.floor(Math.random() * 1000));


const combined = `${year}${month}${day}${hours}${minutes}${seconds}${randomThreeDigits}`;
	
	router.push(url + '?' + combined)
}
</script>
<template>
	<!-- menu with submenu -->
	 
		<li v-if="menu.children"  v-bind:class="{ 'active': subIsActive(menu.children) }">
			<button  @click="movePage(menu.url)" v-bind:class="{ 'active': subIsActive(menu.children) }"> 
				<i v-bind:class="menu.icon"></i>
				<span>{{ menu.title }}</span>  
				<i class="fa-solid item__angle fa-angle-right"></i>
			</button> 
		 	<ul v-bind:class="{ 'active': subIsActive(menu.children) }" >  
			
					<template v-for="(submenu, index) in menu.children">
						<sidebar-nav v-bind:menu="submenu"></sidebar-nav>
					</template>

			</ul>  
		</li>   
  
	<!-- menu without submenu -->
	<router-link  v-else v-bind:to="menu.url" custom v-slot="{  isActive }">  
		<li @click="movePage(menu.url)">
				<button  v-bind:class="{ 'active': isActive }"> 
				<i v-if="menu.icon" v-bind:class="menu.icon"></i>
				<span>{{ menu.title }}</span>   
			</button>
		</li>
	</router-link>
</template>
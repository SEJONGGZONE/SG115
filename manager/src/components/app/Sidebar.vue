<script setup lang="ts">
import { useAppSidebarMenuStore } from '@/stores/app-sidebar-menu';
import { useAppSidebarMenuDevStore } from '@/stores/app-sidebar-menu_dev';
import { useAppOptionStore } from '@/stores/app-option';
import { onMounted , computed} from 'vue'; 

import SidebarNav from '@/components/app/SidebarNav.vue';

let appSidebarMenu = useAppSidebarMenuStore();
if(location.origin.indexOf("localhost")>-1){
  appSidebarMenu = useAppSidebarMenuDevStore(); 
}
const appOption = useAppOptionStore();
var appSidebarFloatSubmenuTimeout = '';
var appSidebarFloatSubmenuDom = '';
 

const getUserName = computed(() =>{
	let userInfoStr = sessionStorage.getItem("userInfo") ?? ''
	let userInfo = ''
	if(userInfoStr){
		userInfo = JSON.parse(userInfoStr)
	}
	return userInfo?.NAME 
})
onMounted(() => {
	// console.log(11111111111111111111)
});
const onClickFoldButton = ()=> {  
      const ele = document.querySelectorAll('.lnb .btn_fold');
      const contents = document.querySelectorAll('.lnb');
      ele[0].classList.toggle('fold');
      contents[0].classList.toggle('fold'); 
}

import { RouterLink,useRouter } from 'vue-router'; 
const router = useRouter() 
const logout = () =>{
	sessionStorage.removeItem("userInfo")
	router.push('/')
}
</script>
<template> 
  <div class="lnb">
    <button class="btn_fold" @click="onClickFoldButton()">
      <i class="fa-solid fa-arrow-left"></i>
    </button>
    <div class="part__lnb fold">
      <div class="part__login_info">
        <div class="item__profile">
          <img src="/assets/img/sungchang/logo_01_200.png" alt="성창푸드" />
        </div>
        <!-- <b class="item__name">{{getUserName}}</b> -->
      </div>
      <div class="part__logout" style="align-self: flex-end;">
        <button class="btn_save" @click="logout">
          <span> 로그아웃 >></span>
        </button>
      </div>
      <div class="part__menu_list">
        <ul class="menu_list"> 
          <template v-for="menu in appSidebarMenu">  
            <sidebar-nav v-if="menu.title" v-bind:menu="menu"  ></sidebar-nav>
          </template>
        </ul>
      </div>
    </div>
  </div>  
</template>
<style>
ul {
	padding-left:0rem;
}
</style>
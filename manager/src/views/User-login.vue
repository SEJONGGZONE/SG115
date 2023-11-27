<script setup>
import { loginApi } from '@/api'  
import { startLoadingBar, removeLoadingBar, doSort } from "@/common/utils.ts";
import { useAppOptionStore } from '@/stores/app-option';
import { onMounted,ref } from 'vue';

import { useAlert } from '@/composables/showAlert'
import { RouterLink, RouterView, useRouter } from "vue-router";

// 메뉴정보가져오기..
import { useAppSidebarMenuStore } from '@/stores/app-sidebar-menu';
import { useAppSidebarMenuDevStore } from '@/stores/app-sidebar-menu_dev';
let appSidebarMenu = useAppSidebarMenuStore();
if(location.origin.indexOf("localhost")>-1){
  appSidebarMenu = useAppSidebarMenuDevStore(); 
}

const router = useRouter();
const {showAlert,showAlertSuccess} = useAlert()
const appOption = useAppOptionStore();  

const memberId  = ref("")
const memberPw = ref("")
const rememberChk = ref(false)
const bg = {
	activeImg: '/assets/img/login-bg/bg-login.png',
	bg1: {
		active: true,
		img: '/assets/img/login-bg/login-bg-17.jpg'
	},
	bg2: {
		active: false,
		img: '/assets/img/login-bg/login-bg-16.jpg'
	},
	bg3: {
		active: false,
		img: '/assets/img/login-bg/login-bg-15.jpg'
	},
	bg4: {
		active: false,
		img: '/assets/img/login-bg/login-bg-14.jpg'
	},
	bg5: {
		active: false,
		img: '/assets/img/login-bg/login-bg-13.jpg'
	},
	bg6: {
		active: false,
		img: '/assets/img/login-bg/login-bg-12.jpg'
	},
	bg7: {
		active: false,
		img: '/assets/img/login-bg/bg-login.png'
	}
} 



onMounted(()=>{
	appOption.appsidebarHide = true
	const saveYn = localStorage.getItem("LOGIN_SAVE_YN");
	const memeberId = localStorage.getItem("LOGIN_ID");
	if(saveYn === 'Y'){
		rememberChk.value = true
		memberId.value = memeberId
	}
	select('bg4')
}) 
		//로그인
const clickLogin = async () =>{ 
	if(!memberId.value){
		showAlert("아이디를 입력하세요")
		return
	}else if(!memberPw.value){
		showAlert("비밀번호를 입력하세요")
		return	
	}

	// 대기창표시
	startLoadingBar();

	const param = {
		userId : memberId.value,
		userPw : memberPw.value
	}
	let data = await loginApi.executeLogin(param)
	if (data.RecordSet.length > 0) {
		var userInfo = data.RecordSet[0]; 
		showAlertSuccess(userInfo.RET_MSG)
		if (userInfo.RET_CODE == "100") {
			// 아이디 암호 저장..
			if (rememberChk.value) {
				localStorage.setItem("LOGIN_SAVE_YN", "Y");
				localStorage.setItem("LOGIN_ID", memberId.value);
			} else {
				localStorage.setItem("LOGIN_SAVE_YN", "N");
				localStorage.setItem("LOGIN_ID", "");
			}
			
			sessionStorage.setItem('userInfo', JSON.stringify(userInfo));
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
			// 정상처리, 페이지이동..
			//router.push('/member/memberMng?'+combined)
			// 수정(2023.10.17)
			router.push('/client/clientMng?'+combined)
			
		} else {
				
		}
	} else {
		showAlert('등록된 사용자가 없습니다.')
	}

	// 로딩바숨기기..
    removeLoadingBar();
	
}
const select = (x) =>{
	bg.bg1.active = false;
	bg.bg2.active = false;
	bg.bg3.active = false;
	bg.bg4.active = false;
	bg.bg5.active = false;
	bg.bg6.active = false;
	bg.bg7.active = false;

	switch (x) {
		case 'bg1': 
			bg.bg1.active = true;
			bg.activeImg = bg.bg1.img;
			break;
		case 'bg2': 
			bg.bg2.active = true;
			bg.activeImg = bg.bg2.img;
			break;
		case 'bg3': 
			bg.bg3.active = true;
			bg.activeImg = bg.bg3.img;
			break;
		case 'bg4': 
			bg.bg4.active = true;
			bg.activeImg = bg.bg4.img;
			break;
		case 'bg5': 
			bg.bg5.active = true;
			bg.activeImg = bg.bg5.img;
			break;
		case 'bg6': 
			bg.bg6.active = true;
			bg.activeImg = bg.bg6.img;
			break;
		case 'bg6': 
			bg.bg6.active = true;
			bg.activeImg = bg.bg6.img;
			break;
		case 'bg7': 
			bg.bg7.active = true;
			bg.activeImg = bg.bg7.img;
			break;
			
	}
} 
</script>
<template>
	<div>
		<!-- BEGIN login -->
		<div class="login login-v2 fw-bold">
			<!-- BEGIN login-cover -->
			<div class="login-cover">
				<div class="login-cover-img" v-bind:style="{ backgroundImage: 'url('+ bg.activeImg +')' }"></div>
				
				<div class="login-cover-bg"></div>
			</div>
			<!-- END login-cover -->
			
			<!-- BEGIN login-container -->
			<div class="login-container">
				<!-- BEGIN login-header -->
				<div class="login-header">
					<div class="brand">
						<div class="d-flex align-items-center" style="padding-bottom: 10px;">
							
						<span style="border-radius: 20px;width: 100px;height: 100px; " v-bind:style="{ backgroundImage: 'url('+ '/assets/img/logo/ic_launcher_ilove_ci_foreground.png' + ')'}"></span> &nbsp;<b> SG623 - 아이사랑</b>
						</div>
						<small style="padding-left:10px">관리자 페이지에 오신걸 환영합니다.</small>
					</div> 
				</div>
				<!-- END login-header -->
				
				<!-- BEGIN login-content2 -->
				<div class="login-content"> 
						<div class="form-floating_ mb-20px">
							<input type="text" class="form-control fs-13px h-45px border-0" placeholder="ID" id="emailAddress" v-model="memberId" />
							<!-- <label for="emailAddress" class="d-flex align-items-center text-gray-600 fs-13px">아이디 입력</label> -->
						</div>
						<div class="form-floating_ mb-20px">
							<input type="password" class="form-control fs-13px border-0" placeholder="Password" v-model="memberPw" @keyup.enter="clickLogin()"/>
							<!-- <label for="emailAddress" class="d-flex align-items-center text-gray-600 fs-13px">암호 입력</label> -->
						</div>
						<div class="form-check mb-20px">
							<input class="form-check-input border-0" type="checkbox" id="rememberMe" v-model="rememberChk"  />
							<label class="form-check-label fs-13px text-gray-500" for="rememberMe">
								아이디 저장
							</label>
						</div>
						<div class="mb-20px">
							<button   class="btn btn-success d-block w-100 h-45px btn-lg" @click="clickLogin()" @keyup.enter="clickLogin()"
								style="padding:0px;"
								>LOGIN</button>
						</div> 
				</div>
				<!-- END login-content -->
			</div>
			<!-- END login-container -->
		</div>
		<!-- END login -->
		
		<!-- BEGIN login-bg -->
		<div class="login-bg-list clearfix" v-show="false">
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg1.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg1')" style="background-image: url(/assets/img/login-bg/login-bg-17.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg2.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg2')" style="background-image: url(/assets/img/login-bg/login-bg-16.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg3.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg3')" style="background-image: url(/assets/img/login-bg/login-bg-15.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg4.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg4')" style="background-image: url(/assets/img/login-bg/login-bg-14.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg5.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg5')" style="background-image: url(/assets/img/login-bg/login-bg-13.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg6.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg6')" style="background-image: url(/assets/img/login-bg/login-bg-12.jpg)"></a></div>
			<div class="login-bg-list-item" v-bind:class="{ 'active': bg.bg7.active }"><a href="javascript:;" class="login-bg-list-link" v-on:click="select('bg7')" style="background-image: url(/assets/img/login-bg/bg-login.png)"></a></div>
		</div>
		<!-- END login-bg -->
	</div>
</template>
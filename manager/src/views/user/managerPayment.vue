<script setup>
import { useAppOptionStore } from '@/stores/app-option';
import { ref,onMounted } from 'vue';
import { onBeforeRouteLeave, useRoute } from 'vue-router';
import { Toast } from 'bootstrap';
import axios from 'axios'
import {memberApi} from '@/api';

const appOption = useAppOptionStore();
const route = useRoute();


let memo = ref('')//주문명
let amount = ref('')//금액
let phoneNo = ref('')//폰번호
let clcode = ref('')
let clName = ref('')
let msg = ref('')

const setParam=()=>{
	clcode.value = route.query.CLCODE
	clName.value = route.query.CLNAME
}

 
onMounted(()=> {
	setParam()
	appOption.appSidebarHide = true;
	appOption.appHeaderHide = true;
	appOption.appContentClass = 'p-0';
})
onBeforeRouteLeave((to, from, next)=> {
	appOption.appSidebarHide = false;
	appOption.appHeaderHide = false;
	appOption.appContentClass = '';
	next();
})
const checkForm= (e) => {
	e.preventDefault();
	this.$router.push({ path: '/dashboard/v3'})
} 

const checkKey= () => {
	amount.value = amount.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	phoneNo.value = phoneNo.value.replace(/[^0-9]/g, '');
	phoneNo.value = phoneNo.value.slice(0, 11);
}

const save= (event) => {

	let chk = false
	let chkId = ''

	if(!memo.value){
		chk = true 
		chkId = `${chkId} 상품명`
	}
	if(!amount.value){
		chk = true 
		chkId = !chkId? `금액` : `${chkId}, 금액`
	}
	if(!phoneNo.value){
		chk = true 
		chkId = !chkId? `휴대폰 번호` : `${chkId}, 휴대폰 번호`
	}
	
	if(chk){
		msg.value = `필수입력 항목을 확인해 주세요..<br>(${chkId})`
		event.preventDefault();
		toastpop();
		return;
	}
	doSave()
}

const toastpop = () =>{
	var toast = new Toast(document.getElementById("toastMsg"));
	toast.show();
}

const doSave = async () => { 

	let data;
	let date = new Date();
	date = date.getFullYear().toString() + date.getMonth().toString() + date.getDate().toString() + date.getHours().toString() + date.getMinutes().toString() + date.getSeconds().toString()
	

	const param = {
		clcode : clcode.value,
		memo : memo.value,
		amount: amount.value.replace(/,/gi, ''),
		key :"KEYIN_" + clcode.value +"_"+ date,
		phoneNo : phoneNo.value
	}
    
	try {
		data = await memberApi.accoutPaymentSave(param);
		if(data.RecordSet?.[0]?.PAYMENT_NO){
			msg.value = '저장되었습니다.'
			toastpop()
		}else{
			msg.value = '관리자에게 문의바랍니다.'
			toastpop()
		}
	} catch (error) {
		console.error(error);
	}
}




</script>
<template>
	<!-- BEGIN register -->
	<div class="register register-with-news-feed"> 
		<!-- END news-feed -->
		
		<!-- BEGIN register-container -->
		<div class="register-container" style="margin: auto;">
			<!-- BEGIN register-header -->
			<div class="register-header mb-25px h1">
				<div class="mb-1">결제정보 입력</div>
				<small class="d-block fs-15px lh-16">결제 정보를 입력해주세요..</small>
			</div>
			<!-- END register-header -->
			
			<!-- BEGIN register-content -->
			<div class="register-content">
					<div class="mb-3">
						<label class="mb-2">상호명</label>
						<input type="text" class="form-control fs-26px" v-model="clName" disabled/>
					</div>
					<div class="mb-3">
						<label class="mb-2">주문명 <span class="text-danger">*</span></label>
						<input type="text" class="form-control fs-26px" placeholder="주문명" v-model = "memo"  />
					</div>
					<div class="mb-3">
						<label class="mb-2">금액 <span class="text-danger">*</span></label>
						<input type="text" class="form-control fs-26px" placeholder="금액" v-model = "amount" @input="checkKey()" pattern="[0-9]*"/>
					</div>
					<div class="mb-4">
						<label class="mb-2">휴대폰 번호 <span class="text-danger">*</span></label>
						<input type="text" class="form-control fs-26px" placeholder="휴대폰 번호" v-model = "phoneNo" @input="checkKey()" pattern="[0-9]*"/>
					</div>

					<div class="mb-4">
						<button type="submit" class="btn btn-primary d-block w-100 btn-lg h-45px fs-15px" @click="save($event)">결제 생성</button>
					</div>
					
				
			</div>
			<!-- END register-content -->
		</div>
		<!-- END register-container -->
	</div>
	<!-- END register -->
	
	<div class="position-fixed end-0 top-0 me-5 pt-5 mt-5 toasCenter">
		<div class="toast fade hide mb-3" data-autohide="false" id="toastMsg">
			<div class="toast-header">
				<i class="far fa-bell text-muted me-2"></i>
				<strong class="me-auto">알림</strong>
				<button type="button" class="btn-close" data-bs-dismiss="toast"></button>
			</div>
			<div class="toast-body" v-html="msg">
			</div>
		</div>
	</div>
</template>

<style>
html {font-size: 26px;}
.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}
.toasCenter {
	width: 350px;
  position: fixed;
  bottom: 80%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
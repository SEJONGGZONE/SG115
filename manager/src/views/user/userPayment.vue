<script setup>
import { useAppOptionStore } from "@/stores/app-option";
import { ref, onMounted } from "vue";
import { onBeforeRouteLeave, useRoute } from "vue-router";
import { Toast } from "bootstrap";
import { userApi } from "@/api";

const appOption = useAppOptionStore();
const route = useRoute();

let juminNum = ref(""); //주민번호(6자리)
let cardNum = ref(""); //카드번호(4*4 16자리 또는 15자리)

let cardNum1 = ref("");
let cardNum2 = ref("");
let cardNum3 = ref("");
let cardNum4 = ref("");

const focus1 = ref(null);
const focus2 = ref(null);
const focus3 = ref(null);
const focus4 = ref(null);
const focus5 = ref(null);
const focus6 = ref(null);
const focus7 = ref(null);
const focus8 = ref(null);

let cardPassNum = ref(""); //비밀번호(2자리)
let cardPeriodY = ref(""); //카드 유효기간 년(2자리)
let cardPeriodM = ref(""); //카드 유효기간 월(2자리)
let inputUser = ref(""); //사용자명
let paymentNo = ref("");

let radioValue = ref("6"); //주민번호(6), 사업자번호(10)
let isPayLodaing = ref(false); // 결제 로딩여부

let responseData = ref("");

let msg = ref("");

const setParam = () => {
  paymentNo.value = route.query.PAYMENT_NO;
};

onMounted(() => {
  setParam();
  doSearch();
  appOption.appSidebarHide = true;
  appOption.appHeaderHide = true;
  appOption.appContentClass = "p-0";
});
onBeforeRouteLeave((to, from, next) => {
  appOption.appSidebarHide = false;
  appOption.appHeaderHide = false;
  appOption.appContentClass = "";
  next();
});
const checkForm = (e) => {
  e.preventDefault();
  this.$router.push({ path: "/dashboard/v3" });
};

const doSearch = async () => {
  let data;
  const param = {
    paymentNo: paymentNo.value,
  };
  try {
    data = await userApi.userPaymentSearch(param);
    if (data.RecordCount > 0) {
      responseData.value = data.RecordSet[0];
    } else {
      document.getElementById("saveBtn").disabled = true;
      msg.value = "존재하지 않는 화면입니다.<br>관리자에게 문해주세요";
      event.preventDefault();
      toastpop();
      return;
    }
  } catch (error) {
    console.error(error);
  }
};

const checkKey = (length, obj, txt) => {
  /*
	카드번호 input박스 4개로 설정할 경우
	if(obj==='cardNum1'){
		cardNum1.value = cardNum1.value.replace(/[^0-9]/g, '').slice(0, length);
		
		if(cardNum1.value.toString().length >= length){
			focus2.value.focus();
			return;
		}
	}else if(obj==='cardNum2'){
		cardNum2.value = cardNum2.value.replace(/[^0-9]/g, '').slice(0, length);

		if(cardNum2.value.toString().length >= length){
			focus3.value.focus();
			return;
		}
	}else if(obj==='cardNum3'){
		cardNum3.value = cardNum3.value.replace(/[^0-9]/g, '').slice(0, length);

		if(cardNum3.value.toString().length >= length){
			focus4.value.focus();
			return;
		}
	}else if(obj==='cardNum4'){
		cardNum4.value = cardNum4.value.replace(/[^0-9]/g, '');

		if(cardNum4.value.toString().length >= length){
			focus5.value.focus();
			return;
		}
	}
	*/

  if (obj === "cardNum") {
    cardNum.value = cardNum.value.replace(/[^0-9]/g, ""); //숫자만

    if (cardNum.value.toString().length >= 16) {
      cardNum.value = cardNum.value.replace(
        /(.{4})(.{4})(.{4})(.{4})/,
        "$1-$2-$3-$4"
      ); //16글자되면 4-4-4-4로 표현
    }
  } else if (obj === "cardPeriodM") {
    cardPeriodM.value = cardPeriodM.value.replace(/[^0-9]/g, "");

    if (cardPeriodM.value.toString().length >= length) {
      focus6.value.focus();
      return;
    }
  } else if (obj === "cardPeriodY") {
    cardPeriodY.value = cardPeriodY.value.replace(/[^0-9]/g, "");

    if (cardPeriodY.value.toString().length >= length) {
      focus7.value.focus();
      return;
    }
  } else if (obj === "cardPassNum") {
    cardPassNum.value = cardPassNum.value.replace(/[^0-9]/g, "");

    if (cardPassNum.value.toString().length >= length) {
      focus8.value.focus();
      return;
    }
  } else if (obj === "juminNum") {
    juminNum.value = juminNum.value
      .replace(/[^0-9]/g, "")
      .slice(0, radioValue.value);
  }
};

const save = (event, target) => {
  //validation check
  let chk = false;
  let chkId = "";
  let js = "";

  /*if(!cardNum1.value || !cardNum2.value || !cardNum3.value ||!cardNum4.value){
		chk = true 
		chkId = `${chkId} 카드번호`
	}*/

  if (!cardNum.value) {
    chk = true;
    chkId = `${chkId} 카드번호`;
  }
  if (!cardPeriodY.value || cardPeriodY.value.length < 2) {
    chk = true;
    chkId = !chkId ? `유효기간(월)` : `${chkId}, 유효기간(년)`;
  }
  if (!cardPeriodM.value || cardPeriodM.value.length < 2) {
    chk = true;
    chkId = !chkId ? `유효기간(월)` : `${chkId}, 유효기간(월)`;
  }
  if (!cardPassNum.value || cardPassNum.value.length < 2) {
    chk = true;
    chkId = !chkId ? `비밀번호` : `${chkId}, 비밀번호`;
  }
  if (!juminNum.value || juminNum.value.length < radioValue.value) {
    chk = true;
    js = radioValue.value == 6 ? "주민번호" : "사업자번호";
    chkId = !chkId ? js : chkId + ", " + js;
  }

  if (chk) {
    msg.value = `필수입력 항목을 확인해 주세요..<br>(${chkId})`;
    event.preventDefault();
    toastpop();
    return;
  }
  doSave();
};

const toastpop = () => {
  var toast = new Toast(document.getElementById("toastMsg"));
  toast.show();
};

const doSave = async () => {
  isPayLodaing.value = true;
  let data;
  const param = {
    paymentNo: paymentNo.value,
    cardNum: cardNum.value.replace(/-/gi, ""),
    //cardNum : cardNum1.value + cardNum2.value + cardNum3.value + cardNum4.value,
    cardPeriodY: cardPeriodY.value,
    cardPeriodM: cardPeriodM.value,
    cardPassNum: cardPassNum.value,
    inputUser: responseData.value.PHONE_NO,
    juminNum: juminNum.value,
  };
  console.log("param", param);
  try {
    data = await userApi.userPaymentSave(param);
    const resultData = JSON.parse(data.RecordSet[0].RESULT_DATA);

    if (resultData?.message) {
      //결제실패
      msg.value = resultData?.message;
      toastpop();
      isPayLodaing.value = false;
    } else {
      setTimeout(() => {
        const url = resultData?.receipt?.url;
        location.href = url;
      }, 5000);
    }
  } catch (error) {
    console.error(error);
  }
};
const radioChange = () => {
  juminNum.value = juminNum.value.slice(0, radioValue.value);
};
</script>
<template>
  <!-- BEGIN register -->
  <div
    class="register register-with-news-feed"
    :class="isPayLodaing ? 'dimmed' : ''"
  >
    <!-- END news-feed -->

    <!-- BEGIN register-container -->
    <div class="register-container" style="margin: auto">
      <!-- BEGIN register-header -->
      <div class="register-header mb-25px h1">
        <div class="mb-1">결제확인</div>
        <small class="d-block fs-15px lh-16"
          >사용자의 결제 정보를 입력해주세요..</small
        >
      </div>
      <!-- END register-header -->

      <!-- BEGIN register-content -->
      <div class="register-content">
        <!--
					<div class="mb-3">
						<label class="mb-2">주문번호</label>
						<input type="text" class="form-control fs-26px" disabled />
					</div>
					-->
        <div class="mb-3">
          <label class="mb-2">상호명</label>
          <input
            type="text"
            class="form-control fs-26px"
            v-model="responseData.CLNAME"
            disabled
          />
        </div>
        <div class="mb-3">
          <label class="mb-2">주문명</label>
          <input
            type="text"
            class="form-control fs-26px"
            v-model="responseData.ORDERNAME"
            disabled
          />
        </div>
        <div class="mb-3">
          <label class="mb-2">금액</label>
          <input
            type="text"
            class="form-control fs-26px"
            v-model="responseData.TOTALAMOUNT"
            disabled
          />
        </div>
        <!--
					<div class="mb-3">
						<label class="mb-2">카드번호<span class="text-danger">*</span></label>
						<div class="row gx-3">
							<div class="col-md-6 mb-2 mb-md-0" style = "width: 25%;">
								<input type="text" class="form-control fs-26px" style="padding-left: 5px; padding-right: 5px;" ref="focus1" v-model = "cardNum1" @input="checkKey(4, 'cardNum1', cardNum1)" pattern="[0-9]*"/>
							</div>
							<div class="col-md-6" style = "width: 25%;">
								<input type="text" class="form-control fs-26px" style="padding-left: 5px; padding-right: 5px;" ref="focus2" v-model = "cardNum2" @input="checkKey(4, 'cardNum2', cardNum2)" pattern="[0-9]*"/>
							</div>
							<div class="col-md-6" style = "width: 25%;">
								<input type="text" class="form-control fs-26px" style="padding-left: 5px; padding-right: 5px;" ref="focus3" v-model = "cardNum3" @input="checkKey(4, 'cardNum3', cardNum3)" pattern="[0-9]*"/>
							</div>
							<div class="col-md-6" style = "width: 25%;">
								<input type="text" class="form-control fs-26px" style="padding-left: 5px; padding-right: 5px;" ref="focus4" v-model = "cardNum4" @input="checkKey(4, 'cardNum4', cardNum4)" pattern="[0-9]*"/>
							</div>
						</div>
					</div>
					-->

        <div class="mb-3">
          <label class="mb-2"
            >카드번호 <span class="text-danger">*</span></label
          >
          <input
            type="tel"
            class="form-control fs-26px"
            placeholder="카드번호"
            v-model="cardNum"
            @input="checkKey(16, 'cardNum', cardNum)"
            pattern="[0-9]*"
          />
        </div>

        <div class="mb-3">
          <label class="mb-2"
            >유효기간 <span class="text-danger">*</span></label
          >
          <div class="row gx-3">
            <div class="col-md-6 mb-2 mb-md-0" style="width: 50%">
              <input
                type="tel"
                class="form-control fs-26px"
                placeholder="월(mm)"
                maxlength="2"
                ref="focus5"
                v-model="cardPeriodM"
                @input="checkKey(2, 'cardPeriodM', cardPeriodM)"
                pattern="[0-9]*"
              />
            </div>
            <div class="col-md-6" style="width: 50%">
              <input
                type="tel"
                class="form-control fs-26px"
                placeholder="년(yy)"
                maxlength="2"
                ref="focus6"
                v-model="cardPeriodY"
                @input="checkKey(2, 'cardPeriodY', cardPeriodY)"
                pattern="[0-9]*"
              />
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="mb-2"
            >비밀번호 <span class="text-danger">*</span></label
          >
          <input
            type="tel"
            class="form-control fs-26px"
            placeholder="비밀번호 앞 2자리"
            maxlength="2"
            ref="focus7"
            v-model="cardPassNum"
            @input="checkKey(2, 'cardPassNum', cardPassNum)"
            pattern="[0-9]*"
          />
        </div>
        <div class="mb-4">
          <label class="mb-2"
            >주민번호 또는 사업자번호 <span class="text-danger">*</span></label
          >

          <div class="col-md-9 pt-2">
            <div
              class="form-check form-check-inline"
              style="margin-bottom: 10px"
            >
              <input
                class="form-check-input"
                type="radio"
                id="radio1"
                value="6"
                v-model="radioValue"
                @change="radioChange()"
                checked
              />
              <label class="form-check-label" for="radio1"
                >주민번호(6자리)</label
              >
            </div>
            <div
              class="form-check form-check-inline"
              style="margin-bottom: 10px"
            >
              <input
                class="form-check-input"
                type="radio"
                id="radio2"
                value="10"
                v-model="radioValue"
                @change="radioChange()"
              />
              <label class="form-check-label" for="radio2"
                >사업자번호(10자리)</label
              >
            </div>
          </div>

          <input
            type="tel"
            class="form-control fs-26px"
            placeholder="주민번호 앞 6자리 or 사업자번호"
            maxlength="10"
            ref="focus8"
            v-model="juminNum"
            @input="checkKey(10, 'juminNum', juminNum)"
            pattern="[0-9]*"
          />
        </div>

        <div class="mb-4">
          <button
            type="submit"
            class="btn btn-primary d-block w-100 btn-lg h-75px fs-26px"
            id="saveBtn"
            @click="save($event, 'toastMsg')"
          >
            결제 요청
          </button>
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
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="toast"
        ></button>
      </div>
      <div class="toast-body" v-html="msg"></div>
    </div>
  </div>

  <div class="fa-3x progressbar" v-if="isPayLodaing">
    <i class="fas fa-spinner fa-pulse"></i>
  </div>
</template>

<style>
html {
  font-size: 26px;
}
.App {
  font-size: 30px; /* 폰트 크기 조절 */
}
.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}
.progressbar {
  position: fixed;
  bottom: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.toasCenter {
  width: 350px;
  position: fixed;
  bottom: 80%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
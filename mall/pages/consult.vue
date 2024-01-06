<template>
  <layout>

    <section class="product__area pt-90 pb-100">
      <div class="custom-container">
        <div class="row">
          <div class="col-xl-2"></div>
          <div class="col-xl-8">
            
            <div class="section__title-wrapper text-center mb-20 p-relative">
              <div class="section__title mb-10">
                <h2 class="grey-bg" style="font-size:30px;line-height: 1.2;">상담신청서</h2>
              </div>
            </div>
            <!-- <div class="mt-50">
              <p style="text-align: center;font-size: 15px;color: #393939;line-height: 2;">
                (주)쿡짱몰에서는 사전 검증된 신뢰할 수 있는 해당 분야의 전문가님들과 상생의 플랫폼 연계 서비스를 하고 있습니다.<br/>
                아래 상담 내용을 입력 한 후 상담신청하기 버튼을 클릭하면 해당 분야의 전문가님께 문자 메시지가 전송됩니다.<br/>이후 편하게 상담 받으시면 됩니다.<br/>
              </p>  
            </div> -->
            <div class="mt-50">
              <p style="text-align: center;font-size:2rem;color: #393939;line-height: 2;">
                아래 내용입력후 편하게 상담 받으시면 됩니다.<br/>
              </p>  
            </div>
            <!-- <div class="row mt-50">
              <div class="col-xl-2"></div>
              <div class="col-xl-3 text-center"><img :src="companyImg" style="width: 50%;"/></div>
              <div class="col-xl-7 d-flex align-items-center">
                <span>
                  상호 : 한빛텍스 세무법인<br/>
                  연락처 : 043-710-7000<br/>
                  주소 : 충청북도 청주시 흥덕구 사운로 186(문천동), 6층<br/>
                </span>
              </div>
            </div> -->
            <div class="row mt-50">
                <div class="col-xl-2"></div>
                <div class="col-xl-8">
                  <div class="checkout-form-list" style="margin-bottom: 20px;">
                    <label>상담자명</label>
                    <input type="text" v-model="name"/>
                  </div>
                  <div class="checkout-form-list" style="margin-bottom: 20px;">
                    <label>휴대전화</label>
                    <input type="text" v-model="phoneNo" inputmode="numeric" pattern="[0-9]*" @input="checkKey()"/>
                  </div>
                  <div class="checkout-form-list" style="margin-bottom: 20px;">
                    <label>상담내용</label>
                    <textarea v-model="memo" style="width: 100%; height: 150px; border:1px solid #eaedff ;"></textarea>
                  </div>
                  <button class="os-btn os-btn-black w-100" @click="submit()" style="font-size:20px;font-weight:500;">상담 신청</button>
                </div>
                  
            </div>
            

          </div>
          <div class="col-xl-2"></div>
        </div>
      </div>
    </section>
  </layout>
</template>

<script setup lang="ts">

import Layout from "@/layout/LayoutFour.vue";
import * as mainApi from '@/api';
import { useUserStore } from '@/store/useUser';
import * as common_utils from "@/common/utils.ts";

const router = useRouter()

//로그인 정보
const store = useUserStore();
const nameS = store.getUserInfo?.NAME;
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const companyNameS = store.getUserInfo?.COMPANY_NAME;
const loginType = store.getUserInfo?.TYPE;//10-일반사용자(장바구니 사용가능), 20-사업자(관심상품, 장바구니 사용가능)
const isLogin = computed(()=>{
    return store.isLogin
})

const name = ref()//상담자명
const phoneNo = ref()//휴대전화
const memo = ref()//상담내용

const checkKey=() =>{  
  phoneNo.value = String(phoneNo.value).replace(/[^0-9]/g, '');//핸드폰번호
}

const getCurrentDate=() => {
  const currentDate = new Date();
  const year = currentDate.getFullYear();
  const month = String(currentDate.getMonth() + 1).padStart(2, '0');
  const day = String(currentDate.getDate()).padStart(2, '0');
  
  return `${year}-${month}-${day}`;
}

const submit = async (col)=>{//상담신청 api호출

  let data;
  const formattedDate = getCurrentDate();
  let param = {
          type : loginType ?? "",
          name : name.value ?? "",
          phoneNo : phoneNo.value ?? "",
          memo : memo.value ?? "",
          wsNewdate : formattedDate ?? "",
          wsNewuser : name.value ?? "",
          inputUser : userNoS ?? ""
  }
  try {
    data = await mainApi.main_addConsult(param)
    if(data.ResultCode === '00'){
      common_utils.fxAlert("상담 신청 되었습니다.")
      name.value = ''
      phoneNo.value = ''
      memo.value = ''
    }
  } catch (error) {
    console.error(error);
  }
}

</script>

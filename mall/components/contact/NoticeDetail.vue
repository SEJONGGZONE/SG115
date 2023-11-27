
<script setup lang="ts"> 

import { Field, Form, ErrorMessage } from "vee-validate";
import { useUserStore } from '@/store/useUser';
import * as operateApi from '@/api';
import * as common_utils from "@/common/utils.ts";
    
//로그인 정보
const store = useUserStore();
const route = useRoute();  
const nameS = ref(store.getUserInfo?.NAME);
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const companyNameS = store.getUserInfo?.COMPANY_NAME;
let phoneS = formatPhoneNumber(store.getUserInfo?.PHONE);
const addressS_1 = store.getUserInfo?.ADDRESS1;
const addressS_2 = store.getUserInfo?.ADDRESS2;
const userInfoPostNo = store.getUserInfo?.POST_NO
const loginType = store.getUserInfo?.TYPE;//10-일반사용자(장바구니 사용가능), 20-사업자(관심상품, 장바구니 사용가능)
const username = computed(()=>{
    return companyNameS ? companyNameS+"("+nameS.value+")" : nameS.value
})
const isLogin = computed(()=>{
    return store.isLogin
})

const noticeList = ref([])//공지사항 목록

const currGno = ref('')//현재 게시글의 gno
const currItemRow = ref({})//현재 게시글 row
const prevItemRow = ref({})//이전 게시글 row
const nextItemRow = ref({})//다음 게시글 row
  
/****************************************************************************************** mounted */
onMounted(()=>{
    currGno.value = Number(route.query.gno);   
    doNoticeList();//공지사항 목록   
})

/****************************************************************************************** method */
function formatPhoneNumber(phoneNumber) {// 정규식을 사용하여 3-4-4 형식으로 변환

    if (!phoneNumber) {
      return ''; // null 또는 undefined인 경우 빈 문자열 반환
    }

    const cleaned = phoneNumber.replace(/\D/g, '');// 숫자를 제외한 모든 문자 제거
    const regex = /^(\d{3})(\d{4})(\d{4})$/;
    const parts = cleaned.match(regex);

    if (parts) {
      return `${parts[1]}-${parts[2]}-${parts[3]}`;// 변환된 형식으로 전화번호 반환
    }

    return phoneNumber;// 변환에 실패한 경우 원본 전화번호 반환
}

//이전, 현재, 다음 게시글 데이터 셋팅
const setDataRow=(gno, pb)=>{
  
  if(!gno) {//이전글 다음글이 없는 경우
    let pbname = pb === 'prev' ? '이전글' : '다음글'
    common_utils.fxAlert(pbname+'이 없습니다.')
    return
  }
  const currentIndex = noticeList.value.findIndex((item) => item.GNO === gno);//현재 게시글 row

  // 현재글
  currItemRow.value = noticeList.value[currentIndex];
  // 이전글
  prevItemRow.value = (currentIndex > 0) ? noticeList.value[currentIndex - 1] : null;
  // 다음글
  nextItemRow.value = (currentIndex < noticeList.value.length - 1) ? noticeList.value[currentIndex + 1] : null;
  
}

//목록 버튼
const router = useRouter()
const goList=()=>{
  router.push(`/noticeList`);//질문답변 화면으로 이동
}

/****************************************************************************************** api호출 */
const doNoticeList = async ()=>{//공지사항목록 가져오기
    noticeList.value = []

      let data;
      let param = { 
    }
      try {
        data = await operateApi.operate_operateNoticeSel(param)
        if(data.RecordCount > 0){
          noticeList.value.push(...data.RecordSet);
        }
      } catch (error) {
        console.error(error);
      }finally{
        setDataRow(currGno.value,'')
      }
}

</script>

<template>
  <div class="brand__area pb-60 pt-100"> 
    <div class="container custom-container-2 cus-mtop">
      <!--화면명-->
      <div class="section__title-wrapper text-center mb-55 p-relative">
          <div class="section__title mb-10">
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">공지사항 상세</h2>
          </div>
      </div>
      <!--화면내용-->
      <div class="basic-login pt-20 pb-20">
        <!--내용_테이블영역-->
        <div>
            <!--제목-->
            <div class="mb-10 line-2"></div>
            <div class="mb-10">
              <h3>{{ currItemRow?.TITLE }}</h3>
            </div>
            <div class="mb-10 line-1"></div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>작성자 : 관리자</h5>
                </div>
                <div class="col-12 col-md-auto" style="margin-left: auto;">
                    <h5>작성일 : {{ currItemRow?.DATETIME }}</h5>
                </div>
            </div>
            
            
            <!--내용-->
            <div class="mb-10" style="height: 500px; white-space: pre-line; overflow: auto;" v-html="currItemRow?.MSG">
            </div>
            <div class="mb-10 line-2"></div>
            <div class="mb-10 cursor-pointer" @click="setDataRow(prevItemRow?.GNO,'prev')">
              이전글 : {{ prevItemRow?.TITLE || "이전글이 없습니다."}}
            </div>
            <div class="mb-10 line-1"></div>
            <div class="mb-10 cursor-pointer" @click="setDataRow(nextItemRow?.GNO,'next')">
              다음글 : {{ nextItemRow?.TITLE || "다음글이 없습니다."}}
            </div>
            <div class="mb-10 line-2"></div>
        </div>
        <div class="row nav mb-20 justify-content-end" id="nav-tab" role="tablist">  
          <button  id="nav-home-tab" class="nav-link btn-primary active" @click="goList()" style="font-size:18px;font-weight:500; width:auto;margin-right:10px;">
            <i class="fas fa-list-ul"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;목록</span>
          </button> 
        </div>
      </div>
    </div>
  </div>
</template> 

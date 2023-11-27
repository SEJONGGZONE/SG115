
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

const qnaList = ref([])//공지사항 목록
const qnaReplyList = ref([])//댓글 목록

const currsearchKeyword = ref('')
//조회 페이지 정보
const pageNum = ref(1);
const pageSize = ref(1000000);

const currGeonum = ref('')//현재 게시글의 gno
const currItemRow = ref({})//현재 게시글 row
const prevItemRow = ref({})//이전 게시글 row
const nextItemRow = ref({})//다음 게시글 row

//댓글
const memo = ref('')

//작성일
const today = new Date(); // 현재 날짜와 시간을 포함하는 Date 객체 생성
const year = today.getFullYear(); // 년도 가져오기
const month = today.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
const day = today.getDate(); // 일 가져오기
const formattedToday = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
  
/****************************************************************************************** mounted */
onMounted(()=>{
    currGeonum.value = Number(route.query.geonum);   
    currsearchKeyword.value = route.query.searchKeyword;   

    
    doQnaList();//공지사항 목록   
    doQnaReplyList(currGeonum.value);
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
const setDataRow=(geonum, pb, yn)=>{
      
      if(!geonum) {//이전글 다음글이 없는 경우
        let pbname = pb === 'prev' ? '이전글' : '다음글'
        common_utils.fxAlert(pbname+'이 없습니다.')
        return
      }
      const currentIndex = qnaList.value.findIndex((item) => item.GEONUM === geonum);//현재 게시글 row

      // 현재글
      currItemRow.value = qnaList.value[currentIndex];
      // 이전글
      prevItemRow.value = (currentIndex > 0) ? qnaList.value[currentIndex - 1] : null;
      // 다음글
      nextItemRow.value = (currentIndex < qnaList.value.length - 1) ? qnaList.value[currentIndex + 1] : null;
     
      if(yn === 'y'){//이전글 다음글 선택 시에만, 댓글 조회하도록 수정
        doQnaReplyList(geonum)
      }
}

//목록 버튼
const router = useRouter()
const goList=()=>{
      router.push(`/qnaList`);//질문답변 화면으로 이동
}
const goReplySave=()=>{//댓글저장

      if(!memo.value){
        common_utils.fxAlert("내용을 입력해 주세요..")
        return;
      }

      doQnaReplySave();
}

/****************************************************************************************** api호출 */
const doQnaList = async ()=>{//게시판 목록 가져오기

      qnaList.value = []
      let data;
      let param = { 
          geonum : "",
          boardType : "10",
          keyword : currsearchKeyword.value ?? "",
          pageSize : pageSize.value,
          pageNum : pageNum.value,
          refNo : "0",    //메인글
          inputUser : userNoS
      }
      
      try {
        data = await operateApi.operate_operateQnaSel(param)
        if(data.RecordCount > 0){
          qnaList.value.push(...data.RecordSet);
        }
      } catch (error) {
        console.error(error);
      }finally{
        setDataRow(currGeonum.value,'','n');
      }
}

const doQnaReplyList = async (geonum)=>{//현재글의 댓글 가져오기

      qnaReplyList.value = []
      let data;
      
      let param = { 
                geonum : "",
                boardType : "10",
                keyword : "",
                pageSize : pageSize.value,
                pageNum : pageNum.value,
                refNo : geonum,
                inputUser : ""
      }
      
      try {
        data = await operateApi.operate_operateQnaSel(param)
        if(data.RecordCount > 0){
          qnaReplyList.value.push(...data.RecordSet);
        }
      } catch (error) {
        console.error(error);
      }
}

const doQnaReplySave = async ()=>{//댓글저장

      let data;
      let param = { 
                geonum : '',
                type : '10',
                title : '',                       //제목
                memo : memo.value,                //내용
                refNo : currItemRow.value.GEONUM, //WEB_BOAR.GEONUM
                refSeq : '',                      //답글 순번
                date : formattedToday,            //등록일
                user : userNoS,                   //등록자
                inputUser : ''
      }

      try {
        data = await operateApi.operate_operateQnaRefSav(param)
        if(data.ResultCode === '00'){
          doQnaReplyList(currItemRow.value.GEONUM)//댓글 재조회
        }
      } catch (error) {
        console.error(error);
      }finally{
        memo.value = ''
      }
}
const delQnaReply= async(item)=>{//댓글 삭제
      let data;
          let param = { 
                    geonum : item.GEONUM,
                    inputUser : ''
          }

          try {
            data = await operateApi.operate_operateQnaRefDel(param)
            if(data.ResultCode === '00'){
              doQnaReplyList(currItemRow.value.GEONUM)//댓글 재조회
            }
          } catch (error) {
            console.error(error);
          }
}

</script>

<template>
  <div class="brand__area pb-60 pt-100"> 
    <div class="container custom-container-2 cus-mtop">
      <!--화면명-->
      <div class="section__title-wrapper text-center mb-55 p-relative">
          <div class="section__title mb-10">
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">1:1 문의 상세</h2>
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
            <div class="row mb-20">
                <div class="col-12 col-md-6">
                    <h5>작성자 : {{ nameS }}</h5>
                </div>
                <div class="col-12 col-md-auto" style="margin-left: auto;">
                    <h5>작성일 : {{ currItemRow.WS_EDTDATE ?? currItemRow.WS_NEWDATE }}</h5>
                </div>
            </div>
            
            
            <!--내용-->
            <div class="mb-10" style="height: auto; min-height: 300px; max-height: 500px; white-space: pre-line; overflow: auto;" v-html="currItemRow?.MEMO"></div>
            <!--댓글 내용-->
            <div class="mb-20" v-if="qnaReplyList.length > 0" v-for="(item,i) in qnaReplyList" :key="i">
                <div class="mb-20 line-1" style="border-color: #e0d9d9; width: 100%;"></div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="d-block mb-10">
                          <span class="mobile-changLine-display-block">작성자 : {{ item.WS_EDTUSER ? (item.WS_EDTUSER === String(userNoS) ? nameS : item.WS_EDTUSER) : (item.WS_NEWUSER === String(userNoS) ? nameS : item.WS_NEWUSER) }} </span>
                          <span> / 작성일 : {{ item.WS_EDTDATE ?? item.WS_NEWDATE }}</span>
                        </div>
                        <span class="d-block">{{ item.MEMO }}</span>
                    </div>
                    <i @click="delQnaReply(item)" class="fa fa-times"></i>
                </div>
            </div>
            <!--댓글 입력-->
            <div class="d-flex align-items-center mb-30 row">
                <div class="col-12 col-md d-flex align-items-center">
                  <textarea class="flex-grow-1" v-model="memo" style="width: 100%; min-height:50px; max-height: 300px; padding: 5px 5px;"></textarea>
                </div>
                <div class="col-12 col-md-auto">
                  <button class="nav-link btn-primary active" @click="goReplySave()" style="margin-left: auto; width: auto; white-space: nowrap; min-height:50px; max-height: 50px;">
                    <i class="fas fa-pen"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;댓글 작성</span>
                  </button>
                </div>
            </div>

            <!--관리자 답변 있을경우 화면-->
            <!--
            <div class="accordion mb-30" id="accordionPanels1">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                    <button class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelsStayOpen-1" 
                            aria-expanded="true" 
                            aria-controls="panelsStayOpen-1" 
                            style="background-color: #d4d6d6;;color: #323232;">
                            <i class="fas fa-comment"></i>&nbsp;관리자 답변
                    </button>
                  </h2>
                  
                  <div id="panelsStayOpen-1" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                    <div class="accordion-body">
                        <div class="checkout-form-list">
                          안녕 난 관리자야    
                        </div>
                    </div>
                  </div>
                  
                </div>
            </div>
            -->

            <!--하단 영역-->
            <div class="mb-10 line-2"></div>
            <div class="mb-10 cursor-pointer" @click="setDataRow(prevItemRow?.GEONUM,'prev','y')">
              이전글 : {{ prevItemRow?.TITLE || "이전글이 없습니다."}}
            </div>
            <div class="mb-10 line-1"></div>
            <div class="mb-10 cursor-pointer" @click="setDataRow(nextItemRow?.GEONUM,'next','y')">
              다음글 : {{ nextItemRow?.TITLE || "다음글이 없습니다."}}
            </div>
            <div class="mb-10 line-2"></div>
        </div>
        <div class="row nav mb-20 justify-content-end">  
          <button  id="nav-home-tab" class="nav-link btn-primary active" @click="goList()" style="font-size:18px;font-weight:500; width:auto;margin-right:10px;">
            <i class="fas fa-list-ul"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;목록</span>
          </button> 
        </div>
      </div>
    </div>
  </div>
</template> 


<script setup lang="ts"> 

import { Field, Form, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { useUserStore } from '@/store/useUser'; 
import * as operateApi from '@/api';
import * as common_utils from "@/common/utils";
import quillEditor from "@/components/common/others/QuillEditor.vue"; 
    
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

let schema = ref({}) 
//저장
const name = ref('')
const userName = ref('')//작성자
const title = ref('')//제목
const memo = ref('')//내용

//작성일
const today = new Date(); // 현재 날짜와 시간을 포함하는 Date 객체 생성
const year = today.getFullYear(); // 년도 가져오기
const month = today.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
const day = today.getDate(); // 일 가져오기
const formattedToday = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;

  
/****************************************************************************************** mounted */
onMounted(()=>{
  userName.value = nameS.value
  schema.value = yup.object({
    title: yup.string().required("제목을 입력해주세요"),
    //memo: yup.string().required("내용을 입력해주세요"),
  });  

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

//목록 버튼
const router = useRouter()
const goList=()=>{
  router.push(`/qnaList`);//질문답변 화면으로 이동
}

const editConponentData = ref({});
const onEditorReady = (editor) => { 
  editConponentData.value = editor;
};
const onSubmit = (values: object,{resetForm}: {resetForm: () => void})=> {
  
  
  memo.value = editConponentData.value.getHTML()

  if(!memo.value){
    common_utils.fxAlert("내용을 입력해 주세요..")
    return;
  }
  addQna();
}


/****************************************************************************************** api호출 */
const addQna = async()=>{//

      let data;
      let param = { 
        type : '10',               //qna
        title :title.value,         //제목
        memo :memo.value,           //내용
        refNo : 0,                  //WEB_BOAR.GEONUM
        refSeq : 0,                 //답글 순번
        wsNewdate : formattedToday, //최초 작성일
        wsNewuser :userNoS ,        //최초 작성자
        inputUser : userNoS         //등록사용자번호
      }
      try {
        data = await operateApi.operate_operateQnaSav(param)
        if(data.ResultCode === "00"){
            common_utils.fxAlert("저장되었습니다.")
            router.push(`/qnaList`);
        }
      } catch (error) {
        console.error(error);
      }finally{
        
      }
      }


</script>

<template>
  <div class="brand__area pb-60 pt-100"> 
    <div class="container custom-container-2 cus-mtop">
      <!--화면명-->
      <div class="section__title-wrapper text-center mb-55 p-relative">
          <div class="section__title mb-10">
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">1:1 문의 작성</h2>
          </div>
      </div>
      <!--화면내용-->
      <div class="basic-login pt-20 pb-20">
        <!--내용_테이블영역-->
        <div>
          <Form :validation-schema="schema" @submit="onSubmit">
                <div class="outline"><!--일반-->  
                      <div class="row mb-10">
                            <div class="col-sm-12 col-md-6">  
                              <label for="userName" style="font-size:16px;font-weight:500;">작성자</label>
                              <Field name="userName" id="userName" type="text" v-model="userName" readOnly="true" />
                            </div>    
                        
                      </div> 
                      <div class="row mb-10">
                            <div class="col">  
                                <label for="title" style="font-size:16px;font-weight:500;" >제목<span>*</span></label>
                              <Field name="title" id="title" type="text" v-model="title" maxlength="11" placeholder="제목을 입력하세요."/>
                              <ErrorMessage name="title" class="text-danger" />
                            </div>    
                      </div> 
                      <div class="row mb-10">
                            <div class="col-12">
                              <label for="title" style="font-size:16px;font-weight:500;" >내용<span>*</span></label>
                              <quill-editor @onEditorReady="onEditorReady" placeholder="내용을 입력하세요." style="height: 400px;width: 100%; padding: 20px 20px; border: 2px solid #eaedff;"/>
                            </div>
                      </div>
                  </div>
                    
                  <div class="row nav mt-30 mb-20 justify-content-end">  
                    <button  href="#" id="nav-home-tab" class="nav-link btn-primary active"  style="font-size:18px;font-weight:500; width:auto;margin-right:10px;">
                      <i class="fas fa-pen"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;저장</span>
                    </button> 
                    <button  id="nav-home-tab" class="nav-link btn-primary active" @click="goList()" style="font-size:18px;font-weight:500; width:auto;margin-right:10px;">
                      <i class="fas fa-list-ul"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;목록</span>
                    </button> 
                  </div>
            </Form>
        </div>

        
        
      </div>
    </div>
  </div>
</template> 

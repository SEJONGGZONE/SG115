<template>
    <section class="login-area pt-10 pb-100">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 offset-lg-2">
            <div class="basic-login">
              <h3 class="text-center mb-60" style="font-size:34px;font-weight:500;">쿡짱몰 로그인</h3>
              <!-- form start -->
                <!--
                <div class="text-center  pb-20"> 
                  <button type="button" class="btn  btn-lg mr-10 type_button" :class="userType === 20 ? 'btn-primary' : 'btn-secondary' " @click="changeUserType(20)">사업자</button>
                  <button type="button" class="btn  btn-lg type_button"  :class="userType === 10 ? 'btn-primary' : 'btn-secondary'  " @click="changeUserType(10)"  >일반</button> 
                </div>
                -->
              <div class="nav nav-tabs mb-20" id="nav-tab" role="tablist">
                  <button class="nav-link" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-20" type="button" role="tab" aria-controls="nav-home" aria-selected="true"
                          :class="userType === 20 ? 'btn-primary active' : 'btn-secondary' " @click="changeUserType(20)" style="font-size:20px;font-weight:500;">사업자</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-10" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"
                          :class="userType === 10 ? 'btn-primary active' : 'btn-secondary'  " @click="changeUserType(10)" style="font-size:20px;font-weight:500;">일반</button>
              </div>
              
              <Form v-if="userType === 20" :validation-schema="schema20" @submit="onSubmit">
                  <div class="mb-20">
                    <label for="businessNumber" style="font-size:16px;font-weight:500;">사업자번호<span>*</span></label>
                    <Field name="businessNumber" id="businessNumber" type="text" v-model="userEmail" inputmode="numeric" @input="checkKey()" placeholder="사업자번호를 입력해주세요.(-제외)" />
                    <ErrorMessage name="businessNumber" class="text-danger" />
                  </div>
                  <div class="mb-20">
                    <label for="pass" style="font-size:16px;font-weight:500;">비밀번호 <span>*</span></label>
                    <Field name="password" id="pass" type="password" v-model="userPassword" placeholder="비밀번호를 입력해주세요" />
                    <ErrorMessage name="password" class="text-danger" />
                  </div>
                  
                  <div class="login-action mb-20 fix">
                      <span class="log-rem f-left">
                          <input id="remember" type="checkbox" style="height:auto;"  v-model="rememberChk"/>
                          <label for="remember">사업자번호 저장</label>
                      </span>
                      <span class="forgot-login f-right">
                          <a href="#" data-bs-toggle="modal" data-bs-target="#productModalId">비밀번호를 잊으셨나요?</a>
                      </span>
                  </div>
                  <button class="os-btn w-100" style="font-size:20px;font-weight:500;">로그인</button>
                  <div class="or-divide"><span></span></div>
                  <nuxt-link href="/register" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;">
                    회원 가입
                  </nuxt-link>
              </Form>

              <Form v-if="userType === 10" :validation-schema="schema10" @submit="onSubmit">
                  <div class="mb-20">
                    <label for="email-id" style="font-size:16px;font-weight:500;">이메일<span>*</span></label>
                    <Field name="email" id="email-id" type="text" v-model="userEmail" placeholder="이메일을 입력해주세요." @input="checkKey()"/>
                    <ErrorMessage name="email" class="text-danger" />
                  </div>

                  <div class="mb-20">
                    <label for="pass" style="font-size:16px;font-weight:500;">비밀번호 <span>*</span></label>
                    <Field name="password" id="pass" type="password" v-model="userPassword" placeholder="비밀번호를 입력해주세요" />
                    <ErrorMessage name="password" class="text-danger" />
                  </div>
                  
                  <div class="login-action mb-20 fix">
                      <span class="log-rem f-left">
                          <input id="remember" type="checkbox" style="height:auto;"  v-model="rememberChk"/>
                          <label for="remember">이메일 저장</label>
                      </span>
                      <span class="forgot-login f-right">
                          <a href="#" data-bs-toggle="modal" data-bs-target="#productModalId">비밀번호를 잊으셨나요?</a>
                      </span>
                  </div>
                  <button class="os-btn w-100" style="font-size:20px;font-weight:500;">로그인</button>
                  <div class="or-divide"><span></span></div>
                  <nuxt-link href="/register" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;">
                    회원 가입
                  </nuxt-link>
              </Form>
              <!-- form end -->


            </div>
          </div>
        </div>
      </div>
    </section> 
    
    <!--모달1_임시비밀번호 요청 모달-->
    <div class="modal fade" id="productModalId" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
              <div class="product__modal-wrapper p-relative">
                  <div class="product__modal-close p-absolute">
                      <button data-bs-dismiss="modal" id = "modal1CloseButton"><i class="fal fa-times"></i></button>
                  </div>
                  <div class="product__modal-inner">
                    <div class="row">
                      <div class="col-lg-8 offset-lg-2">
                        <div class="product__modal-conten`">
                          <div class="basic-login">
                            <h3 class="text-center mb-60">임시 비밀번호 요청</h3>
                            <Form :validation-schema="schema30" @submit="onRequstTemporaryPassWord">
                                <div class="mb-20">
                                  <label for="fEmail">이메일<span>*</span></label>
                                  <Field name="fEmail" id="fEmail" type="text" v-model="modal1Email" placeholder="회원가입 시 작성한 이메일을 입력해주세요" @input="checkKey()"/>
                                  <ErrorMessage name="fEmail" class="text-danger" />
                                  <br>* 작성한 이메일로 임시비밀번호가 발송됩니다.
                                </div>
                                <div class="mb-20">
                                  <label for="fPhoneNo">휴대폰번호 <span>*</span></label>
                                  <Field name="fPhoneNo" id="fPhoneNo" type="tel" v-model="modal1PhoneNo" inputmode="numeric" maxlength="12" @input="checkKey()" placeholder="회원가입 시 작성한 휴대폰번호를 입력해주세요" />
                                  <ErrorMessage name="fPhoneNo" class="text-danger" />
                                </div>
                                <button class="os-btn w-100" style="font-size:20px;font-weight:500;">임시비밀번호요청</button>
                            </Form>
                          </div>
                        </div>
                      </div>
                    </div>    
                  </div>
              </div>
            </div>
        </div>
    </div>
    <!--모달1 end-->

    <!--모달2_임시비밀번호 및 새 비밀번호 입력 화면-->
    <div class="modal fade" id="productModalId2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
              <div class="product__modal-wrapper p-relative">
                  <div class="product__modal-close p-absolute">
                      <button data-bs-dismiss="modal"><i class="fal fa-times"></i></button>
                  </div>
                  <div class="product__modal-inner">
                    <div class="row">
                      <div class="col-lg-8 offset-lg-2">
                        <div class="product__modal-conten`">
                          <div class="basic-login">
                            <h3 class="text-center mb-60">비밀번호 변경</h3>
                            
                            <Form :validation-schema="schema40" @submit="onResetToNewPassWord">
                                <div class="mb-20">
                                  <label for="TemporaryPassword">임시 비밀번호<span>*</span></label>
                                  <Field name="TemporaryPassword" id="TemporaryPassword" type="text" v-model="TemporaryPassword" placeholder="비밀번호를 입력해주세요." />
                                  <ErrorMessage name="TemporaryPassword" class="text-danger" />
                                </div>
                                <div class="mb-10">
                                  <label for="newPassword1">신규 비밀번호<span>*</span></label>
                                  <Field name="newPassword1" id="newPassword1" type="password" v-model="newPassword1" placeholder="비밀번호를 입력해주세요." />
                                  <ErrorMessage name="newPassword1" class="text-danger" />
                                  <Field name="newPassword2" id="newPassword2" type="password" v-model="newPassword2" placeholder="비밀번호 다시 한번 입력해주세요." />
                                  <ErrorMessage name="newPassword2" class="text-danger" />
                                </div>
                                <button class="os-btn w-100" style="font-size:20px;font-weight:500;">변경요청</button>
                            </Form>
                          </div>
                        </div>
                      </div>
                    </div>    
                  </div>
              </div>
            </div>
        </div>
    </div>
    <!--모달2 end-->
</template>

<script setup> 
import Layout from "@/layout/Layout.vue"; 
import { useUserStore } from '@/store/useUser';
import * as loginApi from '@/api'
import { Field, Form, ErrorMessage } from "vee-validate";
import * as yup from "yup"; 
import * as common_utils from "@/common/utils.ts";

const userType = ref(20) // 20 사업자 , 10 회원 

const rememberChk = ref(false)
const router = useRouter()
const userStore = useUserStore(); 
const userEmail = ref("");
const userPassword = ref("");

//모달1 정보
const modal1Email = ref()
const modal1PhoneNo = ref()

//모달2 정보
const TemporaryPassword = ref()
const newPassword1 = ref()
const newPassword2 = ref()

let schema10 = ref({})
let schema20 = ref({})
let schema30 = ref({})//모달1
let schema40 = ref({})//모달2



/****************************************************************************************** mounted */
// When accessing /posts/1, route.params.id will be 1 
  onMounted(()=>{
    
    rememberEmail();
    
    schema20.value = yup.object({
      businessNumber: yup.string()
        .required('사업자 등록번호를 입력해주세요.')
        .matches(/^\d{10}$|^\d{13}$/, '유효한 사업자 등록번호 형식이 아닙니다.'),
      password: yup.string().required("비밀번호를 입력해주세요"),
    })
    schema10.value = yup.object({
      email: yup.string().required("이메일을 입력해주세요").email("이메일 형식이 맞지 않습니다."),
      password: yup.string().required("비밀번호를 입력해주세요"),
    });     
    schema30.value = yup.object({
      fEmail: yup.string().required("이메일을 입력해주세요").email("이메일 형식이 맞지 않습니다."),
      fPhoneNo: yup.string().required("휴대폰번호를 입력해주세요"),
    }); 
    schema40.value = yup.object({
      newPassword1: yup.string().required("비밀번호를 입력해주세요.")
      .test('is-four-digits', '4자리 수 이상 입력해주세요.',  value => String(value).length >= 4),
      newPassword2: yup.string().required("비밀번호(검증)를 입력해주세요.")
      .test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4)
      .oneOf([yup.ref("newPassword1")], "비밀번호가 일치하지 않습니다."),
    }); 
  })   
/****************************************************************************************** method */
const rememberEmail=() =>{

  const saveYn = localStorage.getItem("LOGIN_SAVE_YN");
  const memeberId = localStorage.getItem("LOGIN_ID");

  if(saveYn === 'Y'){
    if((memeberId.includes('@') && userType.value === 10) || (!memeberId.includes('@') && userType.value === 20)){
      rememberChk.value = true
      userEmail.value = memeberId
    }
  }
}
const checkKey=() =>{  
  if(userType.value === 20){
    userEmail.value = userEmail.value.replace(/[^0-9]/g, '');//사업자의 경우만 숫자입력만 가능
  }else{
    userEmail.value = userEmail.value.replace(/[ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');//일반의 경우만 한글 입력 제한
  }
  
  modal1PhoneNo.value = String(modal1PhoneNo.value).replace(/[^0-9]/g, '');//모달1_핸드폰번호
  modal1Email.value = String(modal1Email.value).replace(/[ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');//모달1_이메일
}
const onSubmit = (values,{resetForm})=> { //로그인 버튼 클릭
  clickLogin() 
} 
const onRequstTemporaryPassWord = ()=> { //임시 비밀번호 요청 모달
  requstTemporaryPassWord()
} 
const onResetToNewPassWord = () =>{//새 비밀번호 입력 모달
  resetToNewPassWord()
}
const changeUserType = (type)=>{ //사업자, 일반 탭변경
  userEmail.value = ''
  userPassword.value = ''
  userType.value = type
  rememberChk.value = false
  
  rememberEmail();
}
/****************************************************************************************** api호출 */
let userInfo = ''
const clickLogin = async ()=>{//로그인 요청

  let param = {
    userId : userEmail.value,
    userPw : userPassword.value,
    userType : userType.value
  }
  
  let dataObj = await loginApi.login_executeLogin(param) 
  let data = dataObj.data
    if (data.RecordSet.length > 0) {
      userInfo = data.RecordSet[0];
      if (userInfo.RET_CODE == "100") { //------------------------------------ 로그인 성공 
        
          if(userInfo.STATUS === '10'){
            common_utils.fxAlertOk("승인 대기중입니다.").then((result)=>{
              console.log(result)
            }) 
          }else if(userInfo.STATUS === '20'){//정상 
            let localIp = localStorage.getItem("IP")
            loginApi.memeber_webHistoryUserInfo({ip:localIp})
            localStorage.setItem("LOGIN_SAVE_YN" , rememberChk.value ? 'Y' : 'N');
            localStorage.setItem("LOGIN_ID",userEmail.value);
            
             userStore.setUserInfo(userInfo);  
              userStore.setIsLogin(true);
              router.push('/');// 정상처리, 페이지이동..
           
          }else if(userInfo.STATUS === '30'){//비밀번호 변경 팝업 호출
            const myModal = new bootstrap.Modal(document.getElementById('productModalId2'));
            myModal.show();// 모달 열기
          }else{
             common_utils.fxAlertOk("유효한 상태코드가 아닙니다.").then((result)=>{
              console.log(result)
            }) 
          }
    } else {//------------------------------------------------------ 로그인 실패
      common_utils.fxAlert(userInfo.RET_MSG)//ex)아이디가 존재하지 않습니다
    }
  }
} 

const requstTemporaryPassWord = async ()=>{//모달1_임시비밀번호 요청 모달

let param = {
  email : modal1Email.value,
  phoneNo : modal1PhoneNo.value,
  password : ''
}
let data = await loginApi.login_resetPassWord(param) 
    if(data.ResultMsg === 'SUCCESS'){
      common_utils.fxAlertOk("작성한 이메일로 임시비밀번호가 발송되었습니다.").then((result)=>{
          document.getElementById('modal1CloseButton').click();//저장 후 모달창 닫기
      }) 
    }
} 
const resetToNewPassWord = async ()=>{//모달2_임시비밀번호 및 신규비밀번호 입력 모달창

let param = {
  email : userInfo.EMAIL,
  phoneNo : userInfo.PHONE,
  password : newPassword1.value
}
let data = await loginApi.login_resetPassWord(param) 
    if(data.ResultMsg === 'SUCCESS'){
        if(data.ResultMsg === 'SUCCESS'){ 
          common_utils.fxAlertOk("비밀번호가 변경되었습니다.").then((result)=>{
            document.getElementById('productModalId2').click();//저장 후 모달창 닫기
            userPassword.value = ''
        }) 
      }
    }
} 
</script>

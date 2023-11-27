
<script setup lang="ts">
import * as loginApi from '@/api'
import { Field, Form, ErrorMessage } from "vee-validate";
import * as common_utils from "@/common/utils.ts";
import { useUserStore } from '@/store/useUser'; 
import * as yup from "yup";

import * as productApi from '@/api';
import beforeSelectimg from "~/assets/img/beforeSelectimg.png";

const router = useRouter()
const userStore = useUserStore();  

//로그인 정보
const store = useUserStore();
const userId = computed(()=>userStore.getUserInfo?.ID);
const nameS = ref(store.getUserInfo?.NAME);
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const companyNameS = store.getUserInfo?.COMPANY_NAME;
const corpImageUrl = store.getUserInfo?.CORP_IMG_URL;
let phoneS = store.getUserInfo?.PHONE;
const addressS_1 = store.getUserInfo?.ADDRESS1;
const addressS_2 = store.getUserInfo?.ADDRESS2;
const userInfoPostNo = store.getUserInfo?.POST_NO
const emailS = store.getUserInfo?.EMAIL
const loginType = computed(()=>userStore.getUserInfo?.TYPE);//10-일반사용자(장바구니 사용가능), 20-사업자(관심상품, 장바구니 사용가능)
console.log(loginType.value)
const username = computed(()=>{
    return companyNameS ? companyNameS+"("+nameS.value+")" : nameS.value
})
const isLogin = computed(()=>{
    return store.isLogin
})

//일반
const name10 = ref('')
const phone10 = ref('')
const email10 = ref('')
const password10_1 = ref('')
const password10_2 = ref('')
const postNo10_1 = ref('')
const address10_1 = ref('')
const address10_2 = ref('')
const email10tf = ref(true);
const password10tf = ref(true);
//사업자
const companyName20 = ref('')//상호명
const name20 = ref('')//대표자
const companyCorpno20 = ref('')//사업자번호
const phone20 = ref('')
const postNo20_1 = ref('') 
const email20 = ref('')
const password20_1 = ref('')
const password20_2 = ref('')
const address20_1 = ref('')
const address20_2 = ref('')
const email20tf = ref(true);
const password20tf = ref(true);
const agree01  = store.getUserInfo?.AGREE_01_YN;
const agree02  = store.getUserInfo?.AGREE_02_YN;
const agree03 = store.getUserInfo?.AGREE_03_YN;
const agreeEmail = store.getUserInfo?.AGREE_EMAIL_YN;
const agreeSms = store.getUserInfo?.AGREE_SMS_YN;
const imgFileNo = ref(store.getUserInfo?.CORP_FILE_NO); 

//스키마
/** 일반  */
const schema = yup.object({
  email10: yup.string().required("이메일을 입력해주세요.").email("이메일 형식이 맞지 않습니다."),
  password10_1: yup.string().required("비밀번호를 입력해주세요.")
  .test('is-four-digits', '4자리 수 이상 입력해주세요.',  value => String(value).length >= 4),
  password10_2: yup.string().required("비밀번호(검증)를 입력해주세요.")
  .test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4)
  .oneOf([yup.ref("password10_1")], "비밀번호가 일치하지 않습니다."),

  name10: yup.string().required("이름을 입력해주세요."),
  phone10: yup.string().required("전화번호를 입력해주세요.").test('is-number', '유효한 숫자를 입력해주세요.', value => !isNaN(value)),
  postNo10_1: yup.string().required("우편번호를 입력해주세요."),
  address10_1: yup.string().required("기본주소룰 입력해주세요."),
  address10_2: yup.string().required("상세주소를 입력해주세요."),
});
/** 사업자  */
const companySchema = yup.object({
  companyCorpno20: yup.string().required("사업자번호를 입력해주세요."),
  password20_1: yup.string().required("비밀번호를 입력해주세요.").test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4),
  password20_2: yup.string().required("비밀번호(검증)를 입력해주세요.").test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4)
  .oneOf([yup.ref("password20_1")], "비밀번호가 일치하지 않습니다."),

  companyName20: yup.string().required("상호명을 입력해주세요."),
  name20: yup.string().required("대표자명을 입력해주세요."),
  phone20: yup.string().required("전화번호를 입력해주세요.").test('is-number', '유효한 숫자를 입력해주세요.', value => !isNaN(Number(value))),
  email20: yup.string().required("이메일를 입력해주세요.").email("이메일 형식이 맞지 않습니다."),
  postNo20_1: yup.string().required("우편번호를 입력해주세요."),
  address20_1: yup.string().required("기본주소룰 입력해주세요."),
  address20_2: yup.string().required("상세주소를 입력해주세요."),
});

/******************************************************************************* onMounted */
 
onMounted(()=>{
  setCustomInfo(); 
})
/******************************************************************************* method */
//로그인한 사용자 '업체정보' 기본 정보 셋팅
const setCustomInfo=()=>{
    if(loginType.value === '10'){
      name10.value = nameS.value
      phone10.value = phoneS
      address10_1.value = addressS_1
      address10_2.value = addressS_2
      postNo10_1.value = userInfoPostNo
    }else{
      companyName20.value = companyNameS
      name20.value = nameS.value
      phone20.value = phoneS
      email20.value = emailS
      address20_1.value = addressS_1
      address20_2.value = addressS_2
      postNo20_1.value = userInfoPostNo
      selectImg.value = corpImageUrl
      imgFileNo.value = store.getUserInfo?.CORP_FILE_NO;
    }
}

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

const findAdress = () =>{//우편번호 찾기
  common_utils.searchAdress().then((result)=>{
    if(loginType.value ==='10'){
        postNo10_1.value = result.zonecode
        address10_1.value = result.roadAddress
      }else{
        postNo20_1.value = result.zonecode
        address20_1.value = result.roadAddress
      }
  })
}
const checkKey=() =>{
  phone10.value = phone10.value.replace(/[^0-9]/g, '');
  phone20.value = phone20.value.replace(/[^0-9]/g, '');
}

/****************************************************************************************** api호출 */
let userInfo = ''
const doRegist = async ()=>{

    let data;
    let param = {
        geonum : userNoS,
        type : loginType.value,
        clcode :loginType.value === '10' ? '' : userId.value ,
        name : loginType.value === '10' ? name10.value : name20.value ,
        phone : loginType.value === '10' ? phone10.value : phone20.value ,
        email : loginType.value === '10' ? userId.value : email20.value ,
        password : loginType.value === '10' ? password10_1.value : password20_1.value ,
        companyName : loginType.value === '10' ? '' : companyName20.value ,
        companyCorpno : loginType.value === '10' ? '' : userId.value ,
        postNo : loginType.value === '10' ? postNo10_1.value : postNo20_1.value ,
        address1 : loginType.value === '10' ? address10_1.value : address20_1.value ,
        address2 : loginType.value === '10' ? address10_2.value : address20_2.value ,
        status : '',
        joinType : '002',
        userId : userId.value,
        agree01 : agree01,
        agree02 : agree02,
        agree03 : agree03,
        agreeEmail : agreeEmail,
        agreeSms : agreeSms,
        corpFileNo : loginType.value === '10'  ? '' : imgFileNo.value ,
    }
    try {
			data = await loginApi.login_excuteRegiste(param)
			if(data.RecordCount > 0){ 
         userInfo = data.RecordSet[0];
         common_utils.fxAlert("회원정보가 수정되었습니다.")
         userStore.setUserInfo(userInfo);  
			}
		} catch (error) {
			console.error(error);
		}finally{
      if(loginType.value ==='10'){
        password10_1.value = ''
        password10_2.value = ''
      }else{
        password20_1.value = ''
        password20_2.value = ''
      }
    }
}
function onSubmit(values: object,{Form}: {Form: () => void}) {  
    if(loginType.value ==='10'){
      doRegist()
    }else{
      doImgSave()
    }
}


/********************************************** 이미지 업로드관련 */
let selectImg = ref("")
const openFile = (file) =>{	//파일 정보
		var fileObject = document.getElementById("selectFile")
		fileObject.click()
}
const changeFile= (e) =>{	
		
		if(e.target.files[0]) {// 인풋 태그에 파일이 있는 경우
			// 이미지 파일인지 검사 (생략)
			// FileReader 인스턴스 생성
			const reader = new FileReader()
			
			reader.onload = e => {// 이미지가 로드가 된 경우
				const previewImage = document.getElementById("showImg")
				previewImage.src = e.target.result
			}
			
			reader.readAsDataURL(e.target.files[0])// reader가 이미지 읽도록 하기
		}
}
const doImgSave = async () => {//대표이미지 저장
		
		let files = document.querySelector("#selectFile").files;
		if(files.length === 0) {
      doRegist(); //이미지가 없으면 그냥 회원정보 저장
      return 
    }

		let formData = new FormData();
		formData.append("files", files[0]); 
		const fileFormat = files[0].type?.split('/')?.[1]
    const fileType = files[0].name.split(".")[1]

		document.getElementById("selectFile")
		var d = new Date();
		const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0'); 
    const hours = String(d.getHours()).padStart(2, '0');           // 시
    const minutes = String(d.getMinutes()).padStart(2, '0');        // 분
    const seconds = String(d.getSeconds()).padStart(2, '0');        // 초

    // 난수 출력 (3자리로 표시하기 위해 문자열로 변환)
    const randomNumber = Math.floor(Math.random() * 1000); 
    const randomString = randomNumber.toString().padStart(3, '0');
		
		//let fileName = `I_${companyCorpno20.value}_${year}${month}${day}${hours}${minutes}${seconds}_${randomString}.${fileType}` //사업자 이미지 번호 (I_3202200508_20230901) 
		let fileName = `I_${loginType.value}_${year}${month}${day}${hours}${minutes}${seconds}_${randomString}` //사업자 이미지 번호 (I_3202200508_20230901) 
    let url =  `/join_web/${fileFormat}/${false}/${fileName}`
		const data = await productApi.product_fileUpload(formData,url)
		console.log("data:::",data,"//fileName:::",fileName,"//url:::",url);

		if(data.data.ResultCode === '00'){ 
			//showAlertSuccess("저장되었습니다.")
			imgFileNo.value = data.data.fileDetails[0].FileNo
			await doRegist(); 
		}else{
			common_utils.fxAlert("통신 이슈로 인해 저장되지 않았습니다. 재시도 바랍니다.")
		}
};
</script>

<template>
  <div class="brand__area pb-60 pt-90"> 
    <div class="container custom-container-2 cus-mtop">
        <!--화면명-->
        <div class="section__title-wrapper text-center mb-55 p-relative">
            <div class="section__title mb-10">
                <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">회원 정보 수정</h2>
            </div>
        </div>
        <div class="row">
          <div class="col-lg-6 offset-lg-3">
            <!--화면내용-->
            <div class="basic-login pt-20 pb-20">
              <!--사업자/일반 영역-->
              <div class="mb-2">
                <div class="form-check form-inline" style="padding-left: 0;"> 
                  <div class="row nav mb-20" id="nav-tab" role="tablist">
                    <div class="col-12">
                      <div class="nav nav-tabs mb-20" id="nav-tab" role="tablist"> 
                        <button  class="nav-link" type="button"
                                :class="loginType === '20' ? 'btn-primary active' : 'btn-secondary'" style="font-size:20px;font-weight:500;cursor:auto;">사업자</button>
                        <button  class="nav-link" type="button"
                                :class="loginType === '10' ? 'btn-primary active' : 'btn-secondary'" style="font-size:20px;font-weight:500;cursor:auto;">일반</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!--상세 입력-->
              <div>
                
                <!---------------------일반-->
                <Form  v-if="loginType === '10'" :validation-schema="schema" @submit="onSubmit">
                    <div>
                        <div class="d-flex justify-content-between align-items-center grey-bg mb-15 py-2 px-3">
                            <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">로그인 정보</h2>
                        </div>
                        <div class="mb-10">
                            <label class="labelTitle" for="email10"  >이메일</label>
                            <div>  
                              <Field name="email10" id="email10" class="addressMax" type="text"  v-model="userId" disabled /> 
                            </div>
                            <ErrorMessage name="email10" class="text-danger" />
                        </div>
                        <div class="row mb-10">
                              <label class="labelTitle" for="password10_1" >비밀번호<span>*</span></label>
                              <div class="col-sm-12 col-md-6">  
                                <Field name="password10_1" id="password10_1" type="password" v-model="password10_1" placeholder="비밀번호를 입력해주세요." />
                                <ErrorMessage name="password10_1" class="text-danger" />
                              </div>
                              <div class="col-sm-12 col-md-6">  
                                <Field name="password10_2" id="password10_2" type="password" v-model="password10_2" placeholder="비밀번호 다시 한번 입력해주세요." />
                                <ErrorMessage name="password10_2" class="text-danger" />
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center grey-bg mb-15 py-2 px-3">
                          <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">배송 정보</h2>
                        </div>
                        <div class="row mb-10">
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="name10" >이름<span>*</span></label>
                              <Field name="name10" id="name10" type="text" v-model="name10" placeholder="이름을 입력하세요." />
                              <ErrorMessage name="name10" class="text-danger" />
                            </div>
                            
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="phone10" >전화번호<span>*</span></label>
                              <Field name="phone10" id="phone10" type="tel" v-model="phone10" placeholder="전화번호를 입력하세요." @input="checkKey()"/>
                              <ErrorMessage name="phone10" class="text-danger" />
                            </div> 
                        </div> 
                          
                        <div class="row mb-2">
                          <div class="col-12">  
                            <label class="labelTitle" for="address10_1" >기본 주소<span>*</span></label>
                            <Field name="postNo10_1" class="postNoMax" id="postNo10_1"  type="text" readOnly="true"   v-model="postNo10_1" placeholder="우편번호" /> 
                            <a class="os-btn os-btn-black " @click="findAdress" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px; padding: 0px 10px;">주소 찾기</a>
                          </div>
                        </div>
                        <div class="row mb-2">
                          <div class="col-12">   
                              <ErrorMessage name="postNo10_1" class="text-danger" /> 
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">     
                              <Field name="address10_1"  id="address10_1"  type="text" readOnly="true"   v-model="address10_1" placeholder="기본 주소 입력해주세요." /> 
                              <ErrorMessage name="address10_1" class="text-danger" />
                            </div>
                            <div class="col-sm-12 col-md-6">  
                                <Field name="address10_2" id="address10_2" type="text" :disabled="!address10_1"  v-model="address10_2" placeholder="상세 주소를 입력해주세요." />
                              <ErrorMessage name="address10_2" class="text-danger" />
                            </div>
                        </div> 
                    </div>
                    <div class="mt-30" style="text-align:center">
                        <button href="#" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;width:300px;max-width: 300px;" >회원정보 수정하기</button>
                    </div>
                  </Form>


                  <!---------------------사업자-->
                    <Form  v-if="loginType === '20'" :validation-schema="companySchema" @submit="onSubmit">
                      <div>
                          <div class="d-flex justify-content-between align-items-center grey-bg mb-15 py-2 px-3">
                            <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">로그인 정보</h2>
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="companyCorpno20" >사업자번호</label>
                            <div>  
                              <Field name="companyCorpno20" class="addressMax" id="companyCorpno20" type="tel" v-model="userId"  disabled /> 
                            </div>
                            <ErrorMessage name="companyCorpno20" class="text-danger" />
                          </div>
                          <div class="row mb-10">    
                              <label class="labelTitle" for="password20_1" >비밀번호<span>*</span></label>
                            <div class="col-sm-12 col-md-6">  
                              <Field name="password20_1" id="password20_1" type="password" v-model="password20_1" placeholder="비밀번호를 입력해주세요." />
                              <ErrorMessage name="password20_1" class="text-danger" />
                            </div>
                            <div class="col-sm-12 col-md-6">  
                              <Field name="password20_2" id="password20_2" type="password" v-model="password20_2" placeholder="비밀번호를 다시 한번 입력해주세요." />
                              <ErrorMessage name="password20_2" class="text-danger" />
                            </div>
                          </div>

                          <div class="d-flex justify-content-between align-items-center grey-bg mb-15 py-2 px-3">
                            <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">업체 정보</h2>
                          </div>
                          <div class="row mb-10">
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="companyName20" >상호명<span>*</span></label>
                              <Field name="companyName20" id="companyName20" type="text" v-model="companyName20" placeholder="상호명을 입력하세요." />
                              <ErrorMessage name="companyName20" class="text-danger" />
                            </div>    
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="name20" >대표자<span>*</span></label>
                              <Field name="name20" id="name20" type="text" v-model="name20" placeholder="대표자를 입력하세요." />
                              <ErrorMessage name="name20" class="text-danger" />
                            </div>
                          </div> 
                          <div class="row mb-10">
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="phone20" >전화번호<span>*</span></label>
                              <Field name="phone20" id="phone20" type="tel" v-model="phone20" placeholder="전화번호를 입력하세요." @input="checkKey()"/>
                              <ErrorMessage name="phone20" class="text-danger" />
                            </div>
                            <div class="col-sm-12 col-md-6">  
                              <label class="labelTitle" for="email20" >이메일<span>*</span></label>
                              <div>  
                                <Field name="email20" id="email20"     type="text" v-model="email20" placeholder="email을 입력하세요." />
                                <!-- <a class="os-btn os-btn-black " @click="chkId('EMAIL')" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">중복 체크</a> -->
                              </div>
                              <ErrorMessage name="email20" class="text-danger" />
                            </div>
                          </div> 
                          <div class="row mb-2">
                            <div class="col-12">  
                                <label class="labelTitle" for="address20_1" >기본 주소<span>*</span></label>
                                <Field name="postNo20_1" class="postNoMax" id="postNo20_1"  type="text" readOnly="true" @click="findAdress" v-model="postNo20_1" placeholder="우편번호" /> 
                                  <a class="os-btn os-btn-black" @click="findAdress" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin: 5px; padding: 1px 10px;">주소 찾기</a>
                            </div> 
                          </div>
                          <div class="row">
                              <div class="col-sm-12 col-md-6">   
                                  <Field name="address20_1"  id="address20_1" type="text" readOnly="true" v-model="address20_1" @click="findAdress" placeholder="기본 주소 입력해주세요." /> 
                                  <ErrorMessage name="address20_1" class="text-danger" />
                              </div> 
                              <div class="col-sm-12 col-md-6">   
                                <Field name="address20_2" id="address20_2" type="text" :disabled="!address20_1" v-model="address20_2" placeholder="상세 주소를 입력해주세요." />
                                <ErrorMessage name="address20_2" class="text-danger" />
                            </div> 
                          </div>
                           <div class="row mb-10">
                            <label class="labelTitle">사업자 등록증</label>
                            <div class="" style="display: flex; align-items: flex-end;">
                              <img id="showImg" :src="selectImg ? selectImg : beforeSelectimg" class="rounded w-25">
                              <input type="file" v-show="false" id="selectFile" multiple accept=".jpg, .jpeg, .png" @change="changeFile"/>
                              <a href="javascript:void(0);" style="z-index: 1052 !important" class="btn btn-primary btn ml-10" @click.prevent="openFile">찾아보기</a>
                            </div>
                          </div>
                      </div>
                      <div class="mt-30" style="text-align:center">
                        <button href="#" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;width:300px;max-width: 300px;" >회원정보 수정하기</button>
                      </div>
                  </Form>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>    
</template>
<style>

.addressMax{
  max-width: 397px;
}
.postNoMax{
  max-width: 150px;
}
.nav-link:hover {
  /* 호버 효과 없음 */
  border-color: #222222 !important;
}
</style>



<script setup lang="ts">
import * as loginApi from '@/api'
import * as productApi from '@/api';
import { Field, Form, ErrorMessage } from "vee-validate";
import * as common_utils from "@/common/utils.ts";

import RegisterAgree from "@/components/login-register/RegisterAgree.vue";
import beforeSelectimg from "~/assets/img/beforeSelectimg.png";

// import { loadScript } from "vue-plugin-load-script"
import * as yup from "yup";
const router = useRouter()

let msg = ref('')

let radioValue = ref('20')//일반(10), 사업자(20)
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

//저장을 위한 인증 상태값 (이메일 , 사업자번호 인증)
const isSaveCertification = computed(()=>{
  if(radioValue.value === '10'){
    return isEmailCertification.value
  }else{
    return isCertification.value
  }
})
const isEmailCertification = ref(false) //이메일 인증
const isCertification = ref(false) //사업자 인증 

const radioChange = (type) =>{
    isEmailCertification.value = false
    isCertification.value = false
    radioValue.value = type
    if(radioValue.value==='10'){//일반
      companyName20.value = ''
      name20.value = ''
      companyCorpno20
      phone20.value = ''
      email20.value = ''
      postNo20_1.value = ''
      password20_1.value = ''
      password20_2.value = ''
      address20_1.value = ''
      address20_2.value = ''
      email20tf.value = true
      password20tf.value = true
    }else{
      postNo10_1.value = ''
      name10.value = ''
      phone10.value = ''
      email10.value = ''
      password10_1.value = ''
      password10_2.value = ''
      address10_1.value = ''
      address10_2.value = ''
      email10tf.value = true
      password10tf.value = true
    }
}

const findAdress = () =>{
  common_utils.searchAdress().then((result)=>{
    if(radioValue.value==='10'){
        postNo10_1.value = result.zonecode
        address10_1.value = result.roadAddress
      }else{
        postNo20_1.value = result.zonecode
        address20_1.value = result.roadAddress
      }
  })
}
const agreeYN = ref(false)
let chkValue2 = ''
let chkValue3 = ''
let chkValue4 = ''
let chkValue5 = ''
let chkValue6 = ''
let registerAgree=(emitParam)=>{

  chkValue2 = emitParam.chkValue_2
  chkValue3 = emitParam.chkValue_3
  chkValue4 = emitParam.chkValue_4
  chkValue5 = emitParam.chkValue_5
  chkValue6 = emitParam.chkValue_6
  console.log("emitParam:::",emitParam);
  
  
  agreeYN.value = emitParam.nextTF
}
/****************************************************************************************** api호출 */
/**
 * params : type : EMAIL , CORPNO
 */
const chkId = async (type) =>{
  let data = ''
  if(type === 'EMAIL'){
    if(radioValue.value === '10'){
      data = email10.value
    }else{
      data = email20.value
    }
  }else{
    data = companyCorpno20.value
  }
  if(!data){
    common_utils.fxAlert("입력된 데이터가 없습니다.",{type:'info'})
    return
  }
  
  let params = {
    type,
    data
  }
  const result =  await loginApi.member_chkEmailAndBuisinessNumber(params)
  if(result.ResultCode === '00'){
    if(result.RecordSet[0].CHK_RESULT === 'F'){
      if(type === 'EMAIL'){
        common_utils.fxAlert("중복된 이메일입니다.",{type:'info'})
      }else{
        common_utils.fxAlert("중복된 사업자입니다.",{type:'info'})
      }
    }else{
      common_utils.fxAlert("중복 체크가 완료되었습니다.")
      if(type === 'EMAIL'){ 
        isEmailCertification.value = true
      }else{ 
        isCertification.value = true
      }
    }
  }else{
    common_utils.fxAlert("중복체크 검증이 실패했습니다.</br>다시 시도해주세요.",{type:'info'})
  }
  
  console.log(data)
  //isCertification
}
const doRegist = async ()=>{

    let data;
    let param = {
        geonum : '',
        type : radioValue.value,
        clcode :radioValue.value === '10' ? '' : companyCorpno20.value ,
        name : radioValue.value === '10' ? name10.value : name20.value ,
        phone : radioValue.value === '10' ? phone10.value : phone20.value ,
        email : radioValue.value === '10' ? email10.value : email20.value ,
        password : radioValue.value === '10' ? password10_1.value : password20_1.value ,
        companyName : radioValue.value === '10' ? '' : companyName20.value ,
        companyCorpno : radioValue.value === '10' ? '' : companyCorpno20.value ,
        postNo : radioValue.value === '10' ? postNo10_1.value : postNo20_1.value ,
        address1 : radioValue.value === '10' ? address10_1.value : address20_1.value ,
        address2 : radioValue.value === '10' ? address10_2.value : address20_2.value ,
        status : '10',
        joinType : '001',
        userId : '',
        /**23.09.01 약관동의항목 수신동의 여부, 사업자등록증 이미지 컬럼 추가 */
        agree01 : chkValue2,
        agree02 : chkValue3,
        agree03 : chkValue4,
        agreeEmail : chkValue5,
        agreeSms : chkValue6,
        corpFileNo : radioValue.value === '10' ? '' : imgFileNo.value ,

    }
    try {
			data = await loginApi.login_excuteRegiste(param)
			if(data.RecordCount > 0){ 
         common_utils.fxAlertOk(data.RecordSet[0].RET_MSG).then((result)=>{
          router.push('/login');
        })   
			}
		} catch (error) {
			console.error(error);
		}
}

/** 일반  */
const schema = yup.object({
  name10: yup.string().required("이름을 입력해주세요."),
  phone10: yup.string().required("전화번호를 입력해주세요.").test('is-number', '유효한 숫자를 입력해주세요.', value => !isNaN(value)),
  email10: yup.string().required("이메일을 입력해주세요.").email("이메일 형식이 맞지 않습니다."),
  password10_1: yup.string().required("비밀번호를 입력해주세요.")
  .test('is-four-digits', '4자리 수 이상 입력해주세요.',  value => String(value).length >= 4),
  password10_2: yup.string().required("비밀번호(검증)를 입력해주세요.")
  .test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4)
  .oneOf([yup.ref("password10_1")], "비밀번호가 일치하지 않습니다."),
  address10_1: yup.string().required("기본주소룰 입력해주세요."),
  address10_2: yup.string().required("상세주소를 입력해주세요."),
});

/** 사업자  */
const companySchema = yup.object({
  companyName20: yup.string().required("상호명을 입력해주세요."),
  name20: yup.string().required("대표자명을 입력해주세요."),
  companyCorpno20: yup.string().required("사업자번호를 입력해주세요."),
  phone20: yup.string().required("전화번호를 입력해주세요.").test('is-number', '유효한 숫자를 입력해주세요.', value => !isNaN(Number(value))),
  email20: yup.string().required("이메일를 입력해주세요.").email("이메일 형식이 맞지 않습니다."),
  password20_1: yup.string().required("비밀번호를 입력해주세요.").test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4),
  password20_2: yup.string().required("비밀번호(검증)를 입력해주세요.").test('is-four-digits', '4자리 수 이상 입력해주세요.', value => String(value).length >= 4)
  .oneOf([yup.ref("password20_1")], "비밀번호가 일치하지 않습니다."),
  address20_1: yup.string().required("기본주소룰 입력해주세요."),
  address20_2: yup.string().required("상세주소를 입력해주세요."),
});


function onSubmit(values: object,{resetForm}: {resetForm: () => void}) {
  if(!isSaveCertification.value){
    let alertMsg = ''
    if(radioValue.value === '10'){
      alertMsg = '이메일'
    }else{
      alertMsg = '사업자번호'
    }
    common_utils.fxAlertOk(alertMsg + " 중복 체크 바랍니다.",{type:'info'}).then((result)=>{
      console.log(result)
    }) 
  }else{
    if(radioValue.value === '10'){//일반
      doRegist()
    }else{
      doImgSave()
    }
    
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
let imgFileNo = ref(null);
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
		let fileName = `I_${radioValue.value}_${year}${month}${day}${hours}${minutes}${seconds}_${randomString}` //사업자 이미지 번호 (I_3202200508_20230901) 
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
  <section class="login-area pt-10 pb-60">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="basic-login">
            <!--회원가입 1단계 -- 약관동의 -->
            <div v-if = "!agreeYN">
              <h3 class="text-center mb-40" style="font-size:34px;font-weight:500;">회원가입 약관 동의</h3>
              <RegisterAgree @register = "registerAgree"></RegisterAgree>
            </div>
            <!--회원가입 2단계 -- 회원정보입력 -->
            <div v-else>
              <h3 class="text-center mb-40" style="font-size:34px;font-weight:500;">회원가입</h3>

                <!--사업자/일반 탭 선택-->
                <div class="mb-2">
                  <div class="form-check form-inline" style="padding-left: 0;">
                    <div class="nav nav-tabs mb-20" id="nav-tab" role="tablist">
                      <button class="nav-link" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-20" type="button" role="tab" aria-controls="nav-home" aria-selected="true"
                              :class="radioValue === '20' ? 'btn-primary active' : 'btn-secondary' " @click="radioChange('20')" style="font-size:20px;font-weight:500;">사업자</button>
                      <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-10" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"
                              :class="radioValue === '10' ? 'btn-primary active' : 'btn-secondary'  " @click="radioChange('10')" style="font-size:20px;font-weight:500;">일반</button>
                    </div>
                  </div>
                </div>
                <!--이용약관 동의-->
                
                <!--사업자/일반 회원가입 정보 입력-->
                
                    <!--------------------------------------일반-->
                    <Form  v-if="radioValue === '10'" :validation-schema="schema" @submit="onSubmit">
                        <div>
                              <div class="profile__info-top d-flex justify-content-between align-items-center grey-bg">
                                <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">로그인 정보</h2>
                              </div>
                              <div class="mb-10">
                                <label class="labelTitle" for="email10"  >이메일<span>*</span></label>
                                <div>  
                                <Field name="email10" id="email10" class="addressMax" type="text" v-model="email10" placeholder="이메일을 입력해주세요." />
                                <a class="os-btn os-btn-black " @click="chkId('EMAIL')" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">중복 체크</a>
                                </div>
                                <ErrorMessage name="email10" class="text-danger" />
                              </div>
                              <div class="mb-10">
                                <label class="labelTitle" for="password10_1" >비밀번호<span>*</span></label>
                                <Field name="password10_1" id="password10_1" type="password" v-model="password10_1" placeholder="비밀번호를 입력해주세요." />
                                <ErrorMessage name="password10_1" class="text-danger" />
                                <Field name="password10_2" id="password10_2" type="password" v-model="password10_2" placeholder="비밀번호 다시 한번 입력해주세요." />
                                <ErrorMessage name="password10_2" class="text-danger" />
                              </div>

                            <div class="profile__info-top d-flex justify-content-between align-items-center grey-bg">
                              <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">배송 정보</h2>
                            </div>
                              <div class="mb-10">
                                <label class="labelTitle" for="name10" >이름<span>*</span></label>
                                <Field name="name10" id="name10" type="text" v-model="name10" placeholder="이름을 입력하세요." />
                                <ErrorMessage name="name10" class="text-danger" />
                              </div>
                              <div class="mb-10">
                                <label class="labelTitle" for="phone10" >전화번호<span>*</span></label>
                                <Field name="phone10" id="phone10" type="tel" v-model="phone10" placeholder="전화번호를 입력하세요." />
                                <ErrorMessage name="phone10" class="text-danger" />
                              </div>
                              <div class="mb-10">
                                <label class="labelTitle" for="address10_1" >기본 주소<span>*</span></label>
                                <div>  
                                  <Field name="postNo10_1" class="postNoMax" id="postNo10_1"  type="text" readOnly="true" @click="findAdress" v-model="postNo10_1" placeholder="우편번호" />
                                <a class="os-btn os-btn-black " @click="findAdress" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">주소 찾기</a>
                                </div>
                                  <div>  
                                  <Field name="address10_1" class="addressMax" id="address10_1"  type="text" readOnly="true"   v-model="address10_1" placeholder="기본 주소 입력해주세요." />
                                  </div>
                                  <ErrorMessage name="address10_1" class="text-danger" />
                                  
                                  <Field name="address10_2" id="address10_2" type="text" :disabled="!address10_1"  v-model="address10_2" placeholder="상세 주소를 입력해주세요." />
                                  <ErrorMessage name="address10_2" class="text-danger" />
                              </div>
                          </div>
                          <div class="mt-60">
                            <button href="#" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;">회원가입 요청</button>
                          </div>
                      </Form>


                      <!--------------------------------------사업자-->
                      <Form  v-if="radioValue==='20'" :validation-schema="companySchema" @submit="onSubmit">
                        <div>
                            <div class="profile__info-top d-flex justify-content-between align-items-center grey-bg">
                            <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">로그인 정보</h2>
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="companyCorpno20" >사업자번호<span>*</span></label>
                            <div>  
                              <Field name="companyCorpno20" class="addressMax" id="companyCorpno20" type="tel" v-model="companyCorpno20" placeholder="사업자번호를 입력하세요." />
                                <a class="os-btn os-btn-black " @click="chkId('CORPNO')" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">중복 체크</a>
                            </div>
                            <ErrorMessage name="companyCorpno20" class="text-danger" />
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="password20_1" >비밀번호<span>*</span></label>
                            <Field name="password20_1" id="password20_1" type="password" v-model="password20_1" placeholder="비밀번호를 입력해주세요." />
                            <ErrorMessage name="password20_1" class="text-danger" />
                            <Field name="password20_2" id="password20_2" type="password" v-model="password20_2" placeholder="비밀번호를 다시 한번 입력해주세요." />
                            <ErrorMessage name="password20_2" class="text-danger" />
                          </div>
                          <div class="profile__info-top d-flex justify-content-between align-items-center grey-bg">
                            <h2 class="profile__info-title mx-auto" style="font-size:22px;font-weight:500;">업체 정보</h2>
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="companyName20" >상호명<span>*</span></label>
                            <Field name="companyName20" id="companyName20" type="text" v-model="companyName20" placeholder="상호명을 입력하세요." />
                            <ErrorMessage name="companyName20" class="text-danger" />
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="name20" >대표자<span>*</span></label>
                            <Field name="name20" id="name20" type="text" v-model="name20" placeholder="대표자를 입력하세요." />
                            <ErrorMessage name="name20" class="text-danger" />
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="phone20" >전화번호<span>*</span></label>
                            <Field name="phone20" id="phone20" type="tel" v-model="phone20" placeholder="전화번호를 입력하세요." />
                            <ErrorMessage name="phone20" class="text-danger" />
                          </div>
                          <div class="mb-10">
                              <label class="labelTitle" for="email20" >이메일<span>*</span></label>
                              <div>  
                              <Field name="email20" id="email20"  class="addressMax"  type="text" v-model="email20" placeholder="email을 입력하세요." />
                                <!-- <a class="os-btn os-btn-black " @click="chkId('EMAIL')" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">중복 체크</a> -->
                                </div>
                              <ErrorMessage name="email20" class="text-danger" />
                          </div>
                          <div class="mb-10">
                            <label class="labelTitle" for="address20_1" >기본 주소<span>*</span></label>
                            <div>  
                              <Field name="postNo20_1" class="postNoMax" id="postNo20_1"  type="text" readOnly="true" @click="findAdress" v-model="postNo20_1" placeholder="우편번호" />
                              <a class="os-btn os-btn-black " @click="findAdress" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px;">주소 찾기</a>
                            </div>
                            <div>  
                              <Field name="address20_1" class="addressMax" id="address20_1" type="text" readOnly="true" v-model="address20_1" placeholder="기본 주소 입력해주세요." />
                            </div>
                            <ErrorMessage name="address20_1" class="text-danger" />
                            <Field name="address20_2" id="address20_2" type="text" :disabled="!address20_1" v-model="address20_2" placeholder="상세 주소를 입력해주세요." />
                            <ErrorMessage name="address20_2" class="text-danger" />
                          </div>

                          <div class="mb-10">
                            <label class="labelTitle">사업자 등록증</label>
                            <div class="" style="display: flex; align-items: flex-end;">
                              <img id="showImg" :src="selectImg ? selectImg : beforeSelectimg" class="rounded w-25">
                              <input type="file" v-show="false" id="selectFile" multiple accept=".jpg, .jpeg, .png" @change="changeFile"/>
                              <a href="javascript:void(0);" style="z-index: 1052 !important" class="btn btn-primary btn ml-10" @click.prevent="openFile">찾아보기</a>
                            </div>
                          </div>

                        </div>
                        <div class="mt-60">
                          <button href="#" class="os-btn os-btn-black w-100" style="font-size:20px;font-weight:500;" >회원가입 요청</button>
                        </div>
                    </Form>
            </div>


          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<style>
.basic-login input{
  height: 60px;
}

.addressMax{
  max-width: 397px;
}
.postNoMax{
  max-width: 150px;
}
</style>


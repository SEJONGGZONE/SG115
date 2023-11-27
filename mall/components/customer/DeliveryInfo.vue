
<script setup lang="ts"> 

import { Field, Form, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { useUserStore } from '@/store/useUser';
import * as paymentApi from '@/api';
import * as common_utils from "@/common/utils.ts";
    
//로그인 정보
const store = useUserStore();
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
const shipList = ref([])//배송지 목록
//배송지목록 상세
const nicName = ref('');
const name = ref('');
const phone = ref('');
const postNo = ref('')
const address1 = ref('');
const address2 = ref('');

let schema = ref({})

/****************************************************************************************** mounted */
onMounted(()=>{
  doShipList();//배송지 목록

  schema.value = yup.object({
    nicName: yup.string().required("배송지명을 입력해주세요"),
    name: yup.string().required("이름을 입력해주세요"),
    phone: yup.string().required("전화번호를 입력해주세요").matches(/^\d{10}$|^\d{11}$/, '유효한 전화번호 형식이 아닙니다.'),
    postNo: yup.string().required("우편번호를 입력해주세요"),
    address1: yup.string().required("기본주소를 입력해주세요"),
    //address2: yup.string().required("상세주소를 입력해주세요"),
  });  
})

const checkKey=() =>{
  phone.value = phone.value.replace(/[^0-9]/g, '');
}
/****************************************************************************************** method */

function onSubmit(values: object,{resetForm}: {resetForm: () => void}) {
    /*alert(JSON.stringify(values, null, 2));*/
    //resetForm()
    addShipList()
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

const selectRowData = ref([]);
const clickShiplist=(item) =>{//배송지목록 모달 - 배송지 목록 선택

    selectRowData.value = JSON.parse(JSON.stringify(item));

    nicName.value = selectRowData.value.NICK_NAME
    name.value = selectRowData.value.NAME
    phone.value = selectRowData.value.PHONE
    postNo.value = selectRowData.value.POST_NO
    address1.value = selectRowData.value.ADDRESS1
    address2.value = selectRowData.value.ADDRESS2
}

const findAdress = () =>{
  common_utils.searchAdress().then((result)=>{

    postNo.value = result.zonecode
    address1.value = result.roadAddress
    
  })
}

const newBtn=() =>{//모달_신규배송지추가 버튼
    nicName.value = ''
    name.value = ''
    phone.value = ''
    postNo.value = ''
    address1.value = ''
    address2.value = ''
}

const addShipList=() =>{//배송지목록 모달 - 신규배송지추가->배송지 입력->추가 버튼 클릭

    let chk = false
    let chkId = ''

    if(!nicName.value){
      chk = true
      chkId = `${chkId}배송지명`
    }
    if(!name.value){
      chk = true
      chkId = !chkId? `이름` : `${chkId}, 이름`
    }
    if(!phone.value){
      chk = true
      chkId = !chkId? `전화번호` : `${chkId}, 전화번호`
    }
    if(!postNo.value){
      chk = true
      chkId = !chkId? `우편번호` : `${chkId}, 우편번호`
    }
    if(!address1.value && !address2.value){
      chk = true
      chkId = !chkId? `주소` : `${chkId}, 주소`
    }

    if(chk){
      common_utils.fxAlert(`누락된 항목을 확인해 주세요..<br>(${chkId})`)
      return;
    }
    doAddShipList();
}
const delShipList=async(col) =>{//삭제
    await doDelShipList(col)
}
/****************************************************************************************** api호출 */
const doDelShipList = async(col)=>{//배송지목록 삭제
  let data;
    let param = {
            geonum : col.GEONUM ?? "",
            inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await paymentApi.payment_delShipList(param)
      if(data.RecordCount > 0){
        common_utils.fxAlert("배송지가 삭제되었습니다.")
        doShipList()
      }
    } catch (error) {
      console.error(error);
    }finally{
      
    }
}
const doShipList = async ()=>{//배송지목록 가져오기
    shipList.value = []

    let data;
    let param = {
            clcode : clcodeS ?? "",
            userNo : store.getUserInfo?.USER_NO ?? "",
            inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await paymentApi.payment_shipList(param)
      if(data.RecordCount > 0){
        shipList.value.push(...data.RecordSet);
      }
    } catch (error) {
      console.error(error);
    }finally{
      newBtn();
    }
}
const doAddShipList = async ()=>{//신규배송지 추가
    shipList.value = []

    let data;
    let param = {
            geonum : "",
            clcode : clcodeS ?? "",
            userNo : store.getUserInfo?.USER_NO ?? "",
            nickName : nicName.value ?? "",
            name : name.value ?? "",
            address1 : address1.value ?? "",
            address2 : address2.value ?? "",
            postNo : postNo.value ?? "",
            phone : phone.value ?? "",
            inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await paymentApi.payment_addShipList(param)
      if(data.RecordCount > 0){
        common_utils.fxAlert("배송지가 추가되었습니다.")
        doShipList()
      }
    } catch (error) {
      console.error(error);
    }finally{
      
    }
}

</script>

<template>
  <div class="brand__area pb-60 pt-90"> 
    <div class="container custom-container-2 cus-mtop">
      <!--화면명-->
      <div class="section__title-wrapper text-center mb-55 p-relative">
          <div class="section__title mb-10">
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">배송지 관리</h2>
          </div>
      </div>
      <!--화면내용-->
      <div class="basic-login pt-20 pb-20">
        <!--내용_테이블영역-->
        <div class="table-content table-responsive mb-80" style="max-height:500px;" >
               <table class="table table-hover" style="min-width: 700px;">
                  <thead  style="position: sticky; top: 0; height: 50px; font-size: 20px; vertical-align: middle; ">
                      <tr class="headerColor">
                            <th >배송지명</th>
                            <th >이름</th>
                            <th >전화번호</th>
                            <th >우편번호</th>
                            <th  colspan="2">주소</th> 
                      </tr>
                    </thead>
                    <tbody > 
                        <tr class="underLine" v-if="shipList.length > 0" v-for="(item,i) in shipList" :key="i" @click="clickShiplist(item)">
                          <td class="product-name">
                            <span v-html="item.NICK_NAME"></span>
                          </td>
                          <td class="product-name">
                            <span v-html="item.NAME"></span>
                          </td>
                          <td class="product-name">
                              <span>{{ formatPhoneNumber(item.PHONE) }}</span>
                          </td>
                          <td class="product-name">
                              <span v-html="item.POST_NO"></span>
                          </td>
                          <td class="product-name" style="text-align:left;">
                              <span >{{ item.ADDRESS1 }}<br v-if="item.ADDRESS2">{{ item.ADDRESS2 }}</span>
                          </td>
                          <td class="product-remove" @click="delShipList(item)">
                              <a href="#"><i class="fa fa-times"></i></a>
                          </td>
                        </tr>
                        <tr class="underLine" v-else><td colspan="6">저장된 배송지정보가 없습니다.</td></tr>
                    </tbody>
              </table>
        </div>
        <!--내용_배송지추가버튼-->
        <div class="mb-20 text-center">
          <div class="form-check form-inline" style="padding-left: 0;"> 
            <div class="row nav mb-20 justify-content-end" id="nav-tab" role="tablist">  
              <button  id="nav-home-tab" class="nav-link btn-primary active" @click="newBtn()" style="font-size:20px;font-weight:500; width:auto;margin-right:10px;">
                <i class="fas fa-plus"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;신규 배송지 추가</span>
              </button> 
            </div>
          </div>
        </div> 
        <!--내용_상세입력영역-->
        <div>
          <Form   :validation-schema="schema" @submit="onSubmit">
                <div class="outline"><!--일반-->  
                    <div class="row mb-10">
                          <div class="col-sm-12 col-md-6">  
                            <label for="nicName" style="font-size:16px;font-weight:500;">배송지명<span>*</span></label>
                            <Field name="nicName" id="nicName" type="text" v-model="nicName" placeholder="배송지를 입력하세요." />
                            <ErrorMessage name="nicName" class="text-danger" />
                          </div>
                          <div class="col-sm-12 col-md-6">  
                            <label for="name" style="font-size:16px;font-weight:500;">이름<span>*</span></label>
                            <Field name="name" id="name" type="text" v-model="name" placeholder="이름을 입력하세요." />
                            <ErrorMessage name="name" class="text-danger" />
                          </div> 
                      </div> 
                      <div class="row mb-10">
                          <div class="col">  
                              <label for="phone" style="font-size:16px;font-weight:500;" >전화번호<span>*</span></label>
                            <Field name="phone" id="phone" type="tel" v-model="phone" maxlength="11" placeholder="전화번호를 입력하세요." @input="checkKey()"/>
                            <ErrorMessage name="phone" class="text-danger" />
                          </div>
                          
                      </div> 
                      
                      <div class="row mb-2">
                        <div class="col-12">  

                          <label for="postNo" style="font-size:16px;font-weight:500;">기본 주소<span>*</span></label>
                          <Field name="postNo" class="postNoMax" id="postNo"  type="text" readOnly="true"   v-model="postNo" placeholder="우편번호" @click="findAdress"/>
                          <a class="os-btn os-btn-black " @click="findAdress" style="font-size:18px;font-weight:500;height:58px;line-height:50px;margin-left: 5px; padding: 1px 10px;">주소 찾기</a>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-12 col-md-6">     
                            <Field name="address1"  id="address1"  type="text" readOnly="true"   v-model="address1" placeholder="기본 주소 입력해주세요." /> 
                            <ErrorMessage name="address1" class="text-danger" />
                          </div>
                          <div class="col-sm-12 col-md-6">  
                              <Field name="address2" id="address2" type="text" :disabled="!address1"  v-model="address2" placeholder="상세 주소를 입력해주세요." />
                            <ErrorMessage name="address2" class="text-danger" />
                          </div>
                      </div> 
                  </div>
                    <div class="mt-30 text-center">
                    <button href="#" class="os-btn os-btn-black" style="font-size:20px;font-weight:500;width:200px;">저장</button>
                  </div>
            </Form>
        </div>
      </div>
    </div>
  </div>
</template> 

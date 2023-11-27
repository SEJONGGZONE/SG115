
<script setup lang="ts">
import * as loginApi from '@/api'
import { Field, Form, ErrorMessage } from "vee-validate";
import * as common_utils from "@/common/utils.ts";
import AgreementDeatilLayer from "@/components/login-register/AgreementDeatilLayer.vue";
import PrivacyDeatilLayer from "@/components/login-register/PrivacyDeatilLayer.vue";
import MarketingDeatilLayer from "@/components/login-register/MarketingDeatilLayer.vue";

// import { loadScript } from "vue-plugin-load-script"
import * as yup from "yup";
const router = useRouter()

const emit = defineEmits(["register"]);

const goNextRegist=() =>{//다음단계 버튼 선택

  //필수 동의 항목 체크여부
  if(chkValue_2.value === 'Y' && chkValue_3.value === 'Y'){

    const param = {
        chkValue_2 : chkValue_2.value,
        chkValue_3 : chkValue_3.value,
        chkValue_4 : chkValue_4.value,
        chkValue_5 : chkValue_5.value,
        chkValue_6 : chkValue_6.value,
        nextTF : true
    }
    emit("register", param);

  }else{
    if(chkValue_2.value === 'N'){
      common_utils.fxAlert("쇼핑몰 이용 약관에 동의해주세요");
    }else if(chkValue_3.value === 'N'){
      common_utils.fxAlert("개인정보 수집 및 이용에 동의해주세요");
    }
  }
}

//모달에 확인버튼 누르면, 해당 항목 체크되면서 모달 닫기
const closeModal=(emitParam)=>{
  setTimeout(() => {
    document.getElementById(emitParam).click();
  }, 500);  
}

const chkValue_1 = ref("N")
const chkValue_2 = ref("N")
const chkValue_3 = ref("N")
const chkValue_4 = ref("N")
const chkValue_5 = ref("N")
const chkValue_6 = ref("N")

const checkAll=(id) =>{//약관 전체 동의 checkbox(id="agree1"/"agree4")
    if(id === 'agree1'){
        if(chkValue_1.value === 'Y'){//현 체크 -> 후 체크해제
          chkValue_1.value = "N"
          chkValue_2.value = "N"
          chkValue_3.value = "N"
          chkValue_4.value = "N"
          chkValue_5.value = "N"
          chkValue_6.value = "N"
        }else{
          chkValue_1.value = "Y"
          chkValue_2.value = "Y"
          chkValue_3.value = "Y"
          chkValue_4.value = "Y"
          chkValue_5.value = "Y"
          chkValue_6.value = "Y"
        }
    }else if(id === 'agree4'){
      if(chkValue_4.value === 'Y'){//현 체크 -> 후 체크해제
          chkValue_5.value = "N"
          chkValue_6.value = "N"
      }else{
          chkValue_5.value = "Y"
          chkValue_6.value = "Y"
      }
    }
}

const ischk=()=>{
  
  if(chkValue_5.value === 'N' && chkValue_6.value === 'N'){
    chkValue_4.value = "N"
  }else{
    chkValue_4.value = "Y"
  }
}


</script>

<template>
  <section class="login-area pt-10 pb-60">
    <div class="container">
        <div>
            <div class="mem_agree_area">
              <label id="pilsuAgreeAll" class="pilsu_agree_all">
                <input type="checkbox" id="agree1" v-model="chkValue_1" true-value="Y" false-value="N" @click="checkAll('agree1')">
                <span designelement="text" textindex="2">   약관 전체 동의</span>
              </label>

              <ul id="agreeList" class="agree_list3">
                  <li class="agree_section">
                      <a class="agree_view" href="#" data-bs-toggle="modal" data-bs-target="#agreementDeatilLayer" >
                        <span designelement="text" textindex="3">보기</span>
                      </a>
                      <label>
                        <input class="" type="checkbox" id="agree2" v-model="chkValue_2" true-value="Y" false-value="N">
                          <span designelement="text" textindex="4">   쇼핑몰 이용약관</span>
                          <span class="desc pointcolor4 imp" designelement="text" textindex="5">(필수)</span>
                      </label>
                  </li>
                  <li class="agree_section">
                      <a class="agree_view" href="#" data-bs-toggle="modal" data-bs-target="#privacyDeatilLayer">
                        <span designelement="text" textindex="6">보기</span>
                      </a>
                      <label>
                        <input class="" type="checkbox" id="agree3" v-model="chkValue_3" true-value="Y" false-value="N">
                        <span designelement="text" textindex="7">   개인정보 수집 및 이용</span>
                        <span class="desc pointcolor4 imp" designelement="text" textindex="8">(필수)</span>
                      </label>
                  </li>
                  <li class="agree_section">
                      <a class="agree_view" href="#" data-bs-toggle="modal" data-bs-target="#marketingDeatilLayer">
                        <span designelement="text" textindex="12">보기</span>
                      </a>
                      <label>
                        <input class="" type="checkbox" id="agree4" v-model="chkValue_4" true-value="Y" false-value="N" @click="checkAll('agree4')">
                        <span designelement="text" textindex="13">   마케팅 및 광고 활용 동의</span>
                        <span class="desc pointcolor4 imp" designelement="text" textindex="14">(선택)</span>
                      </label>
                      
                      <ul id="agreeList2" class="selection">
                          <li class="agree_section" style="float: left;margin-left: 14px;">
                            <label>
                              <input class="" type="checkbox" id="agree5" v-model="chkValue_5" true-value="Y" false-value="N"  @change="ischk()">
                              <span designelement="text" textindex="15">   이메일 수신</span>
                            </label>
                          </li>
                          <li class="agree_section" style="float: left;margin-left: 14px;">
                            <label>
                              <input class="" type="checkbox" id="agree6" v-model="chkValue_6" true-value="Y" false-value="N" @change="ischk()">
                              <span designelement="text" textindex="16">   SMS 수신</span>
                            </label>
                          </li>
                      </ul>
                  </li>
              </ul>
            </div>

            <div class="btn_area_c">
              <button type="button" id="btn_submit" class="btn_resp size_c color2 Wmax" @click="goNextRegist()">
                <span designelement="text" textindex="24" style="color: #fff !important;">다음 단계</span>
              </button>
            </div>
        </div>


        <!--보기 모달 영역 start-->
        <!-- 1. 쇼핑몰 이용약관-->
        <div id="agreementDeatilLayer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
              <div class="product__modal-wrapper p-relative">
                <div class="product__modal-close p-absolute">
                    <button data-bs-dismiss="modal" id="closeButton_agreementDeatilLayer"><i class="fal fa-times" style="font-size:24px;line-height:40px;"></i></button>
                </div>
                <div class="product__modal-inner">
                  <AgreementDeatilLayer @closeModal="closeModal"></AgreementDeatilLayer>
                </div>
              </div>
            </div>  
          </div>
        </div>

        <!-- 2. 개인정보 수집 및 이용-->
        <div id="privacyDeatilLayer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
              <div class="product__modal-wrapper p-relative">
                <div class="product__modal-close p-absolute">
                    <button data-bs-dismiss="modal" id="closeButton_privacyDeatilLayer"><i class="fal fa-times" style="font-size:24px;line-height:40px;"></i></button>
                </div>
                <div class="product__modal-inner">
                  <PrivacyDeatilLayer @closeModal="closeModal"></PrivacyDeatilLayer>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. 마케팅 및 광고활용 동의-->
        <div id="marketingDeatilLayer" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
              <div class="product__modal-wrapper p-relative">
                <div class="product__modal-close p-absolute">
                    <button data-bs-dismiss="modal" id="closeButton_marketingDeatilLayer"><i class="fal fa-times" style="font-size:24px;line-height:40px;"></i></button>
                </div>
                <div class="product__modal-inner">
                  <MarketingDeatilLayer @closeModal="closeModal"></MarketingDeatilLayer>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--보기 모달 영역 end-->

        
    </div>
  </section>
</template>

<style>
span {
    cursor: pointer;
    line-height: 1.5;
    font-weight: 400;
    color: #666 !important;
}
.pilsu_agree_all {
    display: inline-block;
    padding: 10px 9px;
    font-size: 17px;
    font-weight: 500;
    color: #333;
}
input[type=checkbox], .pilsu_agree_all input[type='checkbox'], .pilsu_agree_all2 input[type='checkbox'], .agree_list2 input[type='checkbox'], .agree_list3 input[type='checkbox'], .resp_cart_wrap label.checkbox_allselect input[type='checkbox'] {
    width: 16px;
    height: 16px;
    vertical-align: middle;
}
.agree_list3 {
    border-top: 1px #ddd solid;
    font-size: 14px;
}
ul {
    display: block;
    list-style-type: disc;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
    margin: 0;
    padding: 0;
    max-height: 100000px;
}
.agree_list3>li {
    border: 1px #ddd solid;
    border-top: none;
}
li {
    display: list-item;
    text-align: -webkit-match-parent;
}
.agree_list3>li>label {
    display: block;
    margin: 0;
    padding: 10px;
    box-sizing: border-box;
}
.agree_list3 .agree_view {
    display: block;
    text-align: right;
    float: right;
    box-sizing: border-box;
    width: 66px;
    padding: 11px 10px 9px 0;
}
.agree_list3 .agree_view + label {
    width: calc( 100% - 66px );
}
a {
    color: #444;
    text-decoration: none;
    border: none;
    outline: none;
}
.pointcolor4.imp {
    color: #697da6 !important;
}
.agree_list3 .desc {
    font-size: 13px;
}
.selection {
    display: inline-block;
    padding: 10px 9px;
    color: #333;
}
.btn_area_c {
    padding-top: 20px;
    text-align: center;
}
/** 버튼 */
.btn_resp {
    border: 1px #ccc solid;
    background: #fff;
    height: 28px;
    font-size: 12px;
    line-height: 26px;
    padding: 0 9px;
    box-sizing: border-box;
    text-align: center;
    color: #333;
    cursor: pointer;
    vertical-align: middle;
    border-radius: 2px;
    transition: border-color 0.2s, background-color 0.2s;
}
.btn_resp.size_c {
    height: 42px;
    font-size: 17px;
    line-height: 40px;
    min-width: 100px;
    padding: 0 14px;
}
.btn_resp.color2 {
    border-color: #434343;
    background-color: #434343;
    color: #fff !important;
}
.Wmax {
    width: 100% !important;
}
/** 모달 */
.title {
    background: #fff;
    border-top: 1px #fff solid;
    font-size: 23px;
    font-weight: 700;
    color: #333;
}
.y_scroll_auto2 {
    overflow-y: auto;
    min-height: 220px;
    margin-bottom: 62px;
}
</style>


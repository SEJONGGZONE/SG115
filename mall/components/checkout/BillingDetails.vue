<template>

  <div class="accordion" id="accordionPanelsStayOpenExample">
    <div class="accordion-item">
      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne" style="
background-color: #323232; 
 color: white;">
          주문자 정보<span class="delimiter">
            <!-- {{ username }}&nbsp;&nbsp; -->
          </span>
        </button>
      </h2>
      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
        <div class="accordion-body">

          <div class="row">
            <div v-if="loginType === '10'"><!--로그인 타입(10 일반사용자, 20 사업자)-->
              <div class="col-md-6">
                <div class="checkout-form-list">
                  <label>이름</label>
                  <input type="text" v-model="nameS"/>
                </div>
              </div>
            </div>
            <div v-else class="row">
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>상호명</label>
                    <input type="text" v-model="companyNameS"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>대표자</label>
                    <input type="text" v-model="nameS"/>
                  </div>
                </div>
            </div>
            <div class="col-md-12">
              <div class="checkout-form-list">
                <label>전화번호</label>
                <input type="text"  v-model="phoneS" inputmode="numeric" maxlength="12" @input="checkKey()"/>
              </div>
            </div>
          
            
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo" style="
background-color: #323232; 
 color: white;"   >
          배송지정보
        </button>
      </h2>
      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
        <div class="accordion-body">
          <div class="row">
            <div class="col-md-8">
              <div class="checkout-form-list create-acc">
                <!-- 통합테스트_0814 2번 최근 배송지 입력 제거  --> 
                <!-- <div class="form-check" v-if="isShowrShip">
                  <input id="cbox1" class="form-check-input" type="checkbox" v-model="copyOrderInfo1" true-value="Y" false-value="N" @click="checkCopyOrderInfo1()" />
                  <label class="form-check-label" for="cbox1">최근배송지 입력</label>
                </div> -->
                <div class="form-check">
                  <input id="cbox2" class="form-check-input" type="checkbox" v-model="copyOrderInfo2" true-value="Y" false-value="N" @click="checkCopyOrderInfo2()" />
                  <label class="form-check-label" for="cbox2">주문자정보와 동일</label>
                </div>
              </div>
            </div>
            <div class="col-md-4 d-flex justify-content-end">
              <a class="os-btn" data-bs-toggle="modal" data-bs-target="#productModalId" @click="clickShipList()">배송지목록</a>
            </div>
          </div>

          <div class="row">
            <div v-if="loginType === '10'"><!--로그인 타입(10 일반사용자, 20 사업자)-->
              <div class="col-md-6">
                <div class="checkout-form-list">
                  <label>이름</label>
                  <input type="text" v-model="name"/>
                </div>
              </div>
            </div>
            <div v-else class="row">
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>배송지명</label>
                    <input type="text" v-model="companyName"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>대표자</label>
                    <input type="text" v-model="name"/>
                  </div>
                </div>
            </div>
            <div class="col-md-12">
              <div class="checkout-form-list">
                <label>전화번호</label>
                <input type="text"  v-model="phone" inputmode="numeric" maxlength="12" @input="checkKey()"/>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkout-form-list">
                <label>우편번호</label>
                <input type="text" disabled readonly v-model="postNo" style="width:150px;margin-right:5px"/>
                <a class="os-btn os-btn-black " @click="findAdress('main')">주소 찾기</a>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkout-form-list">
                <label>주소</label>
                <input type="text" disabled v-model="address_1"/>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkout-form-list">
                <input type="text" v-model="address_2"/>
              </div>
            </div> 
            
             <div class="col-md-12">
              <div class="checkout-form-list">
                <label>메모</label>
                <input type="text" v-model="memo"/>
              </div>
            </div>
        </div>
        </div>
      </div>
    </div>
  </div>






  <!-- 모달 start-->
  <div class="modal fade" id="productModalId" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered product-modal" role="document">
                <div class="modal-content">
                    <div class="product__modal-wrapper p-relative">
                        <div class="product__modal-close p-absolute">
                            <button data-bs-dismiss="modal" id="closeButton"><i class="fal fa-times"></i></button>
                        </div>
                        <div class="product__modal-inner">

                          <!--상단 탭-->
                          <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                              <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">배송지 목록</button>
                              <!--<button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">최근배송지</button>-->
                            </div>
                          </nav>

                          <div class="tab-content" id="nav-tabContent">
                              <!--배송지목록 탭 내용-->
                              <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="d-flex justify-content-end mt-10">
                                  <!--<a class="os-btn" @click="addShipList()">신규 배송지 추가</a>-->
                                  <a class="os-btn" :class="collapseIsShow ? 'collapsed': '' "
                                     data-bs-toggle="collapse" href="#collapseExample" role="button"
                                     :aria-expanded="collapseIsShow ? 'false': 'true'" aria-controls="collapseExample"
                                     @click.prevent="newBtn()">신규 배송지 추가</a>
                                </div>
                                  <div class="table-content table-responsive mt-10">
                                    <!--신규배송지 입력 영역-->
                                    <div class="collapse mb-10" id="collapseExample" :class="collapseIsShow ? 'show': ''">
                                      <div class="card card-body">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="checkout-form-list" style="margin-bottom: 20px;">
                                              <label>배송지명</label>
                                              <input type="text" v-model="nicNameN"/>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="checkout-form-list" style="margin-bottom: 20px;">
                                              <label>이름</label>
                                              <input type="text" v-model="nameN"/>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="checkout-form-list" style="margin-bottom: 20px;">
                                              <label>전화번호</label>
                                              <input type="text"  v-model="phoneN" inputmode="numeric" maxlength="12" @input="checkKey()"/>
                                            </div>
                                          </div>
                                          <div class="row col-12 " style="padding-right:0px">
                                            
                                                  <label>우편번호</label>
                                                <div class="col-6 mb-1 checkout-form-list">  
                                                  <input type="text" disabled v-model="postNoN" inputmode="numeric" maxlength="6" @input="checkKey()"/>
                                                </div>
                                                <div class="col-6 checkout-form-list"> 
                                                  <a class="os-btn os-btn-black " @click="findAdress('popup')">주소 찾기</a>
                                                </div> 
                                          </div>
                                          <div class="col-md-12">
                                            <div class="checkout-form-list" style="margin-bottom: 10px;">
                                              <label>주소</label>
                                              <input type="text" disabled v-model="address_1N" maxlength="100"/>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="checkout-form-list" style="margin-bottom: 20px;">
                                              <input type="text" v-model="address_2N" maxlength="50"/>
                                            </div>
                                          </div>
                                          <div class="col-md-12 text-center">
                                            <a class="os-btn" @click="addShipList()">추가</a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <!--배송지 목록 영역-->
                                    <table class="table table-hover">
                                          <thead>
                                            <th class="cart-product-name">배송지명</th>
                                            <th class="cart-product-name">이름</th>
                                            <th class="cart-product-name">전화번호</th>
                                            <th class="cart-product-name">우편번호</th>
                                            <th class="cart-product-name">주소</th>
                                          </thead>
                                          <tbody>
                                              <tr v-if="shipList.length > 0" v-for="(item,i) in shipList" :key="i" @click="clickShiplist(item)">
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
                                                <td class="product-name">
                                                    <span >{{ item.ADDRESS1 }}<br v-if="item.ADDRESS2">{{ item.ADDRESS2 }}</span>
                                                </td>
                                            </tr>
                                            <tr v-else>
                                              <td colspan="5">검색결과가 없습니다.</td>
                                            </tr>
                                          </tbody>
                                    </table>
                                  </div>
                              </div>
                              <!--배송지 추가 탭 내용-->
                              <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="table-content table-responsive mt-30">

                                </div>
                              </div>
                          </div>




                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 모달 end -->

</template>

<script setup>
 
import { useUserStore } from '@/store/useUser';
import * as paymentApi from '@/api';
import { useCartStore } from "@/store/useCart";
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

//결제예정 상품 정보
const state = useCartStore();
const checkedCartList = computed(() => state.getCheckedCartList());
const cartTotalCnt = computed(() => state.getCartTotalCnt);
const cartTotalAmount = computed(() => state.getCartTotalAmount);

//배송지 영역 checkbox
const copyOrderInfo1 = ref('N')//"최근배송지 정보 입력" 체크박스 Y-체크, N-체크해제
const copyOrderInfo2 = ref('N')//"주문자정보 입력" 체크박스 Y-체크, N-체크해제

//배송지목록
const name = ref('');
const companyName = ref('');
const phone = ref('');
const postNo = ref('')
const address_1 = ref('');
const memo = ref('');
const address_2 = ref('');

//모달내 신규배송지목록
const nicNameN = ref('')
const nameN = ref('');
const phoneN = ref('');
const postNoN = ref('');
const address_1N = ref('');
const address_2N = ref('');

const rShipList = ref([])//최근 배송지 목록
const shipList = ref([])//배송지 목록

const collapseIsShow = ref(false);
const isShowrShip = computed(()=>!!rShipList.value[0]?.RECV_ADDRESS1)

/****************************************************************************************** mounted */
onMounted(()=>{
  //loadScript('https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js')
  doRecentShipList();//최근배송지 목록
})
/****************************************************************************************** method */


const findAdress = (type) =>{
  common_utils.searchAdress().then((result)=>{
    if(type === 'main'){
      postNo.value = result.zonecode
      address_1.value = result.roadAddress
    }else{
      postNoN.value = result.zonecode
      address_1N.value = result.roadAddress
    }

  })
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

const newBtn=() =>{//모달_신규배송지추가 버튼
  collapseIsShow.value = !collapseIsShow.value
}
/** 통합테스트_0814 2번 최근 배송지 입력 제거  */
// const checkCopyOrderInfo1=() =>{//최근배송지 입력 체크박스
//   if(copyOrderInfo1.value === 'Y'){//체크->체크해제
//     name.value = ''
//     companyName.value = ''
//     phone.value = ''
//     postNo.value = ''
//     address_1.value = ''
//     address_2.value = ''
//   }else{//체크해제->체크
//     if(!rShipList.value[0].RECV_ADDRESS1){//null, undefine 체크
//       common_utils.fxAlert("최근 배송지 정보가 없습니다.")
//       copyOrderInfo1.value = 'N';
//       return
//     }
//     name.value = rShipList.value[0].RECV_NAME ?? ""
//     companyName.value = ""
//     phone.value = formatPhoneNumber(rShipList.value[0].RECV_PHONE) ?? ""
//     postNo.value = rShipList.value[0].RECV_POST_NO ?? ""
//     address_1.value = rShipList.value[0].RECV_ADDRESS1 ?? ""
//     address_2.value = rShipList.value[0].RECV_ADDRESS2 ?? ""

//   }
// }
const checkCopyOrderInfo2=() =>{//주문자정보 입력 체크박스
  if(copyOrderInfo2.value === 'Y'){//체크->체크해제
    name.value = ''
    companyName.value = ''
    phone.value = ''
    postNo.value = ''
    address_1.value = ''
    address_2.value = ''
  }else{//체크해제->체크 
    name.value = nameS.value
    companyName.value = companyNameS
    phone.value = phoneS
    postNo.value = userInfoPostNo
    address_1.value = addressS_1
    address_2.value = addressS_2
  }
}
const clickShipList=() =>{//배송지 목록 버튼 클릭
    nicNameN.value = ''
    nameN.value = ''
    phoneN.value = ''
    postNoN.value = ''
    address_1N.value = ''
    address_2N.value = ''
    collapseIsShow.value = false

    doShipList();
}
const checkKey=() =>{
  phone.value = phone.value.replace(/[^0-9]/g, '');
  phoneS = phoneS.replace(/[^0-9]/g, '');
  phoneN.value = phoneN.value.replace(/[^0-9]/g, '');
  postNoN.value = postNoN.value.replace(/[^0-9]/g, '');
}
const addShipList=() =>{//배송지목록 모달 - 신규배송지추가->배송지 입력->추가 버튼 클릭

    let chk = false
		let chkId = ''

		if(!nicNameN.value){
			chk = true
			chkId = `${chkId}배송지명`
		}
		if(!nameN.value){
			chk = true
			chkId = !chkId? `이름` : `${chkId}, 이름`
		}
		if(!phoneN.value){
			chk = true
			chkId = !chkId? `전화번호` : `${chkId}, 전화번호`
		}
    if(!postNoN.value){
			chk = true
			chkId = !chkId? `우편번호` : `${chkId}, 우편번호`
		}
    if(!address_1N.value && !address_2N.value){
			chk = true
			chkId = !chkId? `주소` : `${chkId}, 주소`
		}

		if(chk){
			common_utils.fxAlert(`누락된 항목을 확인해 주세요..<br>(${chkId})`)
			//event.preventDefault();
			return;
		}
    doAddShipList()
    nicNameN.value = ''
    nameN.value = ''
    phoneN.value = ''
    postNoN.value = ''
    address_1N.value = ''
    address_2N.value = ''
}
const selectRowData = ref([]);
const clickShiplist=(item) =>{//배송지목록 모달 - 배송지 목록 선택

  selectRowData.value = JSON.parse(JSON.stringify(item));

  companyName.value = selectRowData.value.NICK_NAME
  name.value = selectRowData.value.NAME
  phone.value = selectRowData.value.PHONE
  postNo.value = selectRowData.value.POST_NO
  address_1.value = selectRowData.value.ADDRESS1
  address_2.value = selectRowData.value.ADDRESS2

  //배송지목록 복사 후 모달창 닫기
  setTimeout(() => {
    document.getElementById('closeButton').click();//저장 후 모달창 닫기
  }, 500);
}

/****************************************************************************************** api호출 */
const doRecentShipList = async ()=>{//최근 배송지목록 가져오기
    rShipList.value = []

    let data;
    let param = {
            clcode : clcodeS ?? "",
            userNo : store.getUserInfo?.USER_NO ?? "",
            inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await paymentApi.payment_recentShipList(param)
      if(data.RecordCount > 0){
        rShipList.value.push(...data.RecordSet);
      }
    } catch (error) {
      console.error(error);
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
    }
}

const doAddShipList = async ()=>{//신규배송지 추가
    shipList.value = []

    let data;
    let param = {
            geonum : "",
            clcode : clcodeS ?? "",
            userNo : store.getUserInfo?.USER_NO ?? "",
            nickName : nicNameN.value ?? "",
            name : nameN.value ?? "",
            address1 : address_1N.value ?? "",
            address2 : address_2N.value ?? "",
            postNo : postNoN.value ?? "",
            phone : phoneN.value ?? "",
            inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await paymentApi.payment_addShipList(param)
      if(data.RecordCount > 0){
        doShipList()
      }
    } catch (error) {
      console.error(error);
    }
}

const getConverterParams = (cartType) =>{
  let cartNo = '' 
  if(cartType === '10'){ 
    cartNo = localStorage.getItem("CART_NO") 
  }else{  
    cartNo = localStorage.getItem("BUY_CART_NO") 
  }

  return {
      orderId : '',
      cartNo : cartNo,
      geonum : '',
      itemList : '',
      clcode : clcodeS ?? "",
      userNo : store.getUserInfo?.USER_NO ?? "",
      status : "",
      orderName : nameS.value,
      orderPhone : phoneS,
      recvName : name.value,
      recvPhone : phone.value, 
      address1 : address_1.value,
      address2 : address_2.value,
      postNo : postNo.value,
      paymentType : '',
      paymentNo : '',
      paymentDate : '',
      orderAmount : 0,
      deliAmount : 0,
      discountAmount : 0,
      paymentAmount : 0 ,
      memo : memo.value,
      inputUser : nameS.value
  }
}

 
const getParams =  (cartType)=>{//주문저장
    shipList.value = []
    const converterParam = getConverterParams(cartType)
    let data;
    let param = {
            geonum : converterParam.geonum,
            orderId : converterParam.orderId,
            cartNo : converterParam.cartNo,
            itemList : converterParam.itemList,
            clcode : converterParam.clcode,
            userNo : converterParam.userNo,
            status : converterParam.status,
            orderName : converterParam.orderName ,
            orderPhone : converterParam.orderPhone,
            recvName : converterParam.recvName,
            recvPhone : converterParam.recvPhone,
            address1 : converterParam.address1,
            address2 : converterParam.address2,
            postNo  : converterParam.postNo,
            paymentType : converterParam.paymentType,
            paymentNo : converterParam.paymentNo,
            paymentDate : converterParam.paymentDate,
            orderAmount : converterParam.orderAmount,
            deliAmount : converterParam.deliAmount,
            discountAmount : converterParam.discountAmount,
            paymentAmount : converterParam.paymentAmount,
            memo : converterParam.memo,
            inputUser  : converterParam.inputUser
    }
    return param
} 
defineExpose({
  getParams
})
</script>
<style>
.accordion-button {
  display: flex;
  align-items: center;
  justify-content: space-between;

}

.delimiter {
  flex-grow: 1;
  text-align: end;
  line-height: 1px;
  margin: 0 5px;
  font-size: 14px;
}

.table-hover tbody tr:hover {
      cursor: pointer;
    }
</style>
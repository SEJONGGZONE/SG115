<template>
  <layout>
      <div class="brand__area pb-60 pt-90">
        <div class="container custom-container-2 cus-mtop">
          <div class="section__title-wrapper text-center mb-55 p-relative">
              <div class="section__title mb-10">
                  <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">주문조회</h2>
              </div>
          </div>

          
          <div class="basic-login pt-20 pb-20 ">
            <!--상단영역-->
            <div>
              <!--검색조건 영역-->
              <div class="group__search"> 
                <div class="row justify-content-end" >
                  <div class="col-auto height50 pr-0 pl-0">
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('today')">오늘</button>
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('week')">일주일</button>
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('thismonth')">이번달</button>
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('1month')">1개월</button>
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('3month')">3개월</button>
                      <button type="button" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" @click="setDate('6month')">6개월</button>
                  </div>
                  <div class="col-auto">
                    <div class="row center">
                      <div class="col" >
                        <!--<vue-date-picker  v-model="startDate" placeholder="선택하세요"  format='yyyy-MM-dd' :locale="ko"  />-->
                        <Datepicker v-model="startDate" format='yyyy-MM-dd'/>
                      </div>
                      <div  class="col-sm-auto" style="padding-right: 0px; padding-left: 0px;">
                        ~
                      </div>
                      <div class="col">
                        <Datepicker v-model="endDate" format='yyyy-MM-dd'/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="checkout-form-list" style="display: flex; align-items: center;">
                  <input type="text" v-model="searchKeyword" style="margin-right: 5px; border-radius: 5px;">
                  <button @click="searchBtn()" class="btn btn-lg btn-white me-1 mb-1 whiteBtn" style="flex-shrink: 0;">
                    <i class="fas fa-search"></i><span>검색</span>
                  </button> 
                </div>
              </div> 
              
              <!--리스트영역-->
              <div>
                <div class="table-content table-responsive mb-80" style="max-height:500px;" >
                  <table class="table table-hover" style="min-width: 700px;">
                    <thead  style="position: sticky; top: 0; height: 50px; font-size: 20px; vertical-align: middle; ">
                        <tr class="headerColor">
                          <th >결제일자</th>
                          <th >주문자</th>
                          <th >수령자</th>
                          <th >상품정보</th>
                          <th >결제금액(배송비)</th>
                          <th >배송상태</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr v-if="orderHistoryList.length > 0" class="underLine" v-for="(item,i) in orderHistoryList" :key="i" @click="clickOrderHistorylist(item)">
                          <td class="product-name">{{ item.ORDER_DATE }}</td>
                          <td class="product-name">{{ item.ORDER_NAME }}</td>
                          <td class="product-name">{{ item.RECV_NAME }}</td>
                          <td class="product-name">{{ item.ORDER_SUMMARY }}<br>({{ item.ORDERID }})</td>
                          <td class="product-price">
                              <span class="amount">{{ common_utils.formattedPrice(item.PAYMENT_AMOUNT) }}원</span>
                          </td>
                          <td class="product-name">{{ item.STATUS_NM }}</td>
                      </tr>
                      <tr v-else><td colspan="6">주문내역이 없습니다.</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!--하단 영역-->
            <div class="row">
              <!--주문정보/배송지 정보-->
              <div class="accordion col-xl-4 col-md-12" id="accordionPanels1">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                    <button class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelsStayOpen-1" 
                            aria-expanded="true" 
                            aria-controls="panelsStayOpen-1" 
                            style="background-color: #323232;color: white;">
                              주문정보
                    </button>
                  </h2>
                  <div id="panelsStayOpen-1" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                    <div class="accordion-body">
                        <div class="checkout-form-list">
                          <label>업체명(상호명)/주문자</label>
                          <input type="text" :value="selectRowData.ORDER_NAME" readonly/>
                        </div>
                        <div class="checkout-form-list">
                          <label>전화번호</label>
                          <input type="text" :value="formatPhoneNumber(selectRowData.ORDER_PHONE)" readonly>
                        </div>
                        <div class="checkout-form-list">
                          <label>주소</label>
                          <input type="text" :value="selectRowData.ORDER_ADDRESS" readonly/>
                        </div>
                    </div>
                  </div>
                  
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                    <button class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelsStayOpen-2" 
                            aria-expanded="false" 
                            aria-controls="panelsStayOpen-2" 
                            style="background-color: #323232;color: white;">
                              배송지정보
                    </button>
                  </h2>
                  <div id="panelsStayOpen-2" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
                    <div class="accordion-body">
                      <div class="checkout-form-list">
                        <label>업체명(상호명)/주문자</label>
                        <input type="text" :value="selectRowData.RECV_NAME" readonly/>
                      </div>
                      <div class="checkout-form-list">
                        <label>전화번호</label>
                        <input type="text" :value="formatPhoneNumber(selectRowData.RECV_PHONE)" readonly/>
                      </div>
                      <div class="checkout-form-list">
                        <label>주소</label>
                        <input type="text" :value="selectRowData.RECV_ADDRESS" readonly/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--상품정보/배송정보,결제정보-->
              <div class="accordion col-xl-8 col-md-12" id="accordionPanels2">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                    <button class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelsStayOpen-3" 
                            aria-expanded="true" 
                            aria-controls="panelsStayOpen-3" 
                            style="background-color: #323232;color: white;">
                              상품정보
                    </button>
                  </h2>
                  <div id="panelsStayOpen-3" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                    <div class="accordion-body">
                      <div class="table-content table-responsive">
                          <table class="table">
                            <thead >
                                <tr>
                                  <th class="cart-product-name tableStyleFont">상품이미지</th>
                                  <th class="cart-product-name tableStyleFont">상품명</th>
                                  <th class="cart-product-name tableStyleFont">금액</th>
                                  <th class="cart-product-name tableStyleFont">수량</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,i) in selectRowDataProductList" :key="i">
                                  <td class="product-name"><CookImage :src="item.ITEM_MAIN_IMAGE"></CookImage></td>
                                  <td class="product-name"><a style="cursor: pointer;
    text-decoration: underline" @click.prevent="moveProduct(item.ITCODE)">{{ item.ITNAME }}</a></td>
                                  <td class="product-price">
                                      <span class="amount">{{ common_utils.formattedPrice(item.AMOUNT) }}</span>
                                  </td>
                                  <td class="product-price">
                                      <span class="amount">{{ item.QTY }}</span>
                                  </td>
                              </tr>
                              <tr><td colspan="4"></td></tr>
                            </tbody>
                          </table>
                      </div>
                    </div>
                  </div>
                  
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                    <button class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#panelsStayOpen-4" 
                            aria-expanded="false" 
                            aria-controls="panelsStayOpen-4" 
                            style="background-color: #323232;color: white;">
                              배송지정보 / 결제정보
                    </button>
                  </h2>
                  <div id="panelsStayOpen-4" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
                    <div class="accordion-body row">
                      <div class="checkout-form-list col-xl-4 col-md-12">
                        <label>배송유형 및 배송비</label>
                        <input type="text" :value="selectRowData.DELIVERY_METHOD" readonly/>
                      </div>
                      <div class="checkout-form-list col-xl-8 col-md-12">
                        <label>결제방법/금액</label>
                        <input type="text" :value="pay" readonly/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div><!--하단 영역 끝-->
          </div>

        </div>
      </div>
  </layout>
</template>

<script setup >

import * as common_utils from "@/common/utils.ts";
import Layout from "@/layout/LayoutFour.vue";
import CheckoutArea from "@/components/checkout/CheckoutArea.vue";
import { useCartStore } from "@/store/useCart";
import CookImage from '@/components/common/others/CookImage.vue' 
import Datepicker from '@/components/common/others/Datepicker.vue' 
import * as listApi from '@/api';
import { useUserStore } from '@/store/useUser';
const router = useRouter()



const store = useUserStore();
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO; 
const cartTotalAmount = computed(() => sumArrayValues());
const cartList = ref([])
const cartType = ref('10')
const checkItCodes  = ref([])

//주문정보
const j_name = ref('1')
const j_phoneNo = ref('2')
const j_address = ref('3')
//배송지정보
const d_name = ref('4')
const d_phoneNo = ref('5')
const d_address = ref('6')
//배송지정보/결제정보
const c_delivery = ref('7')
const c_check = ref('8')

//달력 변수
const today = new Date(); // 현재 날짜와 시간을 포함하는 Date 객체 생성
const year = today.getFullYear(); // 년도 가져오기
const month = today.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
const day = today.getDate(); // 일 가져오기
const formattedToday = new Date(`${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`);
const startDate = ref(formattedToday)
const endDate = ref(formattedToday)
//검색어
const searchKeyword = ref();


/******************************************************************************* onMounted */
onMounted(()=>{
  doOrderHistoryList()
})
/******************************************************************************* method */
const setDate = (type) =>{
      
      /** 일주일 뒤 */
      const nextWeekEndDate = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000); // 일주일 뒤의 날짜
      const nextWeekyear = nextWeekEndDate.getFullYear(); // 년도 가져오기
      const nextWeekmonth = nextWeekEndDate.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
      const nextWeekday = nextWeekEndDate.getDate(); // 일 가져오기

      const nextWeekformattedEndDate = `${nextWeekyear}-${nextWeekmonth.toString().padStart(2, '0')}-${nextWeekday.toString().padStart(2, '0')}`;
      /** 이번달 */
      const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0); // 이번달 마지막 날
      const thisMonthyear = lastDay.getFullYear(); // 년도 가져오기
      const thismonth = lastDay.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
      const thismonthStartday = "01"; // 일 가져오기
      const formattedLastDay = `${lastDay.getDate().toString().padStart(2, '0')}`;
      /** 저번달 */
      const beforeFirstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1); // 저번달 1일
      const beforeLastDay = new Date(today.getFullYear(), today.getMonth(), 0); // 저번달 마지막 날
      const prevMonth = (beforeFirstDay.getMonth() + 1).toString().padStart(2, '0'); // 저번달 월 가져오기
      const prevFirstDay = '01'; // 저번달 1일
      const prevLastDay = beforeLastDay.getDate().toString().padStart(2, '0'); // 저번달 마지막 날
      /** 3개월 전 */
      const threeMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 3, 1); // 3개월 전 월 1일
      const lastDayOfThreeMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 2, 0); // 3개월 전 월 마지막 날
      const threeMonthsYear = threeMonthsAgo.getFullYear(); // 년도 가져오기
      const threeMonthsprevMonth = (threeMonthsAgo.getMonth() + 1).toString().padStart(2, '0'); // 이전달 월 가져오기
      const threeMonthsprevFirstDay = '01'; // 이전달 1일
      const threeMonthsprevLastDay = lastDayOfThreeMonthsAgo.getDate().toString().padStart(2, '0'); // 이전달 마지막 날
      /** 6개월 전 */
      const sixMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 6, 1); // 6개월 전 월 1일
      const lastDayOfSixMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 5, 0); // 6개월 전 월 마지막 날
      const sixMonthsYear = sixMonthsAgo.getFullYear(); // 년도 가져오기
      const sixMonthsprevMonth = (sixMonthsAgo.getMonth() + 1).toString().padStart(2, '0'); // 이전달 월 가져오기
      const sixMonthsprevFirstDay = '01'; // 이전달 1일
      const sixMonthsprevLastDay = lastDayOfSixMonthsAgo.getDate().toString().padStart(2, '0'); // 이전달 마지막 날

      if(type === 'today'){
        startDate.value = new Date(formattedToday)
        endDate.value   = new Date(formattedToday)
      }else if(type === 'week'){
        startDate.value =new Date(formattedToday)
        endDate.value = new Date(nextWeekformattedEndDate)
      }else if(type === 'thismonth'){
        startDate.value = new Date(`${thisMonthyear}-${thismonth}-${thismonthStartday}`)
        endDate.value =  new Date(`${thisMonthyear}-${thismonth}-${formattedLastDay}`)
      }else if(type === '1month'){
        startDate.value = new Date(`${year}-${prevMonth}-${prevFirstDay}`)
        endDate.value = new Date(`${year}-${prevMonth}-${prevLastDay}`)
      }else if(type === '3month'){
        startDate.value =new Date(`${threeMonthsYear}-${threeMonthsprevMonth}-${threeMonthsprevFirstDay}`)
        endDate.value = new Date(`${threeMonthsYear}-${thismonth}-${formattedLastDay}`)
      }else if(type === '6month'){
        startDate.value =new Date(`${sixMonthsYear}-${sixMonthsprevMonth}-${sixMonthsprevFirstDay}`)
        endDate.value = new Date(`${sixMonthsYear}-${thismonth}-${formattedLastDay}`)
      }
  }

const selectRowData = ref([]);
const pay = ref('')//결제방법
let selectRowDataProductList = []//선택한 row의 상품목록
const clickOrderHistorylist=(item) =>{//배송지목록 모달 - 배송지 목록 선택
    selectRowData.value = JSON.parse(JSON.stringify(item));
    pay.value = `${selectRowData.value.PAYMENT_METHOD} (${common_utils.formattedPrice(selectRowData.value.ORDER_AMOUNT)})` //배송지정보/결제정보 > 결제방법/금액

    const parsedData = JSON.parse(selectRowData.value.JSON_ITEMLIST);
    selectRowDataProductList = parsedData;
}
const searchBtn=()=>{//검색 btn
  doOrderHistoryList()
}
function formatDate(date) {
  const year = date.getUTCFullYear();
  const month = (date.getUTCMonth() + 1).toString().padStart(2, '0');
  const day = date.getUTCDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
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
/******************************************************************************* api 호출 */
const orderHistoryList = ref([])//주문내역 목록
const doOrderHistoryList = async ()=>{//주문내역 가져오기
  orderHistoryList.value = []

  let data;
  let param = {
          userNo : store.getUserInfo?.USER_NO ?? "",
          startDate : formatDate(startDate.value) ?? "",
          endDate : formatDate(endDate.value) ?? "",
          keyword : searchKeyword.value ?? "",
          inputUser : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      data = await listApi.list_orderHistoryList(param)
      if(data.RecordCount > 0){
        orderHistoryList.value.push(...data.RecordSet);
      }
    } catch (error) {
      console.error(error);
    }finally{
      
    }
}
const moveProduct = (itCode) =>{ 
  router.push( `/product-details/${itCode}`) 
}
</script>

<style>

</style>

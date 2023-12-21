<template>
  <client-only>
    <section class="cart-area pt-90 pb-100">
      <div class="container">
          <div class="row">
              <div class="col-12">
                <div v-if="cartList.length === 0" class='text-center'>
                  <h3>장바구니가 비어있습니다.</h3>
                  <nuxt-link class="os-btn os-btn-black mt-20" @click="goMain()">쇼핑 계속하기</nuxt-link>
                </div>
                
                <form v-if="cartList.length > 0" action="#">
                    <div class="section__title-wrapper text-center mb-55 p-relative">
                        <div class="section__title mb-10">
                            <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">장바구니</h2>
                        </div>
                    </div>
                      <div class="table-content table-responsive">
                          <table class="table">
                            <thead  style="height: 50px; font-size: 20px; vertical-align: middle; ">
                                <tr>
                                      <th class="product-thumbnail">
                                            <input  class="form-check-input" type="checkbox" @change="chnageAllChk"  v-model="isAll"  >
                                      </th>
                                      <th class="cart-product-name">대표이미지</th>
                                      <th class="cart-product-name">상품명</th>
                                      <th class="cart-product-name">금액</th>
                                      <th class="cart-product-name"  style="border-right: 0px;">수량</th>
                                      <th class="cart-product-name"></th>
                                </tr>
                              </thead>
                              <tbody>
                                  <!--<cart-item v-for="(cartItem,i) in state.cart_products" :key="i" :item="cartItem" />-->
                                  <tr v-for="(item,i) in cartList" :key="i">
                                    <td class="vtl-tbody-td">
                                        <div>
                                            <input class="form-check-input" type="checkbox" v-model="item['CHECK']">
                                        </div>
                                    </td>
                                    <td class="product-thumbnail">
                                        <nuxt-link :href="`/product-details/${item.ITCODE}`">
                                            <!--<img class="imgmMini" :src="item.ITEM_MAIN_IMAGE ? item.ITEM_MAIN_IMAGE : noImg" alt="banner" style="height: 100px;"/>-->
                                            <CookImage :image="item.ITEM_MAIN_IMAGE" :size="'imgmM'"/> 
                                        </nuxt-link>
                                    </td>
                                    <td class="product-name">
                                        <nuxt-link :href="`/product-details/${item.ITCODE}`"><span v-html="item.ITNAME"></span></nuxt-link>
                                    </td>
                                    <td class="product-price">
                                        <span class="amount">{{ common_utils.formattedPrice(item.AMOUNT) }}원</span>
                                    </td>
                                    <td class="product-quantity">
                                        <div class="cart-plus-minus">
                                            <input type="text" v-model="item.QTY" />
                                            <div @click="item.QTY > 1 ? item.QTY-- : item.QTY = 1; clickPMBtn(item)" class="dec qtybutton">
                                            -
                                            </div>
                                            <div @click="item.QTY++; clickPMBtn(item)" class="inc qtybutton">+</div>
                                        </div>
                                    </td>
                                    <!-- 
                                    <td class="product-subtotal">
                                        <span class="amount">{{ common_utils.formattedPrice(item.AMOUNT) }} * {{ item.QTY }} = {{typeof item.QTY !== "undefined" && common_utils.formattedPrice(item.AMOUNT * item.QTY)}}</span>
                                    </td>
                                    -->
                                    <td class="product-remove" @click="delCartList(item)">
                                        <a href="#"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                              </tbody>
                          </table>
                      </div>
                      <!--
                      <div class="row">
                          <div class="col-12">
                              <div class="coupon-all">
                                  <div class="coupon">
                                      <input required id="coupon_code" class="input-text" name="coupon_code" value=""
                                          placeholder="Coupon code" type="text">
                                      <button class="os-btn os-btn-black" name="apply_coupon" type="button">
                                        Apply coupon
                                      </button>
                                  </div>
                                  <div class="coupon2">
                                      <button @click="state.clear_cart" class="os-btn os-btn-black" name="update_cart" type="button">Clear cart</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                    -->
                      <div class="row">
                          <div class="col-md-5 ms-auto">
                              <div class="cart-page-total">
                                  <ul class="mb-20">
                                      <li class="fw-bold" style="font-size: 22px;color: #222222;">총 합계({{ cartTotalCnt }}) :  <span>{{ common_utils.formattedPrice(checkCartTotalAmount) }}원</span></li>
                                  </ul>
                                  <div class="d-flex justify-content-end">
                                    <a class="os-btn os-btn-black me-3" @click="moveToCheckout()" style="font-size: 20px;font-weight: 500;">선택상품 주문하기</a>
                                    <a class="os-btn" @click="goMain()" style="font-size: 20px;font-weight: 500;">쇼핑 계속하기</a>
                                  </div>
                              </div>
                          </div>
                      </div>
                </form>
              </div>
          </div>
      </div>
    </section>
  </client-only>
</template>

<script setup>
import { defineComponent } from "vue";
import { useCartStore } from '@/store/useCart';
import { useUserStore } from '@/store/useUser';
import * as common_utils from "@/common/utils.ts";
import * as cartApi from '@/api';
import CookImage from '@/components/common/others/CookImage.vue'


const state = useCartStore();
const router = useRouter()

//로그인 정보
const store = useUserStore();
const nameS = store.getUserInfo?.NAME;
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const companyNameS = store.getUserInfo?.COMPANY_NAME;
const loginType = store.getUserInfo?.TYPE;//10-일반사용자(장바구니 사용가능), 20-사업자(관심상품, 장바구니 사용가능)
const isLogin = computed(()=>{
    return store.isLogin
})


//장바구니 리스트 정보
const cartList = reactive([])
const cartListLen = ref('')
let cartTotalCnt = ref(0)
let cartTotalAmount = ref(0);  

//체크박스 전체선택
const isAll = ref({})

//결제화면으로 보낼 상품목록 list
const checkedCartList = ref([])

//장바구니 갯수
const cartCnt = ref(1)

/****************************************************************************************** mounted */
onMounted(()=>{
    if(isLogin.value){
        doSearchCatrList()
    }
    isAll.value = true
    nextTick(() => {
			setTimeout(() => {checkAllRow()}, 100);
		});


})
/****************************************************************************************** method */
const goMain = () =>{
    router.push(`/`);
}

const clickPMBtn= async(col)=>{//수량 +- 버튼
    await doDelCartList(col)
    await doAddCatrList(col)
    await doSearchCatrList()
    $bus.$emit("chageCart")
}
const checkCartTotalAmount = computed(()=>{//총 합계 
    cartTotalAmount.value = 0
    cartCnt.value= 0
    for (let i = 0; i < cartList.length; i++) {  
        if(cartList[i].CHECK){
          cartCnt.value++
          cartTotalAmount.value += Number(cartList[i].AMOUNT * cartList[i].QTY);
        }
    }
    return cartTotalAmount.value
})
const delCartList=async(col) =>{//장바구니 삭제
    await doDelCartList(col)
    await doSearchCatrList()
    $bus.$emit("chageCart")
}
const moveToCheckout=() =>{//결제하기 버튼
    checkedCartList.value = []
    
    if(!isLogin.value){
        common_utils.fxAlert("로그인 후 이용해 주세요");
        //router.push(`/login`);//로그인화면으로 이동
        return
    }else{
        checkedCartList.value = cartList.filter((item) => item.CHECK === true) ;
        state.updateCheckedCartList(checkedCartList.value);
        let checkItCode = ''
        checkedCartList.value.map(item=>checkItCode += item.ITCODE+"/") 
        let orderNm = ''
        if(checkedCartList.value.length > 0){
            if(checkedCartList.value.length === 1){
                orderNm = `${checkedCartList.value[0].ITNAME}`
            }else{
                orderNm = `${checkedCartList.value[0].ITNAME} 외 ${checkedCartList.value.length-1}`
            }
        }
       
        state.setOrderProductNm(orderNm); 
        router.push(`/checkout?checkItCodes=${checkItCode.replace(/\/$/, '')}`);
    }
    
}
const chnageAllChk = (e) =>{//헤더부 전체선택
    cartList.map(item=>item.CHECK = isAll.value)
}
const checkAllRow = () =>{//장바구니 항목 전체 체크박스 체크
    cartList.forEach(row => {row.CHECK = true;});
}
/****************************************************************************************** api호출 */
const {$bus} = useNuxtApp();
const doAddCatrList = async (col, cnt)=>{//장바구니 상품 추가
    
      let data;
      let param = {
              cartNo : "",
              seq : "",
              clcode : clcodeS ?? "",
              userNo : userNoS ?? "",
              itcode : col.ITCODE,
              qty : col.QTY ?? 1,
              amount : Number(col.AMOUNT),
              inputUser : userNoS ?? ""//nameS ?? ""
      }
      try {
        const dataObj = await cartApi.cart_addCartList(param)
        data = dataObj.data
        /*if(data.RecordCount > 0){
          $bus.$emit("chageCart")
        }*/
      } catch (error) {
        console.error(error);
      }
}
const doDelCartList = async (col)=>{//장바구니 상품 삭제
    
    let data;
    let param = {
            cartNo : col.CART_NO ?? "",
            seq : col.SEQ ?? ""
    }
    try {
      const dataObj = await cartApi.cart_delCartList(param)
      data = dataObj.data
        /*if(data.ResultCode === '00'){
            $bus.$emit("chageCart")
        }*/
    } catch (error) {
      console.error(error);
    }
}
const doSearchCatrList = async ()=>{//장바구니 상품 조회
    cartList.splice(0);
    
    let data;
    let param = {
            clcode : clcodeS ?? "",
            userNo : store.getUserInfo?.USER_NO ?? ""
    }
    try {
      const dataObj = await cartApi.cart_getCartList(param)
      data = dataObj.data
      if(data.RecordCount > 0){
        cartList.push(...data.RecordSet);
        cartListLen.value = data.RecordCount 
        chnageAllChk()
        checkAllRow()//장바구니 항목 전체 체크박스 체크
      }else{
        localStorage.setItem("CART_NO","") 
      }
    } catch (error) {
      console.error(error);
    }
}

</script>

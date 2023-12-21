<template>
  <layout><!--{{ checkedCartList }}-->
    <section class="cart-area pt-90 pb-100 cus-mtop" v-if="cartList?.length > 0">
        <div class="section__title-wrapper text-center mb-55 p-relative">
            <div class="section__title mb-10">
                <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">주문/결제</h2>
            </div>
        </div>
        <div class="table-content table-responsive">
          <table class="table">
              <thead  style="height: 50px; font-size: 20px; vertical-align: middle; ">
                <th class="cart-product-name">대표이미지</th>
                <th class="cart-product-name">상품명</th>
                <th class="cart-product-name">금액</th>
                <th class="cart-product-name">수량</th>
              </thead>
              <tbody>
                  <tr v-for="(item,i) in cartList" :key="i">
                    <td class="product-thumbnail">
                        <!--<CookImage :image="item.ITEM_MAIN_IMAGE" :size="'imgmM'"/> -->
                        <CookImage v-if="item.hasOwnProperty('ITEM_MAIN_IMAGE')" :image="item.ITEM_MAIN_IMAGE" :size="'imgmM'"/>
                        <CookImage v-else :image="item.ITEM_MAIN_IMAGE" :size="'imgmM'"/>
                    </td>
                    <td class="product-name">
                        <span v-html="item.ITNAME"></span>
                    </td>
                    <td class="product-price">
                        <span class="amount">{{ common_utils.formattedPrice(item.AMOUNT) }} 원</span>
                    </td>
                    <td class="product-quantity">
                        <span class="amount">{{ common_utils.formattedPrice(item.QTY) }}</span>
                    </td>
                </tr>
              </tbody>
          </table>
        </div>
        <div class="row">
            <div class="col-md-5 ms-auto">
                <div class="cart-page-total">
                    <ul class="mb-20">
                        <li class="fw-bold" style="font-size: 24px;font-weight: 500 !important;color: #222222;">총 합계({{ common_utils.formattedPrice(cartList?.length) }}) :  <span>{{ common_utils.formattedPrice(cartTotalAmount) }}원</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <checkout-area :cartTotalAmount="cartTotalAmount" :checkItCodes="checkItCodes" :cartType="cartType" />
      </section>
  </layout>
</template>
<style>
.cart-product-name {
  color:#6641f8;
}
</style>
<script setup >
import * as common_utils from "@/common/utils.ts";
import Layout from "@/layout/LayoutFour.vue";
import CheckoutArea from "@/components/checkout/CheckoutArea.vue";
import { useCartStore } from "@/store/useCart";
import CookImage from '@/components/common/others/CookImage.vue'
import * as cartApi from '@/api';
import { useUserStore } from '@/store/useUser';
 

const store = useUserStore();
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO; 
const cartTotalAmount = computed(() => sumArrayValues());
const cartList = ref([])
const cartType = ref('10')
const checkItCodes  = ref([])
const sumArrayValues = ()=> {
  let sum = 0;
  for (let i = 0; i < cartList.value.length; i++) {
    sum += Number(cartList.value[i].AMOUNT);
  }
  return sum;
}

onMounted(()=>{
   cartType.value = useRoute().query.cartType ?? '10';
   checkItCodes.value = useRoute().query.checkItCodes; 
   doSearchCatrList() 
})

const doSearchCatrList = async ()=>{//장바구니 상품 조회
    cartList.value.splice(0);
    
    let data;
    let param = {
            type : cartType.value,
            clcode : clcodeS ?? "",
            userNo : userNoS ?? ""
    }
    try {
      const dataObj = await cartApi.cart_getCartList(param)
      data = dataObj.data
      if(data.RecordCount > 0){
        if(cartType.value === '20'){
          cartList.value.push(data.RecordSet[data.RecordSet.length-1]); 
        }else{
          let itemList ;
          if(checkItCodes.value){
             itemList = data.RecordSet.filter(item => checkItCodes.value.indexOf(item.ITCODE) > -1)
          }else{
             itemList = data.RecordSet
          }
          cartList.value.push(...itemList); 
        }
      }
    } catch (error) {
      console.error(error);
    }
}

</script>

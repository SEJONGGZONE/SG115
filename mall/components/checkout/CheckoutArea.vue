<template>
  <section class="checkout-area pt-60 pb-100">
    <div class="container">
      <form @submit.prevent="handleFormSubmit">
        <div class="row">
          <div class="col-lg-6">
            <div class="checkbox-form">
              <billing-details ref="billing" /><!--주문자정보-->
            </div>
          </div>
          <div class="col-lg-6">
            <order-area  ref="orderPayment" :cartTotalAmount="cartTotalAmount" @clickPayment="goPayment" /><!--결제정보-->
          </div>
        </div>
      </form>
    </div>
  </section>
</template>

<script>
import BillingDetails from "./BillingDetails.vue"; 
import OrderArea from "./OrderArea.vue";
import * as paymentApi from '@/api';
import { useCartStore } from '@/store/useCart';
import * as common_utils from "@/common/utils.ts";
import { ref, onMounted, getCurrentInstance } from 'vue'
import { array, string } from 'yup';
export default {
  setup(props, ctx) { 
       const state = useCartStore()
       const billing = ref(null) 
       const orderPayment = ref(null) 
       const orderName = state.getOrderProductNm; 
        return {
          billing,
          orderPayment,
          orderName
        }
  }, 
  props:{
    cartTotalAmount: {
      type: Number,
      default: 0,
      required: true,
    },
    checkItCodes: {
      type: String,
      default: "",
    },
    cartType:{
      type: String,
      default : '10'
    }
  },
  data(){
    return{
      orderId : ''
    }
  },
  components: { OrderArea, BillingDetails },
  mounted(){
    this.initOrderSave()
  },
  methods: {
    async initOrderSave(){//초기 데이터 저장
       
      console.log(this.checkItCodes)
      let params = this.billing.getParams(this.cartType)
      params.itemList = this.checkItCodes
      params.status = 10
      const data = await paymentApi.payment_saveOrder(params)
        if(data.RecordCount > 0){

          this.orderId = data.RecordSet[0].ORDERID
        }
    },
    handleFormSubmit() {
      console.log("submit form");
    },
    validateOrder(params){
      if(!params.address1 || !params.address2 || !params.postNo || !params.recvName || !params.recvPhone ){
        common_utils.fxAlertOk("배송 정보를 모두 입력해주세요.").then((result)=>{
            return false
        }) 
      }else{
        return true
      }
    },
    async goPayment(emitParam){//결제 진행하기
      const {orderAmount,discountAmount,paymentAmount,deliAmount,payType} = emitParam

      let params = this.billing.getParams(this.cartType)
      params.orderId = this.orderId
      if(!this.validateOrder(params)) return
      
         params.itemList = this.checkItCodes
        params.orderAmount = orderAmount
        params.discountAmount = discountAmount
        params.paymentAmount = paymentAmount
        params.deliAmount = deliAmount
        params.status = 20
        params.paymentType = payType
        
        try {
          const data = await paymentApi.payment_saveOrder(params)
          if(data.RecordCount > 0){ 
             if(payType === '10'){   
              useRouter().push({path:'/complete',query : {orderid:params.orderId , orderName:this.orderName,totalamount:paymentAmount}})
             }else{
               this.orderPayment.payment(params)
             }
          }
        } catch (error) {
          console.error(error);
        } 
    }
  },
};
</script>

<template>
    <div class="container11">
        <div class="content11">
            <span style="font-size:35px;">
                결제중입니다.
            </span> 
        </div>
        <div class="content"> 
            <div class="d-flex justify-content-center" >
                <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
  </div>
</template>
<script setup>
    
import * as common_utils from "@/common/utils.ts";
import * as paymentApi from '@/api';
import { useUserStore } from '@/store/useUser'; 

const userStore = useUserStore();
const userName = ref(userStore.getUserInfo?.NAME);
const route = useRoute();  
const paymentkey = ref(null)
const orderId = ref(null)
const amount = ref(null)
const orderName = ref(null)
onMounted( async ()=>{
    if(route.query.type === 'fail'){
      common_utils.fxAlertOk(route.query.message).then((result)=>{
            useRouter().push('/checkout')
        }) 
    }else{
        paymentkey.value = route.query.paymentKey
        orderId.value = route.query.orderId
        amount.value = route.query.amount
        orderName.value = route.query.orderName
        let statusParams = {
            paymentKey : paymentkey.value,
            status : 10,
            inputUser:userName.value
        } 
       await paymentApi.payment_paymentStatusUpdate(statusParams)//결제 상태 10으로 업데이트


       //결제 승인
       let confirmParams = {
            paymentKey : paymentkey.value,
            orderId : orderId.value ,
            amount :amount.value,
       }
       let confirmData = await paymentApi.payment_tossPaymentConfirm(confirmParams)//토스 결제 승인
       
       if(confirmData.code){//결제실패
            statusParams.status = 30
            await paymentApi.payment_paymentStatusUpdate(statusParams)//결제 성공 결제 실패 30으로 업데이트 
            common_utils.fxAlertOk(confirmData.message).then((result)=>{
                useRouter().push('/checkout')
            })  
       }else{//결제 성공  
            let payData = confirmData.data
            const paymentSaveParam = { 
                status : payData.status ,  
                paymentkey : paymentkey.value,  
                orderid : payData.orderId ,  
                ordername : orderName.value ,  
                bankcode : payData.bankcode ?? '',  
                accountno : payData.accountno ?? '',  
                card : payData.card.number ?? '',  
                secret : payData.secret,  
                receipt : payData.receipt.url ?? '',  
                totalamount : payData.totalAmount,  
                suppliedamount : payData.suppliedAmount,  
                vat : payData.vat,  
                taxfreeamount : payData.taxFreeAmount,  
                method : payData.method,  
                version : payData.version,  
                orgdata : JSON.stringify(payData)
            }
            const paymentSaveData = await paymentApi.payment_paymentSave(paymentSaveParam)  


            statusParams.status = 20
            await paymentApi.payment_paymentStatusUpdate(statusParams)//결제 성공 결제 상태 20으로 업데이트


            if(paymentSaveData.ResultMsg === 'SUCCESS'){ 
                
                useRouter().push({path:'/complete',query : {orderid:payData.orderId , orderName:orderName.value,totalamount:payData.totalAmount}})
                //   let params = {orderid:payData.orderId , orderName:orderName.value,totalamount:payData.totalAmount}
                // objectToQueryString(params);

                // location.href= '/complete?'+objectToQueryString(params)
            }else{ 
                  statusParams.status = 30
                  await paymentApi.payment_paymentStatusUpdate(statusParams)//결제 성공 결제 상태 20으로 업데이트
                  useRouter().push('/checkout')  
            }
       }   
    }

})

const objectToQueryString = (obj) =>{
  const keyValuePairs = [];

  for (const key in obj) {
    if (obj.hasOwnProperty(key)) {
      const encodedKey = encodeURIComponent(key);
      const encodedValue = encodeURIComponent(obj[key]);
      keyValuePairs.push(`${encodedKey}=${encodedValue}`);
    }
  }

  return keyValuePairs.join('&');
}
</script>

  <style>
    .container11 {
      display: flex;
      justify-content: center; /* 수평 가운데 정렬 */
       flex-direction: column; /* 요소들을 수직으로 배치하기 위해 column 설정 */
      align-items: center; /* 수직 가운데 정렬 */
      height: 35vh; /* 화면 전체 높이에 따라 조정 (뷰포트 높이) */
    }

    .content11 {
      /* 내용에 따라 필요한 스타일 적용 */
      margin: 20px; /* 위쪽과 아래쪽 간격을 20px로 설정 */
    }
  </style>
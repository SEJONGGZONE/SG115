<template>
   <div class="your-order mb-30 ">
        <h3>결제 정보</h3>
        <div class="your-order-table table-responsive">
            <table>
                <thead>
                   
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                    <tr class="cart-subtotal">
                        <th>총 상품 금액</th>
                        <td><span class="amount">{{ common_utils.formattedPrice(cartTotalAmount) }} 원</span></td>
                    </tr>
                    <tr class="shipping">
                        <th>배송유형 및 배송비</th>
                        <td>
                            <ul>
                                <li>
                                    <input v-model="ship_cost" value="1" id="flat-rate" disabled name="ship-cost" type="radio" @change="radioChange()" checked/>
                                    <label for="flat-rate">&nbsp;&nbsp;택배배송 : <span class="amount">3,000원</span></label>
                                </li>
                                <li>
                                    <input v-model="ship_cost" value="2" id="free" disabled name="ship-cost" type="radio" @change="radioChange()" />
                                    <label for="free">&nbsp;&nbsp;업체배송: 무료</label>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr class="order-total">
                        <th>총 결제 금액(배송비)</th>
                        <td>
                         <strong>
                            <span class="amount">{{ common_utils.formattedPrice(totalAmount) }} 원</span>
                        </strong>
                        </td>
                    </tr>
                    <tr class="shipping">
                        <th>결제 방법</th>
                        <td>
                            <ul>
                                <li>
                                    <input v-model="payType" value="10" id="account"   name="pay-type" type="radio"  checked/>
                                    <label for="account">&nbsp;&nbsp;계좌이체 <label style="font-weight:500">(농협 351-0777-4973-23)</label> </label> 
                                </li> 
                                <li>
                                    <input v-model="payType" value="20" id="credit"   name="pay-type" type="radio"  />
                                    <label for="credit">&nbsp;&nbsp;신용카드</label>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="payment-method"> 
            <div id="payment-method" v-show="payType === '20'">

            </div>
            <div class="order-button-payment mt-20">
                <button type="submit" class="os-btn os-btn-black" @click="goPayment()" style="font-size: 22px;font-weight: 500;">결제하기</button>
            </div>
        </div>
    </div>
</template>

<script setup> 
import { useCartStore } from '@/store/useCart'; 
import * as common_utils from "@/common/utils.ts";
import { useUserStore } from '@/store/useUser';

const props = defineProps({
    cartTotalAmount: {
        type : Number,
        default : []
    }
});
const state = useCartStore()
const userStore = useUserStore();
const userName = computed(()=>userStore.getUserInfo?.NAME);
const userEmail = computed(()=>userStore.getUserInfo?.EMAIL);
const userId = computed(()=>userStore.getUserInfo?.ID);
const userType = computed(()=>userStore.getUserInfo?.TYPE);
 
const ship_cost = ref('1')//1-택배, 2-업체배송
const payType = ref('10')//10 : 계좌이체, 20 : 카드
const cartTotalAmount = computed(() => props.cartTotalAmount);
const totalAmount = ref(1000)//cartTotalAmount+택배비
 
const paymentWidget = ref(null) 
const paymentMethodWidget = ref(null)

const checkedCartList = computed(() => state.getCheckedCartList());

onBeforeMount(()=>{
    const clientKey = 'live_ck_jkYG57Eba3GAykkxjlErpWDOxmA1' // 테스트용 클라이언트 키
    const customerKey = userId.value // 내 상점에서 고객을 구분하기 위해 발급한 고객의 고유 ID
    
    paymentWidget.value = PaymentWidget(clientKey, customerKey) // 회원 결제
})

onMounted(async ()=>{  
    
    await initData()
    paymentMethodWidget.value =  paymentWidget.value.renderPaymentMethods("#payment-method", { value: totalAmount.value });
})

const initData = () =>{
    if(userType.value === '10'){ // 개인은 택배비 3000원 부과
        ship_cost.value = '1'
    }else{// 사업자는 무료
        ship_cost.value = '2'
    }
    radioChange()
}


const emit = defineEmits(["clickPayment"]);
const goPayment = ()=>{
    let deliAmount = 0
    if(ship_cost.value === '1'){//개인은 3000원 적용
        deliAmount = 3000
    }
    const paymentObject = {
        orderAmount : cartTotalAmount.value,
        discountAmount : 0,
        paymentAmount : totalAmount.value,
        payType : payType.value,
        deliAmount : deliAmount
    }
    emit('clickPayment', paymentObject) 
}

const payment = (params)=>{ 

    paymentMethodWidget.value.updateAmount(params.paymentAmount)
    const orderName = state.getOrderProductNm; 
   
    paymentWidget.value.requestPayment({ 
        orderId: params.orderId,
        orderName: orderName,
        successUrl:  location.origin+'/callback?type=success&orderName='+orderName,
        failUrl:  location.origin+'/callback?type=fail',
        customerEmail: userEmail.value,
        customerName: userName.value,
    }) .catch((error)=> {
        if (error.code === 'INVALID_ORDER_NAME') {
        // 유효하지 않은 'orderName' 처리하기
        } else if (error.code === 'INVALID_ORDER_ID') {
        // 유효하지 않은 'orderId' 처리하기
        }
  })
} 
const radioChange = () =>{//라디오버튼 선택 변경

    if(ship_cost.value === '1'){//택배
        totalAmount.value = cartTotalAmount.value+Number(3000)
    }else{//업체배송
        totalAmount.value = cartTotalAmount.value
    }
}
 


defineExpose({
  payment
})
</script>

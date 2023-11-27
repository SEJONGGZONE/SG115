
<script setup >

import Layout from "@/layout/LayoutFour.vue"; 
import * as common_utils from "@/common/utils.ts";
const route = useRoute();   
const orderid = ref(null)
const orderName = ref(null)
const totalamount = ref(null) 
onMounted(()=>{  
    orderid.value = route.query.orderid
    orderName.value = route.query.orderName
    totalamount.value = route.query.totalamount
})

const formmatAmt = computed(()=>common_utils.formattedPrice(totalamount.value)) 
const goOrderDetail = ()=>{ 
    useRouter().push('/orderListHistory')   
}
</script>

<template> 
        <section class="product__area pt-100 pb-100">
            <div class="custom-container">
                <!--헤더-->
                <div class="row">
                    <div class="col-xl-12">
                    <div class="section__title-wrapper text-center mb-55 p-relative">
                        <div class="section__title mb-10">
                        <h2 class="grey-bg" style="font-size: 30px;font-weight: 500;line-height: unset;">주문완료</h2>
                        </div>
                    </div>
                    </div>
                </div>
                <!--본문-->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="profile__menu-right">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-account" role="tabpanel" aria-labelledby="nav-account-tab">
                                    <div class="profile__info"> 
                                        <div class="profile__info-wrapper white-bg">
                                            <div class="profile__info-item">
                                                <p><i class="fal fa-check"></i> 주문번호</p>
                                                <h4>{{orderid}}</h4>
                                            </div>
                                            <div class="profile__info-item">
                                                <p><i class="fal fa-check"></i> 주문명</p>
                                                <h4>{{orderName}}</h4>
                                            </div>
                                            <div class="profile__info-item" >
                                                <p><i class="fal fa-check"></i> 결제정보</p>
                                                <h4>결제 금액 : {{formmatAmt}} 원</h4>
                                            </div>
                                            <!-- <div class="profile__info-item">
                                                <p><i class="fal fa-check"></i> 주문 및 배송정보</p>
                                                <div>
                                                    <ul>
                                                        <li>
                                                            <span>주문자 : 김회원</span>
                                                        </li>
                                                        <li>
                                                            <span>전화번호 : 010-1234-5678</span>
                                                        </li>
                                                        <li>
                                                            <span>배송지 : 서울시 용산구 한남동 한남더힐 107호 (12345)</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>  -->
                                        </div>
                                    </div>
                                </div>
                                 
                            </div>
                        </div>
                    </div>
                    <div class="order-button-payment mt-20" style="text-align:center;">
                        <button type="submit" class="os-btn os-btn-black" style="width:300px; font-size: 24px; height: 60px;" @click="goOrderDetail()">주문내역 확인하기</button>
                    </div>
                </div>
            </div>
        </section>      
</template> 
<template>   
    <div id="modal-overlay-cart" class="modal-overlay "> 
            <div class="modal-content">
                <div class="product__modal-wrapper p-relative">
                    <div class="product__modal-close p-absolute" style="top:-20px;right:-20px;">
                        <button  @click="closeModal"><i class="fal fa-times" style="font-size:24px;line-height:40px;"></i></button>
                    </div>
                    <div class="product__modal-inner" style="padding:10px;">
                        <div class="row"> 
                            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 col-12">
                                <div class="product__modal-box">
                                    <div class="tab-content mb-20" id="nav-tabContent">
                                        <div class="product__modal-img w-img">
                                            <!--<img :src="modalData.ITEM_MAIN_IMAGE ? modalData.ITEM_MAIN_IMAGE : noImg" alt="product_img">-->
                                            <CookImage id="mainImg" :image="active_img" :size="'imgmL'"/> 
                                        </div>
                                    </div>
                                    <nav>
                                      <div class="nav nav-tabs justify-content-between" id="cartModalNav">
                                         <template v-for="(image,index ) in json_img" v-bind:key="index">  
                                            <button  class="nav-item nav-link active" @mouseover="handleActiveImg(image?.URL, $event)"  style="margin-bottom: 5px;">
                                              <div class="product__nav-img "> 
                                                <cook-image :image="image?.URL" :size="'detailimg1'"></cook-image> 
                                              </div>
                                            </button> 
                                        </template>  
                                      </div>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 col-12">
                              <div class="product__modal-conten`">
                                  <h4>
                                    <nuxt-link :href="`/product-details/${modalData.ITCODE}`">
                                        <span v-html="modalData.ITNAME" style="font-size:30px;font-weight:500;"></span>
                                    </nuxt-link>
                                  </h4>
                                  <div class="product__price-2 mb-25">
                                    <span>{{common_utils.formattedPrice(modalData.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(modalData.FAVITEM_CNT) }}건(</span><star-icon :startNumber="favItemCntToStar(modalData.FAVITEM_CNT)"></star-icon><span>)</span>
                                  </div>
                                  <!--
                                  <div class="product__price-2 mb-25">
                                    <ul>
                                      <li style="font-size: 20px;color: #595959;line-height:35px;">(박스당 가격 : {{common_utils.formattedPrice(modalData.ITBOXSDAN)}})</li>
                                    </ul>
                                  </div>
                                  -->
                                  <div class="product__modal-des mb-30">
                                    <ul>
                                      <li style="font-size: 20px;color: #595959;line-height:35px;">규격 : {{ modalData.ITSTAN }}</li>
                                      <li style="font-size: 20px;color: #595959;line-height:35px;">박스입수량 : {{ modalData.ITBOX_IPQTY }} {{ modalData.ITUNIT }}</li>
                                      <li style="font-size: 20px;color: #595959;line-height:35px;">공급가 : {{ modalData.AMOUNT }}원 ({{modalData.ITTAX_GUBUN}} :  {{common_utils.formattedPrice(modalData.VAT)}})</li>
                                    </ul>
                                  </div>
                                  <div class="product__modal-form">
                                      <form>
                                          <div class="row">

                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-5 "  >
                                              <div class="product-quantity"> 
                                                <div class="cart-plus-minus">
                                                  <input type="text" v-model="cartCnt" style="font-size:20px;">
                                                    <div @click="cartCnt > 1 ? cartCnt-- : cartCnt = 1" class="dec qtybutton">-</div>
                                                    <div @click="cartCnt++" class="inc qtybutton">+</div>
                                                </div>
                                              </div>
                                              </div>
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-7">
                                              <div class="pro-cart-btn">
                                                  <a @click="addCart(modalData, cartCnt)" href="#" class="os-btn os-btn-black" style="width:250px;font-size:20px;font-weight: 500;">+ 장바구니 담기</a>
                                              </div>
                                              </div>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div> 
</template>






<script setup> 
import CookImage from '@/components/common/others/CookImage.vue'
import StarIcon from '~/components/common/others/StarIcon.vue'
import * as common_utils from "@/common/utils.ts";
import { useUserStore } from '@/store/useUser';
const router = useRouter()
const store = useUserStore();
import * as cartApi from '@/api';
  const isLogin = computed(()=>{
    return store.isLogin
})

const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;  
const props = defineProps({
    modalData: {
        type : Object,
        default : {}
    },
}); 
 

const cartCnt = ref(1)
const emit = defineEmits(["closeModal"]);  
const active_img = ref()//모달창 이미지
const json_img = ref()//모달창 이미지

onMounted(()=>{
  active_img.value = props.modalData.ITEM_MAIN_IMAGE; 
  if(props.modalData?.JSON_IMAGE_URL){
    json_img.value = JSON.parse(props.modalData?.JSON_IMAGE_URL)
    json_img.value.sort(function(a, b) {
              return a.seq - b.seq;
    });
  }
})
onUpdated(()=>{
  console.log(props.modalData)
 // active_img.value = props.modalData.ITEM_MAIN_IMAGE;
  // if(props.modalData?.JSON_IMAGE_URL){
  //   json_img.value.sort(function(a, b) {
  //             return a.seq - b.seq;
  //   });
  // }
  
})

let targetObj = '';
const handleActiveImg= (img, event)=>{
  
  const divElement = document.getElementById("cartModalNav")
  const buttons = divElement.querySelectorAll('button');

  buttons.forEach(button => {
    button.classList.remove('active');
  });
  
  active_img.value = img
  document.getElementById("mainImg").src = img

  targetObj = (event.target).closest('button')
  targetObj.classList.add("active");
}
const closeModal = ()=>{  
  emit('closeModal') // 부모에서는 @input에 쓴 메소드가 호출된다. 인수에value
}
const addCart= (col, cnt) =>{//장바구니 담기
      //로그인여부 확인하기
      if(!isLogin.value){
        common_utils.fxAlert("로그인 후 이용해 주세요");
        //router.push(`/login`);//로그인
        return
      }
      doAddCatrList(col,cnt)
      //useCartStore().add_cart_product(col.value)
} 
   const {$bus} = useNuxtApp();
  const doAddCatrList = async (col, cnt)=>{//장바구니 상품 추가
      
        let data;
        let param = {
          cartNo : "",
              seq : "",
              clcode : clcodeS ?? "",
              userNo : userNoS ?? "",
              itcode : col.ITCODE,
              qty : cnt ?? '1',
              amount : Number(col.AMOUNT.replace(/,/g, '')),
              inputUser : userNoS ?? ""//nameS ?? ""
        }
        try {
          const dataObj = await cartApi.cart_addCartList(param)
          data = dataObj.data
          if(data.RecordCount > 0){
            common_utils.fxAlert("장바구니 추가 되었습니다.")
            $bus.$emit("chageCart")
            cartCnt.value = 1
          }
        } catch (error) {
          console.error(error);
        }
  }
  
const favItemCntToStar=(faveItemCnt) =>{//관심상품 별 카운트 계산
    //8/23일 시트2 수정 요청사항(별 3개 이하일 경우 3개로 보이도록 수정)
    let star = Math.floor(faveItemCnt/10)
    if(star < 3){
      star = 3
    }
    return star
}
 
</script>
<style scoped>
.modal-overlay {
  max-width: 90%;
  width: 800px; 
  top: 0;
  left: 0;
  right: 0;
  bottom: 0; 
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.5); /* 배경을 어둡게 처리 */
  z-index: 1056;

  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  height: max-content;

  transform: translate(-50%, -40%); /* 화면 중앙으로 이동 */
  position: absolute;
  top: 550px;
  left: 50%;
  border: 1px solid black; 
  position:fixed;
}
</style>

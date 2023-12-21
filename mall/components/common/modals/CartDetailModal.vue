<template>   
     <a href="#" id="productOpen" data-bs-toggle="modal" data-bs-target="#productModalId" title="간편보기"  ><!--상세보기 모달창-->
        <i class="fal fa-search"></i>
    </a> 
     <!-- 모달 start-->
    <div class="modal fade" id="productModalId" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
                <div class="product__modal-wrapper p-relative">
                    <div class="product__modal-close p-absolute">
                        <button data-bs-dismiss="modal"><i class="fal fa-times"></i></button>
                    </div>
                    <div class="product__modal-inner">
                        <div class="row"> 
                            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 col-12">
                                <div class="product__modal-box">
                                    <div class="tab-content mb-20" id="nav-tabContent">
                                        <div class="product__modal-img w-img">
                                            <!--<img :src="modalInfo.ITEM_MAIN_IMAGE ? modalInfo.ITEM_MAIN_IMAGE : noImg" alt="product_img">-->
                                            <CookImage :image="modalInfo.ITEM_MAIN_IMAGE" :size="'imgmL'"/> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 col-12">
                              <div class="product__modal-conten`">
                                  <h4>
                                    <nuxt-link :href="`/product-details/${modalInfo.ITCODE}`">
                                        <span v-html="modalInfo.ITNAME"></span>
                                    </nuxt-link>
                                  </h4>
                                  <div class="product__price-2 mb-25">
                                    <span>{{common_utils.formattedPrice(modalInfo.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(modalInfo.FAVITEM_CNT) }}건(</span><star-icon :startNumber="favItemCntToStar(modalInfo.FAVITEM_CNT)"></star-icon><span>)</span>
                                  </div>
                                  <div class="product__price-2 mb-25">
                                    <ul>
                                      <li>(박스당 가격 : {{common_utils.formattedPrice(modalInfo.ITBOXSDAN)}})</li>
                                    </ul>
                                  </div>
                                  <div class="product__modal-des mb-30">
                                    <ul>
                                      <li>규격 : {{ modalInfo.ITSTAN }}</li>
                                      <li>박스입수량 : {{ modalInfo.ITBOX_IPQTY }} {{ modalInfo.ITUNIT }}</li>
                                      <li>공급가 : {{ modalInfo.AMOUNT }}원 ({{modalInfo.ITTAX_GUBUN}} :  {{common_utils.formattedPrice(modalInfo.VAT)}})</li>
                                    </ul>
                                  </div>
                                  <div class="product__modal-form">
                                      <form>
                                          <div class="pro-quan-area d-lg-flex align-items-center">
                                              <div class="product-quantity">
                                                <div class="cart-plus-minus">
                                                  <input type="text" v-model="cartCnt">
                                                    <div @click="cartCnt > 1 ? cartCnt-- : cartCnt = 1" class="dec qtybutton">-</div>
                                                    <div @click="cartCnt++" class="inc qtybutton">+</div>
                                                </div>
                                              </div>
                                              <div class="pro-cart-btn ml-20">
                                                  <a @click="addCart(modalInfo, cartCnt)" href="#" class="os-btn os-btn-black os-btn-3 mr-10">+ 장바구니 담기</a>
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
    </div>
</template>




<script >
import { defineComponent } from "vue";
import * as common_utils from "@/common/utils.ts";
import CookImage from '@/components/common/others/CookImage.vue'
import StarIcon from '~/components/common/others/StarIcon.vue'
 
export default defineComponent({
  components: { CookImage, StarIcon },
  props:{
    modalInfo: {
        type : Object,
        default : []
    },
    isOpen:{
        type : Boolean,
        default : false
    }
  },
  data() {
    return {
      cartCnt : 0
    };
  }, 
  watch:{
    isOpen(data){
        console.log(data)
    }
  },
  methods: {  
            addCart (col, cnt) {//장바구니 담기
            //로그인여부 확인하기
            if(!isLogin.value){
                common_utils.fxAlert("로그인 후 이용해 주세요");
                //window.location.href = `/login`;//로그인화면으로 이동
                return
            }
            this.doAddCatrList(col,cnt)
            //useCartStore().add_cart_product(col.value)
            },
            async doAddCatrList  (col, cnt){//장바구니 상품 추가
            
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
           }, 
            favItemCntToStar(faveItemCnt) {//관심상품 별 카운트 계산
                //8/23일 시트2 수정 요청사항(별 3개 이하일 경우 3개로 보이도록 수정)
              let star = Math.floor(faveItemCnt/10)
              if(star < 3){
                star = 3
              }
              return star
            }
  },
  setup() {
    return {};
  },
});
</script>
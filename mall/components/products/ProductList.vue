<template>
  <section class="product__area" :class="isShowHeader ? 'pt-90 pb-100' :'pt-30 pb-20'">
    <div class="custom-container">
      <div class="row" v-if="isShowHeader">
        <div class="col-xl-12">
          <div class="section__title-wrapper text-center p-relative">
            <div class="section__title" data-aos="fade-up" data-aos-once="true"> 
             <!--통합테스트_0814 - 4번 카테고리 정보 적용-->
              <h2 v-if="categoryNm" class="grey-bg" style="font-size:30px;font-weight: 500;line-height: 1;">{{ title }}<br><label style="font-size:22px; color:rgb(158 174 172);">{{categoryNm}}</label></h2> 
              <h2 v-else class="grey-bg" style="font-size:30px;font-weight: 500;line-height: unset;">{{ title }}</h2> 
            </div>
          </div>
        </div>
      </div>

    <!--리스트 영역-->
    <section class="shop__area " :class="isShowSort ? 'pt-30' :'pt-60 pb-30'"  >
      <div class="container">
          <div class="row">
              <div class="col-xl-12">
                  <div class="shop__content-area">
                      <div class="shop__header d-sm-flex justify-content-between align-items-center mb-40" v-if="isShowSort">

                        <div data-aos="fade-left" data-aos-once="true" class="shop__header-right d-flex align-items-center justify-content-between justify-content-sm-end">
                          <sort-filtering :item="productList" @update:item="updateList" />
                        </div>

                        <div data-aos="fade-right" data-aos-once="true" class="shop__header-right d-flex align-items-center justify-content-between justify-content-sm-end">

                          <!-- 사진형 또는 리스트형-->
                          <ul class="nav nav-pills" id="pills-tab" role="tablist">
                              <li class="nav-item">
                                  <!--사진형-->
                                  <a class="nav-link active" id="pills-grid-tab" data-bs-toggle="pill" href="#pills-grid" role="tab" aria-controls="pills-grid" aria-selected="true"><i class="fas fa-th"></i></a>
                              </li>
                              <li class="nav-item">
                                  <!--리스트형-->
                                  <a class="nav-link" id="pills-list-tab" data-bs-toggle="pill" href="#pills-list" role="tab" aria-controls="pills-list" aria-selected="false"><i class="fas fa-list-ul"></i></a>
                              </li>
                          </ul>

                        </div>
                      </div>
                      <!-- 리스트 영역 -->
                      <div v-if="productList.length>0" class="tab-content" id="pills-tabContent">
                          <!--사진형-->
                          <div class="tab-pane fade show active" id="pills-grid" role="tabpanel" aria-labelledby="pills-grid-tab">
                              <div class="row custom-row-12">
                                <div v-for="(col,i) in productList" :key="i" class="pt-10 col-xl-3 col-lg-3 col-md-6 col-sm-6 custom-col-10 col-6 radius ">
                                  <div class="product__wrapper mb-60">
                                      <div class="product__thumb pretty_box" style="border:0 solid #ffffff">
                                          <nuxt-link :href="`/product-details/${col.ITCODE}`" class="w-img">
                                              <!--<img class="img" :src="col.ITEM_MAIN_IMAGE ? col.ITEM_MAIN_IMAGE : noImg" alt="product-img">-->
                                              <!--<CookImage :image="col.ITEM_MAIN_IMAGE ? col.ITEM_MAIN_IMAGE : (col.ITEM_MAIN_IMAGE ? col.ITEM_MAIN_IMAGE : (col.BACK_IMAGE ? col.BACK_IMAGE : ''))" :size="'imgmXL'"/> -->
                                              <!--<CookImage :image="col.ITEM_MAIN_IMAGE" :size="'imgmXL'"/>-->
                                              <CookImage :image="col.ITEM_MAIN_IMAGE" :size="'imgmXL'" alt="product-img"/>
                                              <CookImage :image="col.ITEM_SECOND_IMAGE" :size="'imgmXL'" alt="product-img" class="product__thumb-2" style="background-color: white;"/>
                                          </nuxt-link>
                                          <div class="product__action transition-3">
                                              <a v-if="isLogin && loginType==='20'" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="관심상품" @click="doWishList(col)"><!--관심상품-->
                                                  <i :class="{'fa fa-heart': col.CHK_FAVORITE === 'Y','fal fa-heart': col.CHK_FAVORITE === 'N'}"></i>
                                              </a>
                                              <!--돋보기 모달창-->
                                              <template v-if="multiComponent">
                                                <!--
                                                  --23.08.20 메인(index.vue)에서는 돋보기 모달을 띄우지 않는다.
                                                  <a @click="openModal(col)">
                                                    <i class="fal fa-search" style="font-weight: 500;color: #222222;cursor: pointer;"></i>
                                                </a>
                                                -->
                                              </template>
                                              <template v-else>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#productModalId" title="간편보기" @click="openModal(col)"><!--상세보기 모달창-->
                                                    <i class="fal fa-search" style="font-weight: 500;color: #222222;cursor: pointer;"></i>
                                                </a>
                                              </template>
                                          </div>
                                      </div>
                                      <div class="product__content p-relative">
                                          <div class="product__content-inner">
                                            <nuxt-link :href="`/product-details/${col.ITCODE}`">
                                              <span v-html="col.ITNAME" style="font-size: 18px;font-weight: 500;color: #222222;"></span>
                                            </nuxt-link>
                                              <div class="product__price transition-3">
                                                  <span>{{common_utils.formattedPrice(col.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(col.FAVITEM_CNT) }}건</span>
                                              </div>
                                          </div>
                                          <div class="add-cart p-absolute transition-3">
                                              <a @click="addCart(col,1)" href="#" style="font-size: 16px;">+ 장바구니 담기</a>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                              </div>
                          </div>
                          <!--리스트형-->
                          <div class="tab-pane fade" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab" >
                              <div class="product__wrapper mb-40" v-for="(col,i) in productList" :key="i">
                                <div class="row mb-10" >
                                    <div class="col-xl-4 col-lg-4">
                                      <div class="product__modal-box d-flex pretty_box align-items-center justify-content-center">         
                                        <div class="tab-content" id="product-detailsContent" >
                                              <div class="product__modal-img product__thumb h-100" style="border: 2px solid #ebebeb; text-align: center; padding-bottom: 0px;">
                                                  <nuxt-link :href="`/product-details/${col.ITCODE}`" class="w-img">
                                                      <CookImage :image="col.ITEM_MAIN_IMAGE" :size="'detailimg'" alt="product-img" :id="col.ITCODE"/>
                                                      <!-- <CookImage :image="col.ITEM_SECOND_IMAGE" :size="'imgmXL'" alt="product-img" class="product__thumb-2"/> -->
                                                  </nuxt-link>
                                              </div>
                                          </div>
                                      </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2">
                                      <div class="product__modal-box d-flex align-items-center justify-content-center">         
                                        <ProductThumbnail :itCode="col?.ITCODE" :jsonData="col?.JSON_IMAGE_URL"  @changeListImage="changeListImage"/>
                                      </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="product__content p-relative">
                                            <div class="product__content-inner list">
                                                <h4>
                                                  <nuxt-link :href="`/product-details/${col.ITCODE}`">
                                                    <span v-html="col.ITNAME" style="font-size:24px;font-weight:500;"></span>
                                                  </nuxt-link>
                                                </h4>
                                                <div class="product__price-2 mb-10">
                                                  <span style="font-size:18px;color:#595959;">{{common_utils.formattedPrice(col.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(col.FAVITEM_CNT) }}건</span>
                                                </div>
                                                <div class="product__modal-des mb-30">
                                                  <ul>
                                                    <li style="font-size: 16px;color: #595959;">규격 : {{ col.ITSTAN }}</li>
                                                    <li style="font-size: 16px;color: #595959;">박스입수량 : {{ col.ITBOX_IPQTY }} {{ col.ITUNIT }}</li>
                                                    <li style="font-size: 16px;color: #595959;">공급가 : {{ col.AMOUNT }}원({{col.ITTAX_GUBUN}} :  {{common_utils.formattedPrice(col.VAT)}})</li>
                                                  </ul>
                                                </div>
                                            </div>
                                            <div class="add-cart-list d-sm-flex align-items-center">  
                                                <a href="#" class="add-cart-btn mr-10" @click="addCart(col,1)" style="font-size:20px;">+ 장바구니 담기</a>
                                                <div class="product__action-2 transition-3 mr-20">
                                                  <a v-if="isLogin && loginType==='20'" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="관심상품" @click="doWishList(col)"><!--관심상품-->
                                                    <i :class="{'fa fa-heart': col.CHK_FAVORITE === 'Y','fal fa-heart': col.CHK_FAVORITE === 'N'}"></i>
                                                  </a>
                                                  <!--8/3일 쇼핑몰 수정요청 사항-->
                                                  <!--상세보기 모달창-->
                                                  <!--<a href="#" data-bs-toggle="modal" data-bs-target="#productModalId" title="간편보기" @click="openModal(col)">
                                                      <i class="fal fa-search" style="font-weight:500;"></i>
                                                  </a>-->
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div v-else class="shop__content-area" style="text-align: center;"><span>{{ result }}</span></div>
                  </div>
              </div>
          </div>
      </div>
    </section>

    <!-- 모달 start-->
    <div class="modal fade" id="productModalId" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered product-modal" role="document">
            <div class="modal-content">
                <div class="product__modal-wrapper p-relative">
                    <div class="product__modal-close p-absolute">
                        <button data-bs-dismiss="modal"><i class="fal fa-times" style="font-size:24px;line-height:40px;"></i></button>
                    </div>
                    <div class="product__modal-inner">
                        <div class="row">
                            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 col-12">
                                <div class="product__modal-box">
                                    <div class="tab-content mb-20" id="nav-tabContent">
                                        <div class="product__modal-img w-img">
                                            <!--<img :src="modalInfo.ITEM_MAIN_IMAGE ? modalInfo.ITEM_MAIN_IMAGE : noImg" alt="product_img">-->
                                            <CookImage :image="active_img" :size="'imgmL'"/>
                                        </div>
                                    </div>
                                    <nav>
                                      <div class="nav nav-tabs justify-content-center"  id="modalNav">
                                        <template v-for="(image,index ) in json_img" v-bind:key="index">  
                                            <button  class="nav-item nav-link mb-5" :class="index===0 ? 'active': ''" @mouseover="handleActiveImg(image?.URL, $event,1)">
                                              <div class="product__nav-img "> 
                                                <cook-image :image="image?.URL" :size="'imgmXS'"></cook-image> 
                                              </div>
                                            </button> 
                                        </template>  
                                      </div>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 col-12">
                              <div class="product__modal-content">
                                  <h4>
                                    <!--<nuxt-link :href="`/product-details/${modalInfo.ITCODE}`">-->
                                        <span v-html="modalInfo.ITNAME" style="font-size:30px;font-weight:500;"></span>&nbsp;&nbsp;
                                        <br>
                                    <!--</nuxt-link>-->
                                    <a v-if="isLogin && loginType==='20'" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="관심상품" @click="doWishList(modalInfo)"><!--관심상품-->
                                      <span style="font-size: 20px; color: #848b8a; font-weight: 500; margin-right: 7px;line-height: 35px;">
                                        <i :class="{'fa fa-heart': modalInfo.CHK_FAVORITE === 'Y','fal fa-heart': modalInfo.CHK_FAVORITE === 'N'}"></i>
                                        관심상품추가
                                      </span>
                                    </a>
                                  </h4>
                                  <div class="product__price-2 mb-25">
                                    <span>{{common_utils.formattedPrice(modalInfo.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(modalInfo.FAVITEM_CNT) }}건(</span><star-icon :startNumber="favItemCntToStar(modalInfo.FAVITEM_CNT)"></star-icon><span>)</span>
                                  </div>
                                  <!--
                                  <div class="product__price-2 mb-25">
                                    <ul>
                                      <li style="font-size: 20px;color: #595959;line-height: 35px;">(박스당 가격 : {{common_utils.formattedPrice(modalInfo.ITBOXSDAN)}})</li>
                                    </ul>
                                  </div>
                                  -->
                                  <div class="product__modal-des mb-30">
                                    <ul>
                                      <li style="font-size: 20px;color: #595959;line-height: 35px;">규격 : {{ modalInfo.ITSTAN }}</li>
                                      <li style="font-size: 20px;color: #595959;line-height: 35px;">박스입수량 : {{ modalInfo.ITBOX_IPQTY }} {{ modalInfo.ITUNIT }}</li>
                                      <li style="font-size: 20px;color: #595959;line-height: 35px;">공급가 : {{ modalInfo.AMOUNT }}원 ({{modalInfo.ITTAX_GUBUN}} :  {{common_utils.formattedPrice(modalInfo.VAT)}})</li>
                                    </ul>
                                  </div>
                                  <div class="product__modal-form">
                                      <form>
                                          <div class="pro-quan-area d-lg-flex align-items-center">
                                              <div class="product-quantity">
                                                <div class="cart-plus-minus">
                                                  <input type="text" v-model="cartCnt" style="font-size:20px;">
                                                    <div @click="cartCnt > 1 ? cartCnt-- : cartCnt = 1" class="dec qtybutton">-</div>
                                                    <div @click="cartCnt++" class="inc qtybutton">+</div>
                                                </div>
                                              </div>
                                              <div class="pro-cart-btn mw-100 ml-20">
                                                  <a @click="addCart(modalInfo, cartCnt)" href="#" class="os-btn os-btn-black os-btn-3 mr-10" style="font-size:20px;font-weight: 500;">+ 장바구니 담기</a>
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
    <!-- 모달 end -->


    </div>
  </section>
</template>

<script setup>

import { array, string } from 'yup';
import * as cartApi from '@/api';
import * as listApi from '@/api';
import { useUserStore } from '@/store/useUser';
import SortFiltering from '@/components/shop/filter-widget/SortFiltering.vue';
import CookImage from '@/components/common/others/CookImage.vue'
import ProductThumbnail from '@/components/products/ProductThumbnail.vue'
import StarIcon from '~/components/common/others/StarIcon.vue'
import * as common_utils from "@/common/utils.ts";
import AOS from "aos";

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
const router = useRouter()
//장바구니 갯수
const cartCnt = ref(1)

const props = defineProps({
  productList: {
    type : Array,
    default : []
  },
  title : {
    type : String,
    default:""
  },
  isShowHeader:{
      type:Boolean,
      default:true
  } ,
  isShowSort:{
      type:Boolean,
      default:true
  } ,
  multiComponent:{
     type : Boolean,
    default : false
  },
  categoryNm :{
    type : String,
    default : ''
  }
});

const modalInfo = ref({})//돋보기버튼_상세모달창
const active_img = ref()//모달창 이미지
const json_img = ref()//모달창 이미지
const result = ref()//상품이 없습니다.

onMounted(()=>{ 
  nextTick(() => {
			setTimeout(() => {
        if(props.productList.length === 0){
          result.value = "상품이 없습니다."
        }
      }, 2000);
		});

  
})
/****************************************************************************************** method */

const changeListImage = (itCode,image) =>{ 
  document.getElementById(itCode).src= image 
}

let targetObj = '';
const handleActiveImg= (img, event, code)=>{

  let divElement = ''
  if(code === 1){
    divElement = document.getElementById("modalNav")
  }else{
    divElement = (event.target).closest('div');
  }

  const buttons = divElement.querySelectorAll('button');

  buttons.forEach(button => {
    button.classList.remove('active');
  });

  active_img.value = img

  targetObj = (event.target).closest('button')
  targetObj.classList.add("active");
} 
const addCart= (col, cnt) =>{//장바구니 담기
      //로그인여부 확인하기
      if(!isLogin.value){
        common_utils.fxAlert("로그인 후 이용해 주세요");
        //router.push(`/login`);//로그인화면으로 이동
        return
      }
      doAddCatrList(col,cnt)
      //useCartStore().add_cart_product(col.value)
}

const doWishList=(col) =>{//위시리스트 담기/제거
    
    //8/3일 수정사항에 WEB_FAVITEM_SAVE만으로 관심상품 추가,삭제가 가능하다고 나와있음
    /*if(col.CHK_FAVORITE === 'N'){//담기(N->Y)
      doAddWishList(col)
      col.CHK_FAVORITE = 'Y'
    }else{//제거(Y->N)
      doDelWishList(col)
      col.CHK_FAVORITE = 'N'
    }*/
    if(col.CHK_FAVORITE === 'N'){//담기(N->Y)
      col.CHK_FAVORITE = 'Y'
    }else{//제거(Y->N)
      col.CHK_FAVORITE = 'N'
    }
    doAddWishList(col)
}
const emit = defineEmits(["changeProduct","showDetailModal","doSearchfavList"]);
const updateList = (updatedItem) => {//정렬 후 업데이트된 리스트

  emit('changeProduct', updatedItem) // 부모에서는 @input에 쓴 메소드가 호출된다. 인수에value
}
const openModal = (obj) => { //관심상품 영역_상품 돋보기버튼 클릭
    cartCnt.value = 1
    if(props.multiComponent){
      emit('showDetailModal', obj) // 부모에서는 @input에 쓴 메소드가 호출된다. 인수에value
    }else{
       modalInfo.value = obj;
    }
    
    active_img.value = obj.ITEM_MAIN_IMAGE
    if(obj?.JSON_IMAGE_URL){
      json_img.value = JSON.parse(obj?.JSON_IMAGE_URL)
      json_img.value.sort(function(a, b) {
                return a.seq - b.seq;
      });
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
/****************************************************************************************** api호출 */
const doAddWishList = async (col)=>{//관심상품 상품 추가

    let data;
      let param = {
              clcode : clcodeS ?? "",
              itcode : col.ITCODE,
              itname : col.ITNAME
      }
      try {
        data = await listApi.list_addFavList(param)
        if(data.ResultCode === '00'){
          if(col.CHK_FAVORITE === 'Y'){
            common_utils.fxAlert("관심상품 목록에 추가 되었습니다.",{type:'',timer:1000})
          }else{
            common_utils.fxAlert("관심상품 목록에 제거 되었습니다.",{type:'',timer:1000})
          }
        }
      } catch (error) {
        console.error(error);
      }finally{
        emit('doSearchfavList')
      }
  }
  const doDelWishList = async (col)=>{//관심상품 상품 삭제

      let data;
      let param = {
              clcode : clcodeS ?? "",
              itcode : col.ITCODE
      }
      try {
        data = await listApi.list_delFavList(param)
        if(data.ResultCode === '00'){
          common_utils.fxAlert("관심상품 목록에 제거되었습니다.",{type:'error',timer:1000});
        }
      } catch (error) {
        console.error(error);
      }
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
</script>

<style scoped>
.radius{
  border-radius: 0.6rem!important

}
</style>



<template>
  <!--navicate 영역--> 
  <div class="breadcr mt-90">{{ category1nm }} > {{ category2nm }}</div>
  <!-- 카테고리2 영역-->
  <div class="brand__area non-mobile">
      <div class="container custom-container-2">
        <div class="brand__slider-active p-relative mt-20">
          <Carousel
            ref="slider_1"
            :items-to-show="category2List.length < 9 ? category2List.length : 9" 
            :wrap-around="category2List.length < 9 ? isMobile ? true : false : true"
            :snapAlign="category2List.length < 9 ? 'start' : 'center'"
            :model-value="currentSlide"
            :breakpoints="{  
              1100: {
                itemsToShow: 6,
              }, 
              992: {
                itemsToShow: 5,
              }, 
              750: {
                itemsToShow: 4,
              },
              700: {
                itemsToShow: 3,
              },
              550: {
                itemsToShow: 2,
              },
              400: {
                itemsToShow: 2,
              },
              0: {
                itemsToShow: 1,
              },
            }"
          >
            <Slide v-for="(col, i) in category2List" :key="i" class="brand__slider-item" >
              <div class="product__load-btn text-center" > 
                <button class="os-btn" type="submit" :style="`border:none; width: 200px; font-size: 20px; font-weight: 500; padding: 0; ${category2com === col.CODE ? 'border: 2px solid #222222':''}`" @click="navigateToCategoryList(col.CODE, col.NAME)">{{ col.NAME }}</button>
              </div>
            </Slide>
          </Carousel>
          <div class="owl-nav" v-if="category2List.length < 5 ? isMobile ? true : false : true"  style="visibility: visible; opacity: 1">
            <div @click="handlePrev" class="owl-prev">
              <button><i class="fal fa-angle-left"></i></button>
            </div>
            <div @click="handleNext" class="owl-next">
              <button><i class="fal fa-angle-right"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- 모바일 카테고리 영역--> 
  <div class="scrollmenu mobile_brand__slider">
    <a v-for="(col, i) in category2List" :key="i">
        <button class="os-btn" type="submit" :style="`${category2com === col.CODE ? 'border: 1px solid #222222':''}`" @click="navigateToCategoryList(col.CODE, col.NAME)">{{ col.NAME }}</button>
    </a>
  </div>
  <section class="shop__area pb-65">
        <div class="shop__top grey-bg-6 pt-100 pb-90" >
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-4">
                        <div class="product__modal-box d-flex align-items-center justify-content-center">         
                          <div class="tab-content" id="product-detailsContent" >
                                <div class="product__modal-img product__thumb h-100" style="border: 2px solid #ebebeb; text-align: center; padding-bottom: 0px;">
                                  <!--<img class="detailimg" :src="item?.ITEM_MAIN_IMAGE ? item?.ITEM_MAIN_IMAGE : noImg" />-->
                                  <cook-image :image="active_img" :size="'detailimg'"></cook-image> 
                                </div>
                          </div>
                        </div>
                    </div> 
                    <div class="col-xl-2 col-lg-2">     
                        <div class="product__modal-box d-flex align-items-center justify-content-center">         
                          <!--
                            <nav>
                                <div class="nav nav-tabs set-nav" id="product-details" role="tablist">
                                  <template v-for="(image,index ) in json_img" v-bind:key="index">  
                                      <button  class="nav-item nav-link mb-5" :class="index===0 ? 'active': ''" @mouseover="handleActiveImg(image?.URL, $event)">
                                        <div class="product__nav-img "> 
                                          <cook-image :image="image?.URL" :size="'detailimg1'"></cook-image> 
                                        </div>
                                      </button> 
                                  </template> 
                                </div>
                            </nav>
                            -->
                            <ProductThumbnail :itCode="itemItcode" :jsonData="json_img_url"  @changeListImage="changeListImage"/>
                        </div>
                        
                    </div>
                    <div class="col-xl-6 col-lg-6">
                      
                      <div class="product__modal-content product__modal-content-2">
                        <h4>
                          <!--<nuxt-link :href="`/product-details/${item.ITCODE}`">-->
                              <span data-aos="fade-left" data-aos-once="true" v-html="item?.ITNAME" style="font-size:30px;font-weight:500;"></span>&nbsp;&nbsp;
                          <!--</nuxt-link>-->
                          <span v-if="item?.ITORDERMIN > 1">
                              D-{{ item?.ITORDERMIN }}
                          </span>
                          <a v-if="isLogin && loginType==='20'" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="관심상품" @click="doWishList(item)"><!--관심상품-->
                            <span data-aos="fade-left" data-aos-once="true" data-aos-delay="100" class="mobile-changLine-display-block" style="font-size: 20px; color: #848b8a; font-weight: 500; margin-right: 7px">
                              <i :class="{'fa fa-heart': item?.CHK_FAVORITE === 'Y','fal fa-heart': item.CHK_FAVORITE === 'N'}"></i> 
                              <span v-if="item?.CHK_FAVORITE === 'Y'"> 관심상품제거</span>
                              <span v-else> 관심상품추가</span>
                            </span>
                          </a>
                        </h4>
                        <div class="product__price-2 mb-25"> 
                          <span data-aos="fade-left" data-aos-once="true" data-aos-delay="200" >{{common_utils.formattedPrice(item?.ITEASDAN)}} 원  / 관심수 : {{ common_utils.formattedPrice(item?.FAVITEM_CNT) }}건</span>
                          <span data-aos="fade-left" data-aos-once="true" data-aos-delay="300" class="mobile-changLine-display-block">(<star-icon :startNumber="favItemCntToStar(item.FAVITEM_CNT)"></star-icon>)</span>
                        </div>
                        <!--
                        <div class="product__price-2 mb-25">
                          <ul>
                            <li style="font-size: 20px;color: #595959;line-height: 35px;">(박스당 가격 : {{common_utils.formattedPrice(item.ITBOXSDAN)}})</li>
                          </ul>
                        </div>
                        -->
                        <div class="product__modal-des mb-30">
                          <ul>
                            <li data-aos="fade-left" data-aos-once="true" data-aos-delay="400" style="font-size: 20px;color: #595959;line-height: 35px;">규격 : {{ item?.ITSTAN }}</li>
                            <li data-aos="fade-left" data-aos-once="true" data-aos-delay="500" style="font-size: 20px;color: #595959;line-height: 35px;">박스입수량 : {{ item?.ITBOX_IPQTY }} {{ item?.ITUNIT }}</li>
                            <li data-aos="fade-left" data-aos-once="true" data-aos-delay="600" style="font-size: 20px;color: #595959;line-height: 35px;">공급가 : {{ item?.AMOUNT }}원 ({{item?.ITTAX_GUBUN}} :  {{common_utils.formattedPrice(item?.VAT)}}원)</li>
                          </ul>
                        </div>
                        <div class="product__modal-form">
                            <form action="#">
                                <div class="pro-quan-area d-lg-flex align-items-center mb-2">
                                    <div data-aos="fade-left" data-aos-once="true" data-aos-delay="700" class="product-quantity">
                                      <div class="cart-plus-minus">
                                        <input type="text" v-model="cartCnt" style="font-size:20px;">
                                          <div @click="cartCnt > 1 ? cartCnt-- : cartCnt = 1" class="dec qtybutton">-</div>
                                          <div @click="cartCnt++" class="inc qtybutton">+</div>
                                      </div>
                                    </div>
                                </div>
                                <div class="pro-cart-btn" data-aos-delay="800" style="width: 100%;">
                                    <a data-aos="flip-down" data-aos-once="true" @click="addCart(item, cartCnt)" href="#" class="os-btn os-btn-black os-btn-3 mr-10 mt-10" style="cursor: pointer;font-size:20px;font-weight: 500;">
                                      <i class="fas fa-cart-plus" data-aos-delay="900" ></i>&nbsp;장바구니
                                    </a>
                                    <a data-aos="flip-down" data-aos-once="true" data-aos-delay="1000" @click="buyNow(item, cartCnt)" class="os-btn os-btn-black os-btn-3 mr-10 mt-10" style="cursor: pointer;font-size:20px;font-weight: 500;">
                                      <i class="fas fa-credit-card"></i>&nbsp;바로구매
                                    </a>
                                </div>
                            </form>
                        </div>
                      </div>
                    </div>
                    
  
                </div>
              
            </div>
        </div> 

        <!--7/31 요구사항 - 상품대표이미지, 배송정책(deliveryPolicy.png)-->
        <div style="border-top: 2px solid #222222;">

        </div>
        <div>
          <div class="tab-content" id="product-detailsContent" >
              <div class="product__modal-img product__thumb h-100" style="border: 2px solid #ebebeb; text-align: center;">
                 <!--통합테스트_0814 - 4번 추가 이미지 모두 노출-->
                <template v-for="(item,index) in json_img_detail" v-bind:key="index">
                  <div data-aos="fade-left" data-aos-once="true" :data-aos-delay="index*200" class="mt-40 dImage"><cook-image :image="item?.URL" :size="'size-30'"></cook-image></div>
                </template>
                
                <div class="mt-60" style="justify-content: center; align-items: center;" >
                  <!-- 8/25 요구사항 - html로 변경
                    <img class="dPolicy" :src="deliveryImg" style="width: 60%;"/>
                  -->
                  <h1 class="delivery-title" data-aos="fade-left" data-aos-once="true">배송안내 및 배송정책</h1>
                  <br>
                  <br>
                  <div class="delivery-info" style="text-align: left;margin-left: auto; margin-right: auto; font-size: 18px; line-height: 2.5;">
                      <h3 data-aos="fade-right" data-aos-once="true" style="border-bottom:2px solid #222222;padding-bottom:3px;">배송안내</h3><br>
                      <div data-aos="fade-right" data-aos-once="true">
                        <i class="fas fa-circle fa-xs"></i> 당사는 로젠택배 또는 무료 직배송을 서비스하고 있습니다.<br>
                        <i class="fas fa-circle fa-xs"></i> 무료 직배송 가능지역 : 
                                              <span style="color: red;">대전, 세종, 청주, 천안, 아산</span><br>
                                              &nbsp;&nbsp;&nbsp;(<span style="color: red;">일부 지역에 대해서는 직배송이 어려울 수 있습니다.  배송가능 문의는 010-5014-774로 전화 주시면 상세히 안내 드리겠습니다.</span>)<br>
                        <i class="fas fa-circle fa-xs"></i> 오후 2시까지 주문 들어온 상품은 당일 발송 됩니다.<br>
                                              &nbsp;&nbsp;&nbsp;(단, 프리오더의 경우는 주문일자에 따라서 도착예정일이 달라집니다)<br>
                        <i class="fas fa-circle fa-xs"></i> 공휴일 및 휴일은 택배사가 운행하지 않습니다. 따라서 휴일 전날 및 토요일에는 주문하신 상품이 출고되지 않으므로 착오 없으시기 바랍니다.<br>
                        <i class="fas fa-circle fa-xs"></i> 입금은 반드시 사업자등록증상의 업체명으로 해주세요<br>
                        <i class="fas fa-circle fa-xs"></i> 냉동식품의 경우는 보냉재를 넣어서 신선도가 유치될 수 있도록 꼼꼼히 포장하고 있습니다.<br>
                        <i class="fas fa-circle fa-xs"></i> 배송비는 상품별로 다릅니다. 주문시 참고해 주세요<br>
                                              &nbsp;&nbsp;&nbsp;(도서나 산간지방은 별도의 추가 배송비가 발생할 수 있습니다.)<br>
                      </div>
                      <br>
                      <br>
                      
                      <h3 data-aos="fade-right" data-aos-once="true" style="border-bottom:2px solid #222222;padding-bottom:3px;">반품/교환 안내</h3><br>
                      <div data-aos="fade-right" data-aos-once="true">
                        <i class="fas fa-circle fa-xs"></i> 단순 변심으로 인한 반품 및 교환은 어렵습니다.<br>
                        <i class="fas fa-circle fa-xs"></i> 반품배송비 : 편도 6,000원(최초 배송비가 무료인 경우 12,000원 부과)<br>
                        <i class="fas fa-circle fa-xs"></i> 교환배송비 : 12,000원<br>
                        <i class="fas fa-circle fa-xs"></i> 보내실곳 : (30044)대전광역시 대덕구 신탄진로115번안길 29<br>
                        <i class="fas fa-circle fa-xs"></i> 문의전화 : 042-635-9978 / 010-5427-1974
                      </div>
                  </div>


                </div>
              </div>
          </div>
        </div>

        <!--7/31 요구사항 - 연관 카테고리 상품->연관상품 문구변경-->
        <!--통합테스트_0814 - 4번 카테고리 정보 적용-->
        <product-list title="연관상품" :productList="productList" :categoryNm="categorySum" @changeProduct="changeProduct"></product-list>
        <div v-if="isShowMoreBtn" class="text-center mb-100">
          <button class="moreShowBtn" @click="moreBtn"><i class="fal fa-plus"></i>  더보기</button>
        </div>
        <div ref="scrollDetector" style="height: 1px;"></div>
    </section>

</template>
<script setup>
import { object } from "yup";
import StarIcon from '~/components/common/others/StarIcon.vue'
import * as categoryApi from '@/api';   
import * as productApi from '@/api';
import * as cartApi from '@/api';
import * as listApi from '@/api';
import ProductList from "@/components/products/ProductList.vue";
import noImg from "~/assets/img/no_img_sungchang.png";
import { useUserStore } from '@/store/useUser';
import { useCartStore } from '@/store/useCart';
import { Carousel, Slide } from "vue3-carousel";
import CookImage from '@/components/common/others/CookImage.vue'
import deliveryImg from "~/assets/img/deliveryPolicy.png";
import ProductThumbnail from '@/components/products/ProductThumbnail.vue'
import * as common_utils from "@/common/utils.ts";

const isMobile = ref(false);
const props = defineProps({
  item: {
    type : Object,
    default : {}
  },
  category1 : {
    type : String,
    default : ''
  },
  category2 : {
    type : String,
    default : ''
  }
}); 

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
const currentSlide = ref(0)
const state = useCartStore();
const router = useRouter()

//장바구니 갯수
const cartCnt = ref(1)

//페이지 정보
const pageNum = ref(1);
const pageSize = ref(12);

//리스트 정보
let relatedItem =ref([])
let productList = computed(()=>relatedItem.value)
const category1com = computed(()=>{
  return props.category1
})
const category2com = computed(()=>{
  return props.category2
})

//화면스크롤관련 
const scrollDetector = ref(null)
const isShowMoreBtn = ref(false)

//카테고리 정보
const category2List = ref([])//카테고리2 목록
const cate2 = ref('') //카테고리 조회
const category1 = ref('') //메인화면에서 받아온 카테고리1 값
const category1nm = ref('')
const category2 = ref('') //현화면 상단에서 선택한 카테고리2 값 
const category2nm = ref('')
const slider_1 = ref(null) //카테고리 슬라이더
const categorySum = computed(()=>{
  return `(${category1nm.value} / ${category2nm.value})`
})


const active_img = ref(noImg)//모달창 이미지
const json_img = ref([])//썸네일 이미지
const json_img_detail = ref([])//디테일 리스트
let json_img_url = ''//ProductThumbnail 전달값
let itemItcode = ''

const brands = computed(() => JSON.parse(localStorage.getItem("category1")) || []);
//sorting 관련 정보
const orderType10 = ref('')//가격 높은순(DESC) , 낮은순(ASC)
const orderType20 = ref('')//상품 최신순(ASC)
const orderType21 = ref('')//상품 인기순(DESC)
const orderType30 = ref('')//상품명 오름차순(ASC), 내림차순(DESC)

// const brands = [{title : "냉동식품"         , code : '01'},
//                 {title : "냉장식품"         , code : '02'},
//                 {title : "쌀/단무지/면/김치"  , code : '03'},
//                 {title : "장류/식용유/가루"   , code : '04'},
//                 {title : "캔/통조림/반찬"     , code : '05'},
//                 {title : "소스/드레싱/향신료"   , code : '06'},
//                 {title : "농산물/수산물"      , code : '07'},
//                 {title : "커피/차/음료"       , code : '08'},
//                 {title : "치즈/버터/유제품"   , code : '09'},
//                 {title : "용기/세제/잡화"     , code : '10'}]

/****************************************************************************************** mounted */
onMounted(async()=>{  
    
    document.addEventListener('scroll', handleScroll);
    
    setTimeout(async ()=>await doSearch() ,1000)
    setTimeout(async ()=>await doSearchCategory() ,1000)
}) 


/****************************************************************************************** method */
const changeListImage = (itCode,image) =>{ 
  active_img.value= image 
}
const favItemCntToStar=(faveItemCnt) =>{//관심상품 별 카운트 계산

    //8/23일 시트2 수정 요청사항(별 3개 이하일 경우 3개로 보이도록 수정)
    let star = Math.floor(faveItemCnt/10)
    if(star < 3){
      star = 3
    }
    return star
}
const checkMobileScreen=() =>{
  isMobile.value = window.innerWidth <= 767;   
}
let targetObj = '';
const handleActiveImg= (img, event)=>{
  
  const divElement = document.querySelector('.nav.nav-tabs');
  const buttons = divElement.querySelectorAll('button');

  buttons.forEach(button => {
    button.classList.remove('active');
  });
  
  active_img.value = img

  targetObj = (event.target).closest('button')
  targetObj.classList.add("active");
}
const handleNext = () => {
      const sliderRef = slider_1.value ;
      sliderRef.next();
};
const handlePrev = () => {
      const sliderRef = slider_1.value ;
      sliderRef.prev();
};
const navigateToCategoryList= (code) =>{//카테고리 영역_카테고리 클릭
      //category2.value = code
      //category2nm.value = category2List.value.find(item => item.CODE === code).NAME;
      router.replace(`/categoryList?code=${category1com.value}&title=${category1nm.value}`);
}
const doWishList= async(col) =>{//위시리스트 담기/제거
    if(col.CHK_FAVORITE === 'N'){//담기(N->Y)
      await doAddWishList(col)
      col.CHK_FAVORITE = 'Y'
    }else{//제거(Y->N)
      await doDelWishList(col)
      col.CHK_FAVORITE = 'N'
    }
    await doSearch()
}
const changeProduct = (data)=>{
  //relatedItem.value = data
  orderType10.value = data.orderType10
  orderType20.value = data.orderType20
  orderType21.value = data.orderType21
  orderType30.value = data.orderType30
  pageNum.value = 1
  relatedItem.value = []
  doSearch(); 

} 
const handleScroll=()=> {
      const scrollDetector1 = scrollDetector.value;  
      if (!scrollDetector1) return;
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const detectorOffsetTop = scrollDetector1.offsetTop;
      const windowHeight = window.innerHeight || document.documentElement.clientHeight;
      const detectorHeight = scrollDetector1.offsetHeight;

      // 스크롤이 화면 제일 밑까지 도달했는지 확인
      if (scrollTop + windowHeight >= detectorOffsetTop + detectorHeight -200 && isShowMoreBtn.value) {
        pageNum.value = pageNum.value + 1;
        doSearch();
      }
}
const moreBtn=()=>{
  pageNum.value = pageNum.value + 1;
  doSearch();
}
const addCart= (col, cnt) =>{//장바구니 담기
      //로그인여부 확인하기
      if(!isLogin.value){
        common_utils.fxAlert("로그인 후 이용해 주세요");
        //router.push(`/login`);//로그인화면으로 이동
        return
      }
      doAddCatrList(col, cnt)
}
const buyNow=(col, cnt) =>{//바로구매
    if(!isLogin.value){
      common_utils.fxAlert("로그인 후 이용해 주세요");
        router.push(`/login`);//로그인화면으로 이동
        return
    }else{ 
      state.setOrderProductNm(col.ITNAME); 
      doAddBuyCart(col, cnt)
        
    }
}

/****************************************************************************************** api호출 */  
const doSearchCategory = async ()=>{//카테고리2 영역
    let data;
    let param = {
        code : category1com.value,
        inputUser : '',
        name : ''
    }
    try {
      const dataObj = await productApi.product_category(param)
      data = dataObj.data
      if(data.RecordCount > 0){
        category2List.value = data.RecordSet.filter(item => item.NAME && item.NAME.trim() !== '');;
        category2List.value.map((item)=>{
          item.CODE = item.CODE.trim()
        })
        
         category1nm.value = brands.value.find(item => item.CODE === category1com.value).NAME;
         category2nm.value = category2List.value.find(item => item.CODE === category2com.value).NAME;
        setTimeout(()=>{currentSlide.value = category2List.value.findIndex(item => item.CODE === category2com.value)},50)//현재 카테고리 위치 셋팅
      }
    } catch (error) {
      console.error(error);
    }
}
const doAddWishList = async (col)=>{//관심상품 상품 추가
    
    let data;
      let param = {
              clcode : clcodeS ?? "",
              itcode : col.ITCODE,
              itname : col.ITNAME
      }
      try {
        const dataObj = await listApi.list_addFavList(param)
        data = dataObj.data
        if(data.ResultCode === '00'){
          common_utils.fxAlert("관심상품 목록에 추가되었습니다.");
        }
      } catch (error) {
        console.error(error);
      }
  }
  const doDelWishList = async (col)=>{//관심상품 상품 삭제
      
      let data;
      let param = {
              clcode : clcodeS ?? "",
              itcode : col.ITCODE  
      }
      try {
        const dataObj = await listApi.list_delFavList(param)
        data = dataObj.data
        if(data.ResultCode === '00'){
          common_utils.fxAlert("관심상품 목록에 제거되었습니다.");
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

const doAddBuyCart = async (col, cnt)=>{//바로구매 장바구니 추가
    
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
        const dataObj = await cartApi.cart_addBuyCart(param)
        data = dataObj.data
        if(data.RecordCount > 0){   
          const cartNo = data.RecordSet[0].CART_NO
          localStorage.setItem("BUY_CART_NO",cartNo)
         router.push(`/checkout?cartType=20&checkItCodes=${col.ITCODE}`);
        }
      } catch (error) {
        console.error(error);
      }
}

const doSearch = async ()=>{//리스트 정보
  //relatedItem.value = []
  
  let data;
  isShowMoreBtn.value = false;
  let param = {
          clcode : clcodeS ??''
        , itscode :category1com.value ?? ""
        , itscode2 : category2com.value ?? ""
        , keyword : ''
        , itcode : ''
        , pageNum : pageNum.value
        , pageSize : pageSize.value
        , inputUser : ''
        , orderType10 : orderType10.value ?? ''
        , orderType20 : orderType20.value ?? ''
        , orderType21 : orderType21.value ?? ''
        , orderType30 : orderType30.value ?? ''
      }  
      
    try {
        const responseObj = await categoryApi.list_categoryList(param)
        const response = responseObj.data
        if(response.RecordCount > 0){
            relatedItem.value.push( ...response.RecordSet)
            if(response.RecordCount === pageSize.value){
              isShowMoreBtn.value = true;
            }
        }else{
          isShowMoreBtn.value = false;
        } 
      } catch (error) {
        console.error(error);
      }finally{
        active_img.value = props.item?.ITEM_MAIN_IMAGE; 
        itemItcode = props.item?.ITCODE
        json_img_url = props.item?.JSON_IMAGE_URL
        if(props.item?.JSON_IMAGE_URL){
          json_img_detail.value = JSON.parse(props.item?.JSON_IMAGE_URL)
          json_img_detail.value.sort(function(a, b) {
            return a.seq - b.seq;
          });
          json_img.value = json_img_detail.value.slice(0,4)
        } 
      }
}

</script>

<style >

</style>


<!-- 
<script lang="ts">
import { defineComponent,PropType } from 'vue';
import ProductType from '@/types/productType';
import ProductDetailsContent from './ProductDetailsContent.vue';
import ProductDetailsReview from './ProductDetailsReview.vue';
import RelatedProducts from './RelatedProducts.vue';
import { useCartStore } from '~~/store/useCart';

export default defineComponent({
  components: { ProductDetailsContent, ProductDetailsReview, RelatedProducts },
  props:{
    item:{
      type:Object ,
      default:{},
      required:true
    },
  },
  data () {
    return {
      active_img:''//this.item.img
    }
  },
  computed:{
    item(){
      console.log(this.item)
      return this.item
    }
  },  
  mounted(){ 
    console.log(this.item)
  },  
  watch:{
    item(data){
      console.log(data)
    }
  },
  methods:{
    handleActiveImg(img:string){
      this.active_img = img
    },
    formattedPrice(price) {
      if (typeof price === 'number') {
        return price.toLocaleString();
      }
      return price;
    }
  },
  
  setup () {
    watchEffect(() => {
      console.log(1111)
    });
    const state = useCartStore()
    return {state}
  }
})
</script> -->

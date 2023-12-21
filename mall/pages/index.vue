<script setup>
import LayoutFour from "@/layout/LayoutFour.vue";
import { useUserStore } from "@/store/useUser";
import { useModalStore } from "@/store/useModal";
import * as mainApi from "@/api";
import * as cartApi from "@/api";
import ProductList from "@/components/products/ProductList.vue";
import CartModal from "@/components/common/modals/CartModal.vue";
import * as common_utils from "@/common/utils.ts";


import { Carousel, Slide } from "vue3-carousel";
import { useProductsStore } from "@/store/useProducts";
import CookImage from "@/components/common/others/CookImage.vue";
import CartDetailModal from "@/components/common/modals/CartDetailModal.vue";
import StarIcon from "~/components/common/others/StarIcon.vue";
  


//로그인 정보
const store = useUserStore();
const modalStore = useModalStore();

const clcodeS = computed(() => store.userClcode);
const userNoS = computed(() => store.userNo);
const isLogin = computed(() => {
  return store.isLogin;
});

const router = useRouter();
//장바구니 갯수
const cartCnt = ref(1);

//api 호출 결과 값
const doSearch1_table = ref([]); //기획/이벤트 영역
const doSearch2_table = ref([]); //관심상품 영역

const isShowFavorite = computed(
  () => doSearch2_table.value.length > 0 && isLogin
);
// 상품 상세보기
let modalData = ref({});

const slider_1 = ref(null);
/****************************************************************************************** mounted */
onMounted(() => {
  
  doSearch1(); //기획/이벤트 영역
  if (isLogin.value) {
    //로그인 정보가 있을 경우만 관심상품 목록 조회
    doSearch2();
  }
});

/****************************************************************************************** method */

const favList = () => {
  router.push(`/favList`);
};

const handleNext = () => {
  const sliderRef = slider_1.value;
  sliderRef.next();
};
const handlePrev = () => {
  const sliderRef = slider_1.value;
  sliderRef.prev();
};
const navigateToEventList = (geonum, title, eventType) => {
  //기획 이벤트 영역_이미지 || 타이틀 클릭 type 10-테마, 20-일반
  router.push(
    `/eventList?code=${geonum}&title=${title}&eventType=${eventType}`
  );
};
const navigateToCategoryList = (code, title) => {
  //카테고리 영역_카테고리 클릭
  router.replace(`/categoryList?code=${code}&title=${title}`);
};
const showCartPop = ref(false);
const showDetailModal = (item) => {
  modalData.value = JSON.parse(JSON.stringify(item));

  showCartPop.value = true;
  setTimeout(() => {
    common_utils.cartDimmed();
  }, 100);
};

const closeModal = () => {
  common_utils.removeDimmed();
  showCartPop.value = false;
};

const addCart = (col, cnt) => {
  //장바구니 담기
  //로그인여부 확인하기
  if (!isLogin.value) {
    common_utils.fxAlert("로그인 후 이용해 주세요");
    //router.push(`/login`);//로그인화면으로 이동
    return;
  }
  doAddCatrList(col, cnt);
  //useCartStore().add_cart_product(col.value)
};

const favItemCntToStar = (faveItemCnt) => {
  //관심상품 별 카운트 계산
  //8/23일 시트2 수정 요청사항(별 3개 이하일 경우 3개로 보이도록 수정)
  let star = Math.floor(faveItemCnt / 10);
  if (star < 3) {
    star = 3;
  }
  return star;
};

const movePage = (url) => {
  const router = useRouter();
  router.push(url);
};

/****************************************************************************************** api호출 */

const { $bus } = useNuxtApp();
const doAddCatrList = async (col, cnt) => {
  //장바구니 상품 추가

  let data;
  let param = {
    cartNo: "",
    seq: "",
    clcode: clcodeS.value ?? "",
    userNo: userNoS.value ?? "",
    itcode: col.ITCODE,
    qty: cnt ?? "1",
    amount: Number(col.AMOUNT.replace(/,/g, "")),
    inputUser: userNoS.value ?? "", //nameS ?? ""
  };
  try {
    const dataObj = await cartApi.cart_addCartList(param)
    data = dataObj.data
    if (data.RecordCount > 0) {
      common_utils.fxAlert("장바구니 추가 되었습니다.");
      $bus.$emit("chageCart");
      cartCnt.value = 1;
    }
  } catch (error) {
    console.error(error);
  }
};
const event_list = ref([]); //각 기획관리(20)의 상품 리스트
//onst doSearch10 = ref() //기획관리(10) 리스트
const doSearch20 = ref(); //기획관리(20) 리스트
const searchEventList = async () => {
  //(manager)상품관리 > 기획상품관리 목록 조회api

  let data;
  let param = {
    type: "",
    pageSize: 100,
    pageNum: 1,
    inputUser: "",
    eventNo: "",
    clcode: clcodeS.value ?? "",
    orderType10: "",
    orderType20: "",
    orderType21: "",
    orderType30: "",
  };
  try {
    data = await mainApi.list_eventList(param);
    if (data.RecordCount > 0) {
      let eventList = data.RecordSet;
      let tempList = [];

      //doSearch10.value = doSearch1_table.value.filter(item=> item.TYPE === '10')//일반만
      doSearch20.value = doSearch1_table.value.filter(
        (item) => item.TYPE === "20"
      ); //일반만
      doSearch20.value.map((item) => {
        tempList = eventList.filter(
          (eventItem) => item.GEONUM === eventItem.EVENT_NO
        );
        event_list.value.push({
          MANAGER: item,
          ITEMLIST: tempList,
        });
      });
      console.log(event_list.value);
    }
  } catch (error) {
    console.error(error);
  }
};

const doSearch1 = async () => {
  //(mabager)상품관리 > 기획관리 목록 조회api (type : 10-테마, 20-일반)

  let data;
  let param = {
    geonum: "",
    pageSize: "100",
    pageNum: "1",
    inputUser: "",
  };
  try {
    const dataObj = await mainApi.main_excuteEventMng(param);
    data = dataObj.data;
    if (data.RecordCount > 0) {
      doSearch1_table.value = data.RecordSet;
      searchEventList(); //기획별 상품 리스트
    }
  } catch (error) {
    console.error(error);
  }
};

const isComShowCartModal = computed(() => modalStore.isShowCartModal);

const doSearch2 = async () => {
  //관심상품 목록 api
  doSearch2_table.value = [];
  let data;
  let param = {
    clcode: clcodeS.value ?? "",
    keyword: "",
    pageSize: "12",
    pageNum: "1",
    inputUser: userNoS.value ?? "", //userId ?? ""
  };
  try {
    const dataObj = await mainApi.main_excuteFaveItem(param);
    data = dataObj.data;
    if (data.RecordCount > 0) {
      doSearch2_table.value = data.RecordSet;
    }
  } catch (error) {
    console.error(error);
  }
};
</script>

<template>
  <layout-four>
    <div class="brand__area pb-60 ">
      <!-- 통합테스트_0814.5번 > 수정사항 => 해당 영역 제거요청 -->
      <!--테마 영역-->
      <!-- <div class="banner__area pt-60">
      <div class="container custom-container">
        <div>
          <div class="d-flex align-items-center mb-3">
            <h3 class="m-0">테마영역#1</h3> 
          </div>
          <div class="row">
             <Carousel
            :items-to-show="3"
            :wrap-around="true"
             :autoplay="2000"
            :snapAlign="'center'"
            :breakpoints="{
              1200: {
                itemsToShow: 3,
              },
              992: {
                itemsToShow: 3,
              },
              700: {
                itemsToShow: 3,
              },
              0: {
                itemsToShow: 2,
              },
            }"
          >
             <Slide v-for="(col, index) in doSearch10" :key="index" class="brand__slider-item">
                  <div class="banner__item mb-30 p-relative" >
                    <div class="banner__thumb fix" style="border:  0 solid #ffffff">
                      <nuxt-link href="#" class="w-img" @click="navigateToEventList(col.GEONUM, col.TITLE, '10')"> 
                        <CookImage :image="col.IMG_URL" :size="'img-fluid mx-auto'" />
                      </nuxt-link>
                    </div>
                  </div>
              </Slide>
            </Carousel>
          </div>
        </div>
      </div>
    </div>  -->

    <div class="banner__area ">
        <div style="justify-content: center; display: flex; margin-top: 5px">
          <div class="scrollmenu mobile-menu-slider cat_icon_area col-xl-12">
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=01&title=냉동식품')"
            >
              <span><img src="~/assets/img/category/01.png" /></span
              ><span>냉동식품</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=02&title=냉장식품')"
            >
              <span><img src="~/assets/img/category/02.png" /></span
              ><span>냉장식품</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=03&title=가공식품')"
            >
              <span><img src="~/assets/img/category/03.png" /></span
              ><span>가공식품</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=04&title=장류소스')"
            >
              <span><img src="~/assets/img/category/04.png" /></span
              ><span>장류소스</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=11&title=일회용기')"
            >
              <span><img src="~/assets/img/category/05.png" /></span
              ><span>일회용기</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=10&title=생활용품')"
            >
              <span><img src="~/assets/img/category/06.png" /></span
              ><span>생활용품</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=05&title=김치/반찬')"
            >
              <span><img src="~/assets/img/category/07.png" /></span
              ><span>김치/반찬</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=07&title=고춧가루')"
            >
              <span><img src="~/assets/img/category/08.png" /></span
              ><span>고춧가루</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=06&title=커피/음료')"
            >
              <span><img src="~/assets/img/category/09.png" /></span
              ><span>커피/음료</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=09&title=냉동수산')"
            >
              <span><img src="~/assets/img/category/10.png" /></span
              ><span>냉동수산</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=08&title=냉동축산')"
            >
              <span><img src="~/assets/img/category/11.png" /></span
              ><span>냉동축산</span>
            </div>
            <div
              class="cat_icon_style_01"
              @click="movePage('/categoryList?code=12&title=D2-선주문')"
            >
              <span><img src="~/assets/img/category/12.png" /></span
              ><span>D2-선주문</span>
            </div>
          </div>
        </div>
      </div>
      <!-- 관심 상품 -->
      <div class="banner__area pt-100" v-if="isShowFavorite.value">
        <div class="container custom-container themehome">
          <div>
            <div class="d-flex align-items-center">
              <h3 class="m-0">관심 상품</h3>
              <a
                class="ml-auto btn btn-sm"
                style="
                  border-radius: 0 !important;
                  margin-left: auto !important;
                "
                @click="favList()"
                >더보기</a
              >
            </div>
            <product-list
              id=""
              :isShowHeader="false"
              :isShowSort="false"
              :multiComponent="true"
              title=" 관심 상품"
              :productList="doSearch2_table"
              @showDetailModal="showDetailModal"
              @doSearchfavList="doSearch2"
            ></product-list>
          </div>
        </div>
      </div>
      <div
        class="banner__area"
        :class="!isShowFavorite.value && index === 0 ? 'pt-100' : 'pt-60'"
        v-if="event_list.length > 0"
        v-for="(col, index) in event_list"
        :key="index"
        data-aos="fade-up" data-aos-once="true"
        
      >
        <div
          class="container custom-container themehome"
          style="margin-bottom: -100px"
        >
          <div>
            <div
              class="d-flex align-items-center"
              style="margin-top: 0px; margin-bottom: 0px"
            >
              <h3 class="m-0" v-html="col.MANAGER.CONTENTS" data-aos="fade-down" data-aos-once="true"></h3>
              <a
              data-aos="fade-up" data-aos-once="true"
                class="ml-auto btn btn-sm"
                style="
                  border-radius: 0 !important;
                  margin-left: auto !important;
                "
                @click="
                  navigateToEventList(
                    col.MANAGER.GEONUM,
                    col.MANAGER.TITLE,
                    '20'
                  )
                "
                >더보기</a
              >
            </div>
            
            <img :src="col?.MANAGER.IMG_URL" 
                @click="
                      navigateToEventList(
                        col.MANAGER.GEONUM,
                        col.MANAGER.TITLE,
                        '20'
                      )
                    "
            />
            <product-list
              :isShowHeader="false"
              :isShowSort="false"
              :title="col.MANAGER.TITLE"
              :multiComponent="true"
              :productList="col.ITEMLIST"
              @showDetailModal="showDetailModal"
              @doSearchfavList="doSearch2"
            >
            </product-list>
          </div>
        </div>
      </div>
    </div>
    <!----23.08.20 메인(index.vue)에서는 돋보기 모달을 띄우지 않는다. ProductList.vue파일 수정함-->
    <CartModal
      v-if="showCartPop"
      :modalData="modalData"
      @closeModal="closeModal"
    ></CartModal>
  </layout-four>
</template>

<!-- 페이지 스타일시트 -->
<style lang="scss">
.cat_icon_area {
  display: flex;
  align-items: center;
  justify-content: left;
  cursor: pointer;
  border: 0px solid #454545;
  box-shadow: 0px 15px 30px 0px #00038f1a;
}

.cat_icon_style_01 {
  background-color: #ffffff00;
  padding: 5px 15px 5px 15px;
  display: flex;
  flex-direction: column;
  transition: all 0.2s linear;
}
.cat_icon_style_01:hover {
  transform: scale(1.2);
}
.cat_icon_style_01 img {
  width: 3rem;
  border: 0px solid #555555;
}
.cat_icon_style_01 span {
  font-size: 0.9rem;
  font-weight: 600;
  color: #454545;
}

/* PC 사이즈 */
@media (min-width: 1280px) {
  /* 카테고리영역 */
  .cat_icon_area {
    margin-top: 55px;
    margin-bottom: -90px;
    padding-top: 10px;
    display: none;
  }
}
/* 태블릿사이즈 */
@media (max-width: 1280px) {
  /* 카테고리영역 */
  .cat_icon_area {
    margin-top: -5px;
    margin-bottom: 0px;
  }
}
@media (min-width: 720px) {
  /* 카테고리영역 */
  .cat_icon_area {
  }
}

/* 모바일사이즈 */
@media (max-width: 720px) {
  .cat_icon_area {
    margin-top: -20px;
    margin-bottom: -110px;
    padding-bottom: 15px;
    overflow-x: hidden;
    overflow-y: auto;
    height: auto;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0px 5px;
  }
  .cat_icon_style_01 {
    width: 60px;
    flex-wrap: nowrap;
    margin: 5px 0px;
    justify-content: center;
    text-align: center;
    padding: 5px 5px;
  }
  .cat_icon_style_01:hover {
    transform: scale(1);
  }
  .cat_icon_style_01 img {
    width: 3.5rem;
    border: 1px solid #b2b2b268;
    border-radius: 15px;
    box-shadow: 0px 1px 15px 0px #00038f1a;
  }
}
</style>



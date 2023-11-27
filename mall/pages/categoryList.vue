<script setup lang="ts">
import Layout from "@/layout/LayoutFour.vue";
import { Carousel, Slide } from "vue3-carousel";
import * as productApi from "@/api";
import * as listApi from "@/api";
import * as cartApi from "@/api";
import { useUserStore } from "@/store/useUser";
import { useCartStore } from "@/store/useCart";
import { useWishlistStore } from "@/store/useWishlist";
import ProductList from "@/components/products/ProductList.vue";
 
const route = useRoute();

//페이징 정보
const pageNum = ref(1);
const pageSize = ref(12);

//리스트 정보
const category2List = ref([]); //카테고리2 목록
const list = ref([]); //메인 리스트

//화면스크롤관련
const scrollDetector = ref(null);
const isShowMoreBtn = ref(false);

//카테고리 정보
const category1 = ref(""); //메인화면에서 받아온 카테고리1 값
const category1nm = ref("");
const category2 = ref(""); //현화면 상단에서 선택한 카테고리2 값
const category2nm = ref("");
const slider_1 = ref(null); //카테고리 슬라이더

//sorting 관련 정보
const orderType10 = ref(""); //가격 높은순(DESC) , 낮은순(ASC)
const orderType20 = ref(""); //상품 최신순(ASC)
const orderType21 = ref(""); //상품 인기순(DESC)
const orderType30 = ref(""); //상품명 오름차순(ASC), 내림차순(DESC)

const isMobile = ref(false);
const { $bus } = useNuxtApp();

/****************************************************************************************** mounted */
onMounted(async () => {
  console.log(11111);
  setParam();
  await doSearchCategory();
  if (category2List.value.length > 0) {
    category2.value = category2List.value[0].CODE;
  }
  //category2.value = "01"; //화면 첫 진입 시, 카테고리2는 무조건 '01'로 셋팅

  doSearch();

  document.addEventListener("scroll", handleScroll);

  checkMobileScreen();
  window.addEventListener("resize", checkMobileScreen);

  $bus.$emit("changeCategory", category1.value); //9월 7일 요청사항 : 2차 카테고리 선택 시, 헤더의 1차 카테고리 해당항목 표시
});
onBeforeRouteLeave(() => {
  window.removeEventListener("resize", checkMobileScreen);
});
/****************************************************************************************** method */
const checkMobileScreen = () => {
  isMobile.value = window.innerWidth <= 767;
};
const changeProduct = (data) => {
  //list.value = data
  orderType10.value = data.orderType10;
  orderType20.value = data.orderType20;
  orderType21.value = data.orderType21;
  orderType30.value = data.orderType30;
  pageNum.value = 1;
  list.value = [];
  doSearch();
};
const setParam = () => {
  category1.value = route.query.code;
  category1nm.value = route.query.title;
};

watch(
  () => route.query,
  (newQuery, oldQuery) => {
    console.log("watch:::", newQuery);
    category1.value = newQuery.code;
    category1nm.value = newQuery.title;
    doSearchCategory();
    doSearch();

    document.addEventListener("scroll", handleScroll);

    checkMobileScreen();
    window.addEventListener("resize", checkMobileScreen);
  }
);

const handleScroll = () => {
  const scrollDetector1 = scrollDetector.value;
  if (!scrollDetector1) return;
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const detectorOffsetTop = scrollDetector1.offsetTop;
  const windowHeight =
    window.innerHeight || document.documentElement.clientHeight;
  const detectorHeight = scrollDetector1.offsetHeight;

  // 스크롤이 화면 제일 밑까지 도달했는지 확인
  if (
    scrollTop + windowHeight >= detectorOffsetTop + detectorHeight &&
    isShowMoreBtn.value
  ) {
    pageNum.value = pageNum.value + 1;
    doSearch();
  }
};

const moreBtn = () => {
  pageNum.value = pageNum.value + 1;
  doSearch();
};
const handleNext = () => {
  const sliderRef = slider_1.value;
  sliderRef.next();
};
const handlePrev = () => {
  const sliderRef = slider_1.value;
  sliderRef.prev();
};
const navigateToCategoryList = (code) => {
  //카테고리 영역_카테고리 클릭
  category2.value = code;
  category2nm.value = category2List.value.find(
    (item) => item.CODE === code
  ).NAME;
  pageNum.value = 1;
  list.value = [];
  doSearch();
};
/****************************************************************************************** api호출 */

const doSearchCategory = async () => {
  //카테고리2 영역
  let data;
  let param = {
    code: category1.value,
    inputUser: "",
    name: "",
  };
  try {
    data = await productApi.product_category(param);
    if (data.RecordCount > 0) {
      category2List.value = data.RecordSet.map((item) => {
        item.NAME = item.NAME?.trim();
        return item;
      });
      category2nm.value = category2List.value?.[0].NAME;
    }
  } catch (error) {
    console.error(error);
  }
};
const doSearch = async () => {
  //리스트 정보
  let data;
  isShowMoreBtn.value = false;
  let param = {
    clcode: "",
    itscode: category1.value,
    itscode2: category2.value,
    keyword: "",
    itcode: "",
    pageSize: pageSize.value,
    pageNum: pageNum.value,
    inputUser: "",
    orderType10: orderType10.value ?? "",
    orderType20: orderType20.value ?? "",
    orderType21: orderType21.value ?? "",
    orderType30: orderType30.value ?? "",
  };
  try {
    data = await listApi.list_categoryList(param);
    if (data.RecordCount > 0) {
      list.value.push(...data.RecordSet);
      if (data.RecordCount === pageSize.value) {
        isShowMoreBtn.value = true;
      }
    } else {
      isShowMoreBtn.value = false;
    }
  } catch (error) {
    console.error(error);
  }
};
</script>


<template>
  <layout>
    <!--navicate 영역-->
    <div data-aos="flip-down" class="breadcr mt-90">{{ category1nm }} > {{ category2nm }}</div>
    <!-- 카테고리2 영역-->
    <div class="brand__area non-mobile">
      <div class="container custom-container-2">
        <div class="brand__slider-active p-relative mt-40">
          <Carousel
            ref="slider_1"
            :items-to-show="category2List.length < 9 ? category2List.length : 9"
            :wrap-around="
              category2List.length < 9 ? (isMobile ? true : false) : true
            "
            :snapAlign="category2List.length < 9 ? 'start' : 'center'"
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
            <Slide
              v-for="(col, i) in category2List"
              :key="i"
              class="brand__slider-item"
            >
              <div class="product__load-btn text-center" data-aos="flip-left" :data-aos-delay="i*100">
                <button
                  class="os-btn"
                  type="submit"
                  :style="`border:none; width: 200px; font-size: 20px; font-weight: 500; padding: 0; ${
                    category2 === col.CODE ? 'border: 2px solid #222222' : ''
                  }`"
                  @click="navigateToCategoryList(col.CODE, col.NAME)"
                >
                  {{ col.NAME }}
                </button>
              </div>
            </Slide>
          </Carousel>
          <div
            class="owl-nav"
            v-if="category2List.length < 5 ? (isMobile ? true : false) : true"
            style="visibility: visible; opacity: 1"
          >
            <div @click="handlePrev" class="owl-prev" data-aos="fade-down" >
              <button><i class="fal fa-angle-left"></i></button>
            </div>
            <div @click="handleNext" class="owl-next" data-aos="fade-down">
              <button><i class="fal fa-angle-right"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- 모바일 카테고리 영역-->
    <div class="scrollmenu mobile_brand__slider">
      <a v-for="(col, i) in category2List" :key="i">
        <button
          class="os-btn"
          type="submit"
          :style="`${
            category2 === col.CODE ? 'border: 1px solid #222222' : ''
          }`"
          @click="navigateToCategoryList(col.CODE, col.NAME)"
        >
          {{ col.NAME }}
        </button>
      </a>
    </div>

    <product-list
      :title="`'${category2nm}' `"
      :productList="list"
      @changeProduct="changeProduct"
    ></product-list>
    <div v-if="isShowMoreBtn" class="text-center mb-100">
      <button class="moreShowBtn" @click="moreBtn">
        <i class="fal fa-plus"></i> 더보기
      </button>
    </div>
    <div ref="scrollDetector" style="height: 1px"></div>
  </layout>
</template>




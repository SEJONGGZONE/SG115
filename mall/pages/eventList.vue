<script setup lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/LayoutFour.vue";
import * as listApi from '@/api';
import { Carousel, Slide } from "vue3-carousel";
import ProductList from "@/components/products/ProductList.vue";
import { useUserStore } from '@/store/useUser';

const route = useRoute();  
const router = useRouter()

//로그인 정보
const store = useUserStore();
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const isLogin = computed(()=>{
    return store.isLogin
})


//페이지 정보
const pageNum = ref(1);
const pageSize = ref(12);

//리스트 정보
const list = ref([])//관심상품 목록

//화면스크롤관련 
const scrollDetector = ref(null)
const isShowMoreBtn = ref(false)

//sorting 관련 정보
const orderType10 = ref('')//가격 높은순(DESC) , 낮은순(ASC)
const orderType20 = ref('')//상품 최신순(ASC)
const orderType21 = ref('')//상품 인기순(DESC)
const orderType30 = ref('')//상품명 오름차순(ASC), 내림차순(DESC)

//setParam
const type = ref('')
const title = ref('')
const eventType = ref('')//테마-10, 일반-20

const isShowHeader = ref(false)//테마(10)-false, 일반(20)-true (테마(10)상품의 경우 제목>테마상품내용>상품목록 순으로 보여지며, ProductList.vue자체의 타이틀의 비활성화하고 현 화면에서 타이틀을 보여줌)

const slider_1 = ref(null) 
const brands = computed(() => {
  if (typeof window !== 'undefined') { // 클라이언트에서 실행 중인지 확인
    return JSON.parse(localStorage.getItem("category1")) || [];
  }
  return [];
});


/****************************************************************************************** mounted */
onMounted(()=>{
  setParam();
  doSearch();

  document.addEventListener('scroll', handleScroll);
}) 

/****************************************************************************************** method */
const handleNext = () => {
  const sliderRef = slider_1.value ;
  sliderRef.next();
};
const handlePrev = () => {
  const sliderRef = slider_1.value ;
  sliderRef.prev();
}; 
const navigateToCategoryList= (code, title) =>{//카테고리 영역_카테고리 클릭  
  router.replace(`/categoryList?code=${code}&title=${title}`);
}  
const changeProduct = (data)=>{
  //list.value = data
  orderType10.value = data.orderType10
  orderType20.value = data.orderType20
  orderType21.value = data.orderType21
  orderType30.value = data.orderType30
  pageNum.value = 1
  list.value = [];
  doSearch(); 

}
const setParam=()=>{
      type.value = route.query.code
      title.value = route.query.title
      eventType.value = route.query.eventType
      
      isShowHeader.value = eventType.value === '10' ? false : true;
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
/****************************************************************************************** api호출 */

const doSearch = async ()=>{//리스트 정보
  
    let data;
    isShowMoreBtn.value = false;
    let param = {
            type : eventType.value,
            pageSize : pageSize.value,
            pageNum : pageNum.value,
            inputUser : '',
            eventNo : type.value ?? '',
            clcode : clcodeS ?? ''   , 
            orderType10 : orderType10.value ?? '',
            orderType20 : orderType20.value ?? '',
            orderType21 : orderType21.value ?? '',
            orderType30 : orderType30.value ?? ''
    }
    
    try {
      data = await listApi.list_eventList(param)
      if(data.RecordCount > 0){
        //list.value = data.RecordSet;
        list.value.push(...data.RecordSet);
        if(data.RecordCount === pageSize.value){
					isShowMoreBtn.value = true;
				}
      }else{
        isShowMoreBtn.value = false;
      }
    } catch (error) {
      console.error(error);
    }
}

</script>


<template>
  <layout>
      <!--카테고리 영역-->
      <!-- <div class="brand__area pb-40 pt-40">
        <div class="container custom-container-2">
          <div class="brand__slider-active p-relative ">
            <Carousel
              ref="slider_1"
              :items-to-show="3"
              :wrap-around="true"
              :snapAlign="'center'"
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
                  itemsToShow: 3,
                },
                400: {
                  itemsToShow: 2,
                },
                0: {
                  itemsToShow: 2,
                },
              }"
            >
              <Slide v-for="(col, i) in brands" :key="i" class="brand__slider-item pr-10">
                <div class="product__load-btn text-center">
                  <button class="os-btn" type="submit" style="width: 150px; font-size: 14px; padding: 0;" @click="navigateToCategoryList(col.CODE, col.NAME)">{{ col.NAME }}</button>
                </div>
              </Slide>
            </Carousel>
            <div class="owl-nav" style="visibility: visible; opacity: 1">
              <div @click="handlePrev" class="owl-prev">
                <button><i class="fal fa-angle-left"></i></button>
              </div>
              <div @click="handleNext" class="owl-next">
                <button><i class="fal fa-angle-right"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div> -->
      <section class="product__area pt-50" v-if="!isShowHeader">
        <div class="custom-container">
          <div class="row" >
            <div class="col-xl-12">
              <div class="section__title-wrapper text-center p-relative">
                <div class="section__title">
                  <h2 class="grey-bg" style="font-size:30px;">{{ title }}</h2>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-50 mt-50 text-center">
            <div v-html="list[0]?.CONTENTS"></div>
          </div>
        </div>
      </section>

      <product-list :title="`'${title}'`" :productList="list" @changeProduct="changeProduct" :isShowHeader="isShowHeader" :isShowSort="true"></product-list>
      
      <div v-if="isShowMoreBtn" class="text-center mb-100">
        <button class="moreShowBtn" @click="moreBtn"><i class="fal fa-plus"></i>  더보기</button>
      </div>
      <div ref="scrollDetector" style="height: 1px;"></div>
  </layout>
</template>



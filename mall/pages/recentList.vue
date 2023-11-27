<script setup>

import { useUserStore } from '@/store/useUser';
import ProductList from "@/components/products/ProductList.vue"; 
import Layout from "@/layout/LayoutFour.vue"; 
import * as listApi from '@/api'; 

const productList = ref([])

//로그인 정보
const store = useUserStore(); 
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO; 

//페이지 정보
const pageNum = ref(1);
const pageSize = ref(1000); //통합테스트_0814  3번 관심품목 페이징 기능 제거

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



/****************************************************************************************** mounted */

onMounted(()=>{
  let tempProductList = localStorage.getItem('seeProductList')
  if(tempProductList){
    productList.value = JSON.parse(tempProductList)
  }
})

/****************************************************************************************** method */
const handleScroll=()=> {
      const scrollDetector1 = scrollDetector.value; 
      if (!scrollDetector1) return;
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const detectorOffsetTop = scrollDetector1.offsetTop;
      const windowHeight = window.innerHeight || document.documentElement.clientHeight;
      const detectorHeight = scrollDetector1.offsetHeight;

      // 스크롤이 화면 제일 밑까지 도달했는지 확인
      if (scrollTop + windowHeight >= detectorOffsetTop + detectorHeight && isShowMoreBtn.value) {
        pageNum.value = pageNum.value + 1;
        doSearch();
      }
} 

/****************************************************************************************** api호출 */ 

</script>


<template>
  <layout>  
    <product-list title="최근 본 상품" :productList="productList" @changeProduct="updateList" @doSearchfavList="doSearch" :isShowSort="false"></product-list>  
    <!-- 관심상품은 페이지 기능 사용안함
    <div v-if="isShowMoreBtn" class="text-center mb-100">
      <button class="moreShowBtn" @click="moreBtn"><i class="fal fa-plus"></i>  더보기</button>
    </div>
    -->
    <div ref="scrollDetector" style="height: 1px;"></div>
  </layout>
</template>
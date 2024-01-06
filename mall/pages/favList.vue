<script setup>

import { useUserStore } from '@/store/useUser';
import ProductList from "@/components/products/ProductList.vue"; 
import Layout from "@/layout/LayoutFour.vue"; 
import * as listApi from '@/api'; 

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

  doSearch();

  //document.addEventListener('scroll', handleScroll);
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
      if (scrollTop + windowHeight >= detectorOffsetTop + detectorHeight -200 && isShowMoreBtn.value) {
        pageNum.value = pageNum.value + 1;
        doSearch();
      }
} 
/* 관심상품은 페이징기능 사용안함
const moreBtn=()=>{
  pageNum.value = pageNum.value + 1;
  doSearch();
}*/
const updateList = (data) => {//정렬 후 업데이트된 리스트
      //list.value = updatedItem; 
      orderType10.value = data.orderType10
      orderType20.value = data.orderType20
      orderType21.value = data.orderType21
      orderType30.value = data.orderType30
      pageNum.value = 1
      list.value = [];
      doSearch(); 
}
/****************************************************************************************** api호출 */ 
const doSearch = async ()=>{//리스트 정보
    
    let data;
    isShowMoreBtn.value = false;
    let param = {
            clcode : clcodeS ?? "",
            keyword : '',
            pageSize : pageSize.value,
            pageNum : pageNum.value,
            inputUser : userNoS ?? "",//nameS ?? ""
            orderType10 : orderType10.value ?? '',
            orderType20 : orderType20.value ?? '',
            orderType21 : orderType21.value ?? '',
            orderType30 : orderType30.value ?? ''
    }
    try {
      data = await listApi.list_favList(param)
      if(data.RecordCount > 0){
        //list.value.push(...data.RecordSet);
        list.value = data.RecordSet;
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
    <product-list title="관심상품" :productList="list" @changeProduct="updateList" @doSearchfavList="doSearch"></product-list>  
    <!-- 관심상품은 페이지 기능 사용안함
    <div v-if="isShowMoreBtn" class="text-center mb-100">
      <button class="moreShowBtn" @click="moreBtn"><i class="fal fa-plus"></i>  더보기</button>
    </div>
    -->
    <div ref="scrollDetector" style="height: 1px;"></div>
  </layout>
</template>
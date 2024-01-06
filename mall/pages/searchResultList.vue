<script setup lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/LayoutFour.vue";
import * as listApi from '@/api';
import ProductList from "@/components/products/ProductList.vue";

const route = useRoute();  

//페이지 정보
const pageNum = ref(1);
const pageSize = ref(12);

//리스트 정보
const list = ref([])//관심상품 목록

//화면스크롤관련 
const scrollDetector = ref(null)
const isShowMoreBtn = ref(false)

//setParam
const keyWordR = ref('')

//sorting 관련 정보
const orderType10 = ref('')//가격 높은순(DESC) , 낮은순(ASC)
const orderType20 = ref('')//상품 최신순(ASC)
const orderType21 = ref('')//상품 인기순(DESC)
const orderType30 = ref('')//상품명 오름차순(ASC), 내림차순(DESC)


/****************************************************************************************** mounted */
onMounted(()=>{
  setParam();
  doSearch();

  document.addEventListener('scroll', handleScroll);
}) 

/****************************************************************************************** method */
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
    keyWordR.value = route.query.keyWord
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
            clcode : '',
            itscode : '',
            itscode2 : '',
            keyword : keyWordR.value,
            itcode : '',
            pageSize : pageSize.value,
            pageNum : pageNum.value,
            inputUser : '', 
            orderType10 : orderType10.value ?? '',
            orderType20 : orderType20.value ?? '',
            orderType21 : orderType21.value ?? '',
            orderType30 : orderType30.value ?? ''

    }
    try {
      const dataObj = await listApi.list_categoryList(param)
      data = dataObj.data;
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
    <product-list :title="`'${keyWordR}' 검색 결과`" :productList="list" @changeProduct="changeProduct"></product-list>
    <div v-if="isShowMoreBtn" class="text-center mb-100">
      <button class="moreShowBtn" @click="moreBtn"><i class="fal fa-plus"></i>  더보기</button>
    </div>
    <div ref="scrollDetector" style="height: 1px;"></div>
  </layout>
</template>

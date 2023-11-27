<template>
  <layout>

      <!--카테고리 영역-->
      
      <div class="brand__area pb-40 pt-40" style="display: none;">
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
                  <button class="os-btn" type="submit" style="width: 150px; font-size: 20px; padding: 0;" @click="navigateToCategoryList(col.CODE, col.NAME)">{{ col.NAME }}</button>
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
      </div>  
    
      <div class="mb-100 mt-20 text-center company-info">
        <img class="ci-img" :src="companyImg1"/>
        <img class="ci-img" :src="companyImg2"/>
      </div>
  </layout>
</template>

<script setup lang="ts">

import Layout from "@/layout/LayoutFour.vue";
import { Carousel, Slide } from "vue3-carousel";
import companyImg1 from "~/assets/img/company_001.png";
import companyImg2 from "~/assets/img/company_002.png";

const router = useRouter()

const slider_1 = ref(null) 
const brands = computed(() => {
  if (typeof window !== 'undefined') { // 클라이언트에서 실행 중인지 확인
    return JSON.parse(localStorage.getItem("category1")) || [];
  }
  return [];
});



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
</script>

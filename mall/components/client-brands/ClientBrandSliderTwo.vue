<template>
  <div class="brand__area pb-40 pt-40">
    <div class="container custom-container-2">
      <div class="brand__slider-active p-relative ">
        <Carousel
           ref="slider_1"
          :items-to-show="5"
          :wrap-around="true"
          :snapAlign="'center'"
          :breakpoints="{
            1200: {
              itemsToShow: 5,
            },
            992: {
              itemsToShow: 3,
            },
            700: {
              itemsToShow: 2,
            },
            0: {
              itemsToShow: 1,
            },
          }"
        >
          <Slide
            v-for="(brand, i) in brands"
            :key="i"
            class="brand__slider-item"
          >
            <div class="product__load-btn text-center">
              <!--<img :src="brand" alt="client" />-->
              <button class="os-btn" type="submit" style="width: 200px; font-size: 14px;">{{ brand }}</button>
              
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
</template>

<script lang="ts">
// external
import { Carousel, Slide } from "vue3-carousel";
import { defineComponent, reactive, toRefs } from "vue";

// interface
interface sliderRef {
  next(): void;
  prev(): void;
}

export default defineComponent({
  components: { Carousel, Slide },
  methods:{
    handleNext() {
      const sliderRef = this.$refs.slider_1 as sliderRef;
      sliderRef.next();
    },
    handlePrev() {
      const sliderRef = this.$refs.slider_1 as sliderRef;
      sliderRef.prev();
    },
  },
  setup() {
    const state = reactive({
      brands: ["냉동식품", "냉장식품", "쌀/단무지/면/김치", "장류/식용유/가루", "캔/통조림/반찬", "소스/드레싱/향신료", "농산물/수산물", "커피/차/음료", "치즈/버터/유제품", "용기/세제/잡화"],
    });

    return {
      ...toRefs(state),
    };
  },
});
</script>

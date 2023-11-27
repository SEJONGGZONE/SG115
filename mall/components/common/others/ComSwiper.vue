<script setup>
const slides = ref(
  Array.from({ length: 5 }, () => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    // Figure out contrast color for font
    const contrast =
      r * 0.299 + g * 0.587 + b * 0.114 > 186 ? "black" : "white";

    return { bg: `rgb(${r}, ${g}, ${b})`, color: contrast };
  })
);

const handleSlideClick = (idx, slide) => {
  console.log("event", slide);
  alert(`인덱스 확인: [${idx}], 컬러: [${slide.color}]`);
};
const endEvent = (e) => {
  console.log(e);
  if (e.realIndex === slides.value.length - 1) {
    slides.value.push(
      ...Array.from({ length: 10 }, () => {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        // Figure out contrast color for font
        const contrast =
          r * 0.299 + g * 0.587 + b * 0.114 > 186 ? "black" : "white";

        return { bg: `rgb(${r}, ${g}, ${b})`, color: contrast };
      })
    );
  }
};
</script>

<!-- 
[modules]: Swiper에 사용할 추가 모듈들을 배열 형태로 지정
- SwiperAutoplay: 자동 재생
- SwiperEffectCreative: 크리에이티브 효과
- SwiperPagination: 페이지 표시(점) 기능
- SwiperNavigation: 이전/다음 버튼 기능
- SwiperScrollbar: 스크롤바 기능

slidesPerView: 한 번에 보여줄 슬라이드의 수
loop: 슬라이드 루프 기능

[autoplay]: 슬라이드 자동 재생 옵션
- delay: 자동 재생 간격
- disableOnInteraction 슬라이드 드래그시 자동재생 멈춤

creativeEffect: 크리에이티브 효과의 상세 설정
pagination: 페이지 표시(점) 설정
scrollbar: 스크롤바를 활성화
centeredSlides: 활성 슬라이드가 항상 중앙에 위치하도록
spaceBetween: 슬라이드 간의 간격 지정

-->

<template>
  <div>
    <!-- <Swiper
      :height="300"
      :modules="[SwiperAutoplay, SwiperEffectCreative]"
      :slides-per-view="1"
      :loop="true"
      :effect="'creative'"
      :autoplay="{
        delay: 3000,
        disableOnInteraction: true,
      }"
      :creative-effect="{
        prev: {
          shadow: false,
          translate: ['-20%', 0, -1],
        },
        next: {
          translate: ['100%', 0, 0],
        },
      }"
    >
      <SwiperSlide
        v-for="(slide, idx) in slides"
        :key="idx"
        :style="`background-color: ${slide.bg}; color: ${slide.color}`"
      >
        {{ idx }}
      </SwiperSlide>
    </Swiper> -->

    <Swiper
      :height="300"
      :modules="[
        SwiperAutoplay,
        SwiperPagination,
        SwiperNavigation,
        SwiperScrollbar,
      ]"
      :slides-per-view="3"
      :loop="true"
      :effect="'slide'"
      :autoplay="{
        delay: 3000,
        disableOnInteraction: true,
      }"
      :pagination="{ clickable: true }"
      :navigation="true"
      :scrollbar="true"
      :centeredSlides="true"
      :spaceBetween="40"
      @slide-change-transition-end="endEvent"
    >
      <SwiperSlide
        v-for="(slide, idx) in slides"
        :key="idx"
        :style="`background-color: ${slide.bg}; color: ${slide.color}`"
        @click="() => handleSlideClick(idx, slide)"
      >
        {{ idx }}
      </SwiperSlide>
    </Swiper>
    <!-- <h2>Swiper Card Effect</h2>
    <Swiper
      class="swiper-cards"
      :width="240"
      :modules="[SwiperAutoplay, SwiperEffectCards]"
      :slides-per-view="1"
      :loop="true"
      :effect="'cards'"
      :autoplay="{
        delay: 8000,
        disableOnInteraction: true,
      }"
    >
      <SwiperSlide
        v-for="(slide, idx) in slides"
        :key="idx"
        :style="`background-color: ${slide.bg}; color: ${slide.color}`"
      >
        {{ idx }}
      </SwiperSlide>
    </Swiper> -->
  </div>
</template>

<style>
.swiper-slide {
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 18px;
  height: 20vh;
  font-size: 4rem;
  font-weight: bold;
  font-family: "Roboto", sans-serif;

  opacity: 0.5; /* 기본적으로 모든 슬라이드를 흐리게 */
  transition: opacity 0.3s; /* 부드러운 효과를 위한 전환 */
  margin: 0px; /* 여백 */
  margin-top: 30px;
  margin-bottom: 30px;
}
.swiper-slide-active,
.swiper-slide-next,
.swiper-slide-prev {
  opacity: 1; /* 중앙 및 바로 옆 슬라이드는 불투명하게 */
}
.swiper-wrapper {
  min-width: 100vh;
  width: 100vh;
}
.swiper-cards {
  width: 240px;
  height: 240px;
}
.swiper-cards .swiper-slide {
  border-radius: 6px;
  border: 1px solid black;
}
</style>

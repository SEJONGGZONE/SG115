<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";

//////////////api 통신
onMounted(() => {
  setTimeout(() => {
    
    
  }, 500);
});





//////////////함수


</script>

<template>
  <div class="section section__management" style="gap: 2px">
    <div class="group__title">
      <h2>사용자 관리</h2>
    </div>
    <div></div>
    <div class="group__contents_sungchang">
      <!-- 메인데이타 -->
      <div class="part__data_list left_side" style="flex: unset; height: auto">
      메인
      </div>
      <div id="dragMe" class="resizer_h" @mousedown="mouseDownHandlerForDrag($event)"></div>
      <!-- 상세 -->
      <div class="part__data_detail right_side" style="height: auto">
        상세
      </div>
    </div>
  </div>
</template>

<!-- CSS 전역 -->
<style>
tr {
  cursor: pointer;
}
</style>

<!-- CSS 영역한정(좌우조절용) -->
<style scoped>
.left_side {
  width: 75%;
  /* 중앙 정렬 */
  display: flex;
  /*justify-content: center;*/
  min-width: 25%;
}
.right_side {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 25%;
}
</style>

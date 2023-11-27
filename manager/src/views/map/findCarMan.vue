 <script setup>
import { ref } from 'vue'
import { mapInstance } from '@/naverMap/stores'
import { NaverMap } from '@/naverMap'
import { onMounted } from 'vue'

import { operateApi } from '@/api'
onMounted(() => {
  setTimeout(() => {
    mapInstance.value.setOptions('mapTypeControl', true)
  }, 500)
  setInterval(() => {
    console.log(1111)
    operateApi.CvoGpsServiceSave({})
  }, 20000)
})
const resultCount = ref(0)
const searchBtn = () => {
  operateApi.CvoGpsServiceCall().then((item) => {
    resultCount.value = item.data.RecordCount
  })
}
</script>


<template>
  <div class="section section__management" style="gap: 2px">
    <div class="group__title">
      <h2>위치조회</h2>
      <h2>{{ resultCount }}</h2>
    </div>
    <div></div>
    <div class="group__contents">
      <!-- 네이버지도 -->
      <div style="flex: 2; border-style: solid; border-width: 1px; border-color: #a8a7b0; position: relative">
        <NaverMap style="height: 100%; width: 100%" />
        <!-- 새로고침 버튼 추가 -->
        <div style="position: absolute; left: 10px; top: 10px">
          <img class="cvo_map_refresh" @click.prevent="searchBtn" src="/assets/img/map/refresh_blue_01.png" />
        </div>
      </div>
    </div>
    <div></div>
  </div>
</template>

<style lang="scss">
/* CSS 변수설정 */
:root {
  --main-bg-color: #382efe7f;
  --marker-time-display: visible;
  --marker-speed-display: visible;
  --time-marker-bg-30: #a3a3a37f;
  --time-marker-bg-60: #382efe7f;
  --time-marker-bg-90: #fe2e517f;
}

/* 버튼, 속도 ON/OFF */
.recent_route_speed_onoff {
  position: absolute;
  left: 235px;
  bottom: 100px;
  width: 210px;
  height: auto;
  color: #ffffff;
  font-size: 25px;
  font-weight: 600;
  padding: 10px 20px;
  border: 4px solid white;
  background-color: #382efe7f;
  border-radius: 40px;
  box-shadow: 2px 2px 10px #000000c9;
  cursor: pointer;
}
</style>

 

<template>
  <div>
    <input type="button" value="위치 좌표 확인" @click="getCurrentPosition()" />
    <div>{{ isPositionReady ? 'yes' : 'no' }}</div>
    <div>{{ positionObj }}</div>
  </div>
</template>

<script setup>
import VueGeolocation from 'vue-geolocation-api'
import { ref } from 'vue'
const positionObj = ref({})
const isPositionReady = ref(false)
const getCurrentPosition = () => {
  if (!navigator.geolocation) {
  } else {
    navigator.geolocation.getCurrentPosition(getPositionValue, geolocationError)
  }
}
const getPositionValue = (val) => {
  positionObj.value.latitude = val.coords.latitude
  positionObj.value.longitude = val.coords.longitude
  isPositionReady.value = true
  console.log('주소 확인 완료', val)
}
const geolocationError = () => {
  console.log('위치 정보를 찾을 수 없습니다.2')
}
const setAlert = (text) => {
  alert(text)
}
</script> 
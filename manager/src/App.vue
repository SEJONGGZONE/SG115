<script setup lang="ts">
import { getCurrentInstance, onMounted, watch } from 'vue'
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAppOptionStore } from '@/stores/app-option'
import { useAppVariableStore } from '@/stores/app-variable'
import { ProgressFinisher, useProgress } from '@marcoschulte/vue3-progress'
import AppSidebar from '@/components/app/Sidebar.vue'
import AppHeader from '@/components/app/Header.vue'
import AppFooter from '@/components/app/Footer.vue'
import { Toast } from 'bootstrap'
import routers from './router'

const appOption = useAppOptionStore()
const appVariable = useAppVariableStore()
const internalInstance = getCurrentInstance()
const router = useRouter()
watch(
  () => appVariable.toastMsg,
  (newVal, oldVal) => {
    toastpop()
  },
)
const toastpop = () => {
  const toast = new Toast(document.getElementById('toastMsg'))
  if (appVariable.toastMsg) {
    let data = toast.show()
    console.log(data)
  }
  setTimeout(() => {
    appVariable.toastMsg = undefined
  }, 3000)
}

const progresses = [] as ProgressFinisher[]

const permitUrl = [
  //로그인 없이 허용 가능한 페이지
  { url: '/user/login' }, //로그인
  { url: '/user/userPayment' }, // 수기결제(사용자)
  { url: '/user/managerPayment' }, // 수기결제(관리자)
  { url: '/map/findCarMan' }, // 비로그인 주소 정보
  { url: '/map/useGeoLocation' }, // 위치API 확인
  { url: '/map/deliveryImageAny' }, // 아이사랑,거래처 이미지 조회 Any
  { url: '/client/centerAlarmAny' }, // 아이사랑,센터 도착알림 정보 공유페이지 Any
  
]
const validationUrl = (nowUrl: string) => {
  //허용 가능한 페이지 체크
  const permitPage = permitUrl.filter((item) => nowUrl.indexOf(item.url) > -1)
  let returnData = false
  if (permitPage.length > 0) {
    returnData = true
  }
  setPageOption(returnData)
  return returnData
}

const setPageOption = (isSet: boolean) => {
  console.log(isSet)
  if (isSet) {
    appOption.appContentClass = 'p-0'
  } else {
    appOption.appContentClass = ''
  }
  appOption.appSidebarHide = isSet
  appOption.appHeaderHide = isSet
}

const initData = () => {
  progresses.push(useProgress().start())
  appOption.appSidebarMobileToggled = false
  document.body.scrollTop = 0
  document.documentElement.scrollTop = 0
}

routers.beforeEach(async (to, from) => {
  const userInfo = sessionStorage.getItem('userInfo')
  validationUrl(to.path)
  if (!userInfo) {
    if (!validationUrl(to.path)) {
      //허용 불가 페이지인 경우 로그인 페이지로 이동
      router.push('/')
    } else {
      initData()
    }
  } else {
    initData()
  }
})
routers.afterEach(async (to, from) => {
  progresses.pop()?.finish()
})
</script>

<template>
  <div
    class="app"
    v-bind:class="{
      'app-sidebar-end-mobile-toggled': appOption.appSidebarEndMobileToggled,
      'app-content-full-height': appOption.appContentFullHeight,
      'app-content-full-width': appOption.appSidebarHide,
    }"
  >
    <vue3-progress-bar />
    <app-header v-if="!appOption.appHeaderHide" />

    <div class="layout_bundle">
      <app-sidebar v-if="!appOption.appSidebarHide" />

      <div class="main_contents_area" v-bind:class="{ dimmed: appOption.isDimmed }">
        <router-view></router-view>
      </div>
    </div>
    <app-footer v-if="appOption.appFooter" />
    <!-- <app-theme-panel /> -->
  </div>

  <div class="fa-3x progressbar" v-if="appOption.isProgressbar">
    <i class="fas fa-spinner fa-pulse"></i>
  </div>

  <div class="position-fixed end-0 top-0 me-5 pt-5 mt-5 toasCenter" style="z-index: 99999 !important" v-if="appVariable?.toastMsg">
    <div class="toast" :class="appVariable?.toastMsg ? 'show' : 'hide'" data-autohide="false" id="toastMsg" data-bs-config='{"delay":3000}'>
      <div class="toast-header">
        <i class="far fa-bell text-muted me-2"></i>
        <strong class="me-auto">알림</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
      </div>
      <div class="toast-body" v-html="appVariable?.toastMsg"></div>
    </div>
  </div>
</template>

<style>
.progressbar {
  position: fixed;
  bottom: 50%;
  left: 55%;
}

.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}

.toasCenter {
  width: 350px;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
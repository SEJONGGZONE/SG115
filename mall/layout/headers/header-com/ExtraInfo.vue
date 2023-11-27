<template>
  <ul class="extra-info">
      <li>
          <div class="my-account">
              <ul v-if="!userStore.isLogin"> 
                  <!--<li><nuxt-link href="/wishlist">Wishlist</nuxt-link></li>-->
                  <li><nuxt-link href="/login">로그인</nuxt-link></li>
                  <li><nuxt-link href="/register">회원가입</nuxt-link></li>
              </ul>
              <ul v-else> 
                    <li ><nuxt-link href="/customerUpdate">회원정보</nuxt-link></li>
                    <li v-if="userStore.getUserInfo?.TYPE === '20'"><nuxt-link href="/favList">관심상품보기</nuxt-link></li>
                    <li ><nuxt-link href="/orderListHistory">주문조회</nuxt-link></li>
                    <li ><nuxt-link href="/deliveryList">배송지관리</nuxt-link></li> 
                    <li ><nuxt-link href="/qnaList">1:1 문의</nuxt-link></li> 
                    <li @click="logout"><a style="cursor: pointer;">로그아웃</a></li>
              </ul>
          </div>
      </li>
      <!--
       <li>
          <div class="lang">
              <div class="extra-title">
                  <h5>Language</h5>
              </div>
              <ul>
                  <li><a href="#">English</a></li>
                  <li><a href="#">France</a></li>
                  <li><a href="#">Germany</a></li>
                  <li><a href="#">Bangla</a></li>
              </ul>
          </div>
      </li>
      <li>
          <div class="currency">
              <div class="extra-title">
                  <h5>currency</h5>
              </div>
              <ul>
                  <li><a href="#">USD - US Dollar</a></li>
                  <li><a href="#">EUR - Ruro</a></li>
                  <li><a href="#">GBP - Britis Pound</a></li>
                  <li><a href="#">INR - Indian Rupee</a></li>
              </ul>
          </div>
      </li>
      -->
  </ul>
</template>

<script setup lang="ts"> 
import { useUserStore } from '@/store/useUser'; 
import * as common_utils from "@/common/utils.ts";

const userStore = useUserStore();   
const router = useRouter()

const logout = ()=>{
    userStore.logout()//로그아웃 
    router.push('/')
}   
 
const coming = ()=>{
    common_utils.fxAlert("작업중입니다.");  
}
</script>

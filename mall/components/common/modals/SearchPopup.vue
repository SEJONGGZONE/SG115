<template>
  <section :class="`header__search white-bg transition-3 ${showSearch?'search-opened':''}`">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="header__search-inner text-center">
                      <div class="header__search-btn" @click="showSearch = false">
                          <a href="#" class="header__search-btn-close">
                            <i class="fal fa-times" style="font-size:28px;font-weight:200;"></i>
                          </a>
                      </div>
                      <div class="header__search-header">
                          <h3>상품 검색</h3>
                      </div>
                      <div class="header__search-input p-relative">
                          <input ref="inputRef" class="text-center" type="text" v-model="keyWord" @keyup.enter="moveTo()" tabindex="0" style="font-size: 20px; margin: 0 auto; outline: none;" autofocus>
                          <button type="submit" @click="moveTo()"><i class="far fa-search" style="font-size:24px;font-weight:500;"></i></button>
                      </div>
                </div>
            </div>
        </div>
    </div>
  </section>
  <!-- body overlay start -->
  <div @click="showSearch = false" 
   :class="`body-overlay transition-3 ${showSearch?'opened':''}`">
  </div>
  <!-- body overlay end -->
</template>

<script lang="ts">
import { setgroups } from 'process';
import { defineComponent } from 'vue';
import * as listApi from '@/api';
import { globalOptions } from 'vue3-toastify';

export default defineComponent({
//  emits:['searchPopup'],
 setup(){
  const keyWord = ref('')  
  const moveTo=() =>{
    window.location.href = `/searchResultList?keyWord=${keyWord.value}`;//categoryList
  }
  return {
    keyWord,moveTo
  }
 },
 data() {
  return {
    showSearch:false,
    isFirstUpdate: true,
  }
 },
 updated() {
  if (this.isFirstUpdate) {
    const inputElement = this.$refs.inputRef;
    nextTick(() => {
			setTimeout(() => {
        if (inputElement) {
          inputElement.focus();
          //inputElement.value = '검색어를 입력하세요..'
        }
      }, 100);
		}); 
    this.isFirstUpdate = false;
  }
 },
 computed: {
  
 },
 methods:{  
  
  openSearchPopup(){
    console.log('first')
    this.showSearch = true
  }
 }
})
</script>
<style>
  .header__search-input input::placeholder {
    font-size: 20px;
  }
</style>

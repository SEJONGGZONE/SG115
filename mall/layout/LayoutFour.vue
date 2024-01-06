<template>
  <div>
    <Header/>
    <main class="box-white white-bg">
        <div class="container">
          <div class='box-grey-inner'>
            <div class="row">
              <div class='col-xl-1 main-col ' ></div>
              <div class='col-xl-10 main-col' >
                <div class="followMenu">
                  <ul>
                    <!-- <li v-if="isLogin" style="padding-bottom: 20px;">
                      <a style="cursor: default;">
                        <span class="txt">'{{ nameS }}' 님</span>
                      </a>
                    </li> -->
                    <li data-aos="fade-up" >
                      <a @click="goRecent()">
                        <span class="img"><i class="fal fa-clock"></i></span>
                        <span class="txt">최근 본 상품</span>  
                      </a>
                    </li>   
                    <li data-aos="fade-left"  v-if="isLogin">
                      <a @click="goFav()">
                        <span class="img"><i class="fas fa-heart"></i></span>
                        <span class="txt">관심상품</span>
                      </a>
                    </li>  
                    <li data-aos="fade-right"  v-if="isLogin">
                      <a @click="goQna()">
                        <span class="img"><i class="fal fa-comment-dots"></i></span>
                        <span class="txt">1:1 문의</span>
                      </a>
                    </li> 
                    <li data-aos="fade-down" >
                      <a @click="goCart()">
                        <span class="img"><i class="fal fa-cart-plus"></i></span>
                        <span class="txt">장바구니</span>
                      </a>
                    </li> 
                    
                  </ul>  
                </div>
                <slot></slot>
              </div>
            </div>
          </div>
        </div>
      </main>
      <footer-two/>
    <back-to-top />
  </div>
</template>

<script setup lang="ts"> 
import { onMounted, ref } from "vue";
import Header from "./headers/Header.vue";
import FooterTwo from "./footers/FooterTwo.vue";
import BackToTop from "@/components/back-to-top/BackToTop.vue";
import { useUserStore } from "@/store/useUser";
 
//로그인 정보
const store = useUserStore();
const nameS = ref(store.getUserInfo?.NAME);
const isLogin = computed(() => {
  return store.isLogin;
});

/*-----------------------------------------------onMounted*/


/*-----------------------------------------------method*/
const router = useRouter()
const goRecent = () => {//최근 본 상품
  router.push('/recentList');
}
const goFav = () => {//관심상품
  router.push('/favList');
}
const goQna = () => {//1:1 문의
  router.push('/qnaList');
}
const goCart = () => {//장바구니
  router.push('/cart');
}

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


</script>

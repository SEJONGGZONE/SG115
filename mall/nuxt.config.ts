// https://nuxt.com/docs/api/configuration/nuxt-config

//import fs from 'fs'
//import path from 'path'

/*
   - 크몽작업소스..
   
    <!-- CSS -->
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>


    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link href="https://webfontworld.github.io/sunn/SUIT.css" rel="stylesheet">

    <!-- JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/main.js"></script>

*/
export default defineNuxtConfig({
  server: {
    port: 5194,
    host: '0.0.0.0'
    //  https: {
    //   key : fs.readFileSync('C:/Apache24/conf/ssl_cookzzang/ssl_cookzzang.key'),
    //  cert : fs.readFileSync('C:/Apache24/conf/ssl_cookzzang/ssl.crt')
    //  }
  },
  build: {
    hotMiddleware: true,
  },
  css: [
    "bootstrap/scss/bootstrap.scss",
    "vue3-carousel/dist/carousel.css",
    "~/assets/scss/main.scss",
    "~/assets/kmong/css/common.css", // 크몽작업소스 적용
    "~/assets/kmong/css/style.css", // 크몽작업소스 적용
    "~/assets/kmong/css/swiper-bundle.min.css",
  ],
  modules: [
    "nuxt-swiper",
    [
      "@pinia/nuxt",
      {
        autoImports: ["defineStore", ["defineStore", "definePiniaStore"]],
      },
      "@nuxtjs/proxy",
      "vue-sweetalert2/nuxt",
    ],
  ],
  sweetalert: {
    confirmButtonColor: "#41b882",
    cancelButtonColor: "#ff7674",
  },
  app: {
    head: {
      title: "(주)성창FOOD에 오신걸 환영합니다.",
      // link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'}],
      script: [
        {
          src: "https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js",
        },
        {
          src: "https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js",
        },
        {
          src: "https://js.tosspayments.com/v1/payment-widget",
        },
      ],
      //    meta: [
      //     {
      //      "http-equiv" : "Content-Security-Policy",
      //        "content" : "upgrade-insecure-requests"
      //      }
      //   ]
    },
  },
  // vite: {
  //   server: {
  //     proxy: {
  //       "/api": {
  //         target: "http://sjwas.gzonesoft.co.kr:24484/",
  //         changeOrigin: true,
  //       },
  //     },
  //   },
  // },
});

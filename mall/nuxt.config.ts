// https://nuxt.com/docs/api/configuration/nuxt-config

//import fs from 'fs'
//import path from 'path'

export default defineNuxtConfig({
  server: {
    port: 33780,
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
      title: "쿡짱몰에 오신걸 환영합니다.",
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

<template>
  <section class="footer__area white-bg p-relative pt-50">
      <div class="footer__top ">
          <div class="container">
              <div class="row">

                <!--카테고리 영역-->
                  <div class="brand__area">
                    <div class="container custom-container-2">
                      <div class="brand__slider-active p-relative ">
                        <Carousel
                          ref="slider_1"
                          :items-to-show="3"  
                          :breakpoints="{  
                            1100: {
                              itemsToShow: 4,
                            }, 
                            992: {
                              itemsToShow: 4,
                            }, 
                            750: {
                              itemsToShow: 3,
                            },
                            700: {
                              itemsToShow: 3,
                            },
                            550: {
                              itemsToShow: 2,
                            },
                            400: {
                              itemsToShow: 2,
                            },
                            0: {
                              itemsToShow: 1,
                            },
                          }"
                        >
                          <Slide v-for="(item, i) in widget_data" :key="i"  >
                              <div class="footer__widget mb-30" data-aos-once="true" data-aos="fade-down">
                                  <div class="footer__widget-title footer__widget-title-2 mb-25" style="cursor: pointer;">
                                      <h5 v-if="item.id === 2"><a :href="privacyPdf" download :title="item.title + ' 다운로드'">{{item.title}}</a></h5> <!--개인정보처리방침-->
                                      <h5 v-else><a :href="`${item.link}`" :title="item.title">{{item.title}}</a></h5> 
                                  </div>
                                  <div class="footer__widget-content">
                                      <div class="footer__links footer__links-2">
                                          <ul>
                                              <li v-for="(list,i) in item.links" :key="i" style="width:auto;">
                                                <a  class="slider-active" href="">{{list.list}}</a>
                                              </li>
                                          </ul>
                                      </div>
                                  </div>
                              </div>
                          </Slide>
                        </Carousel> 
                      </div>
                    </div>
                  </div> 
                  
              </div>
          </div>
      </div>
      <div class="footer__bottom footer__bottom-2">
          
          <div class="container">
            <div class="footer__bottom-inner footer__bottom-inner-2">
              <div class="row mr-20 ml-20">
                <div class="col-xl-3 col-lg-3 col-md-6 col-6" style="border-right: 1px solid #dedede;padding-left: 40px;">
                    <div class="cursor-pointer">
                      <img data-aos-once="true" data-aos="fade-up" src="~/assets/img/notification.png" alt="notification" style="width: 40px;height: 40px;margin-bottom: 10px;" @click="goGongji" title="공지사항으로 이동">
                      <h5 data-aos-once="true" data-aos="fade-right" style="font-size: 20px;font-weight: 500;" @click="goGongji" title="공지사항으로 이동">공지사항</h5>
                      <div data-aos-once="true" data-aos="fade-up" style="font-size:15px;" v-for="(col, i) in noticeList" :key="i">
                        <a :href="`/noticeDetail?gno=${col.GNO}`" :title="col?.TITLE">{{ col?.TITLE }}</a><br/>
                      </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-6" style="border-right: 1px solid #dedede;padding-left: 40px;">
                    <img data-aos-once="true" data-aos="fade-up" src="~/assets/img/delivery.png" alt="delivery" style="width: 40px;height: 40px;margin-bottom: 10px;">
                    <h5 data-aos-once="true" data-aos="fade-right" style="font-size: 20px;font-weight: 500;">무료 직배송 지역</h5>
                    <span data-aos-once="true" data-aos="fade-up" style="font-size:18px; color: #599ce8;font-weight:500;">
                      대전/세종/청주/천안/아산<br/>
                    </span>
                    <span data-aos-once="true" data-aos="fade-up" style="font-size:15px;">일부지역 배송가능문의는 고객센터로<br/>확인바랍니다.</span>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-6" style="border-right: 1px solid #dedede;padding-left: 40px;">
                    <div class="cursor-pointer">
                      <img data-aos-once="true" data-aos="fade-up" src="~/assets/img/telephone-call.png" alt="telephone-call" style="width: 40px;height: 40px;margin-bottom: 10px;" @click="goQna" title="1:1 문의 게시판으로 이동">
                      <h5 data-aos-once="true" data-aos="fade-right" style="font-size: 20px;font-weight: 500;" @click="goQna" title="1:1 문의 게시판으로 이동">고객센터</h5>
                      <span data-aos-once="true" data-aos="fade-up" style="font-size:18px;font-weight: 500;color:#222222;">010-5427-1974<br/></span>
                      <span data-aos-once="true" data-aos="fade-up" style="font-size:15px; color: #237627;">평일 10시~5시(점심시간 12시~1시)<br/></span>
                      <span data-aos-once="true" data-aos="fade-up" style="font-size:15px;">토요일/일요일/명절 휴무</span>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-6" style="padding-left: 40px;">
                    <img data-aos-once="true" data-aos="fade-up" src="~/assets/img/credit-card.png" alt="credit-card" style="width: 40px;height: 40px;margin-bottom: 10px;">
                    <h5 data-aos-once="true" data-aos="fade-right" style="font-size: 20px;font-weight: 500;">무통장입금안내</h5>
                    <span data-aos-once="true" data-aos="fade-up" style="font-size:18px;font-weight: 500;color:#222222;">농협 351-0777-4973-23<br/></span>
                    <span data-aos-once="true" data-aos="fade-up" style="font-size:15px;">예금주 : 주식회사 성창푸드</span>
                </div>
              </div>          
            </div>
          </div>

          <div class="container copyright">
              <div class="footer__bottom-inner footer__bottom-inner-2">
                  <div class="row">
                      <div data-aos-once="true" data-aos="fade-up" class="col-xl-2 col-lg-2 col-md-3 col-3">
                        <div class="logo" style="cursor: pointer;">
                            <a @click="movePage('/')">
                                <img src="~/assets/img/sungchang/logo_01_100.png" alt="logo" class="set-logo">
                            </a>
                        </div> 
                      </div>
                      <div data-aos-once="true" data-aos="fade-down" class="col-xl-10 col-lg-10 col-md-9 col-9" style="font-size: 15px;"> 
                        <span style="font-size:15px;">
                          사업자등록번호 : 779-88-00012    통신판매신고번호 : 2006-경기성남-0692호    상호명 : (주)성창푸드    대표 : 고재철<br/>
                          대전광역시 대덕구 신탄진로115번안길 29    고객센터 : 042-635-9978 / 010-5427-1974 / gjc1974@naver.com
                        </span>
                      </div>        
                  </div>
              </div>
          </div>
      </div>
  </section>
</template>

<script setup> 
import Social from '@/components/social/Social.vue'
import * as operateApi from '@/api';
import { useUserStore } from '@/store/useUser';
import privacyPdf from "~/assets/img/privacyPolicy.pdf";
import * as common_utils from "@/common/utils.ts";
import AOS from "aos";
//import kmong_jquery_3_4_1 from "@/assets/kmong/js/jquery-3.4.1.min.js";


import { Carousel, Slide } from "vue3-carousel";  
    const widget_data = [
          {
            id: 1,
            title: '회사소개',
            link: '/companyInfo',
          },
          {
            id: 2,
            title: '개인정보처리방침',
            link: '~/assets/img/privacyPolicy.pdf',
          },
          {
            id: 3,
            title: '입점/제휴문의',
            link: '/consult',
          },
          {
            id: 4,
            title: '대량구매',
          },
        ] 
const isMobile = ref(false);

const store = useUserStore();
const isLogin = computed(()=>{
    return store.isLogin
})

onMounted(()=>{ 
  checkMobileScreen();
  window.addEventListener("resize", checkMobileScreen);
  doNoticeList()
})
onBeforeRouteLeave(()=>{
  window.removeEventListener("resize", checkMobileScreen);
})

const checkMobileScreen=() =>{
  isMobile.value = window.innerWidth <= 767;   
}
const router = useRouter()
const goGongji=()=>{
  router.push(`/noticeList`);//공지사항 화면으로 이동
}
const goQna=()=>{
  if(!isLogin.value){
        common_utils.fxAlertOk("로그인 후 이용해 주세요",{type:'warning'}).then(()=>{router.push(`/login`);});
        return
  }else{
        router.push(`/qnaList`);//질문답변 화면으로 이동
  }
}

const noticeList = ref([])//공지사항 목록
const doNoticeList = async ()=>{//공지사항목록 가져오기
    noticeList.value = []

      let data;
      let param = { 
    }
      try {
        data = await operateApi.operate_operateNoticeSel(param)
        if(data.RecordCount > 0){
          const firstThreeRows = data.RecordSet.slice(0, 3);//3개 공지사항만 컷트 css 어그러짐 대비
          noticeList.value.push(...firstThreeRows);
        }
      } catch (error) {
        console.error(error);
      }finally{
        
      }
}

// console.log(kmong_jquery_3_4_1.value);
// console.log(kmong_main_js.value);

</script>
<style>
.set-logo{
  margin-top: -10px; 
  margin-left:80px; 
  height: 90%;
}
@media (max-width: 1200px) {
  .set-logo{
    margin-top: 0px; 
  margin-left: 0px; 
  }
}
</style>

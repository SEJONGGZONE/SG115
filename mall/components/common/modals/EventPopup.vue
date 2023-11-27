<template>   
    <div id="modal-overlay" class="modal-overlay " style="padding:0.2rem 0.2rem 0.2rem 0.2rem;">
        <div class="product__modal-wrapper p-relative">
            <!-- 닫기버튼 -->
            <div class="product__modal-close p-absolute" @click="clodeModal()" style="margin:0.2rem 0.2rem 0.2rem 0.2rem;">
                <button style="background-color: #ffffff;"><i class="fal fa-times" style="font-size:2rem;line-height:40px; color:#000000"></i></button>
            </div> 
            <!-- 타이틀 -->
            <div class="postbox__title mb-20 pl-15 pt-20" style="background-color: #000000; padding:0.1rem 0px;">
                <h2 style="font-size:1.8rem;font-weight:400; color: #FFFFFF;">&nbsp;공지사항</h2> 
            </div>
            <!-- 내용 -->
            <div class="brand__slider-active p-relative" style="background-color: #f9f7ff2d; margin-top:-1rem;">
                <Carousel   :autoplay="5000" 
                            :wrap-around="isWrapAround"  
                            items-to-show="1"
                            style="padding:0px;">
                    <Slide v-for="(item, i) in itemList" :key="i" class="pl-15 pr-15 pb-30" style="padding:0px 0.2rem;"> 
                        <div class="container cursor-pointer" @click="goNoticeDetail(item.GNO)" title="공지사항으로 이동"> 
                            <div class="row pt-25" style="background-color:#ededed; padding-top:7px;">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="product__modal-box">
                                        <div class="tab-content" id="nav-tabContent" > 
                                            <h4 style="font-size:1.5rem;font-weight:500;color:#0a005f;"> {{ item.TITLE }} </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-10"> 
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 autoHeight" v-html="item.MSG" style="font-size:16px;color:#696969;"> 
                                </div>
                            </div> 
                        
                        </div>
                    </Slide>                    
                    <template #addons>
                        <Pagination style="justify-content: center; padding:10px 0px 10px 0px; background-color:#0a005f07;"/>
                    </template>
                </Carousel>
                <div class="row pt-30" style="text-align:center; margin-top:-1rem;">
                    <div class="col-md-12 ms-auto"> 
                        <button @click="clodeOnModal" class="os-btn os-btn-black w-30 pr-5" style="font-size:1.2rem;font-weight:500; margin:0px; padding:0px 15px; width:100%;">
                        오늘 하루 보지 않기
                        </button> 
                    </div>
                </div>
            </div>

        </div>  
    </div> 
</template>






<script setup>
import * as operateApi from '@/api'  
import { Carousel, Slide,Pagination } from "vue3-carousel";
import * as common_utils from "@/common/utils.ts";

const router = useRouter()
const eventList = ref([]) 
const isWrapAround = computed(()=> eventList.value.length > 1)

const emit = defineEmits(["closeModal"]);
const props = defineProps({
    itemList: {
        type : Array,
        default : []
    },
});
 
const clodeModal = () => {//정렬 후 업데이트된 리스트 
  common_utils.removeDimmed()
 // common_utils.setCookie("hidePopup", "true", 1);
  emit('closeModal') // 부모에서는 @input에 쓴 메소드가 호출된다. 인수에value
} 

const clodeOnModal = () => {//정렬 후 업데이트된 리스트  
 common_utils.setCookie("hidePopup", "true", 1); 
 clodeModal()
} 

const goNoticeDetail=(gno)=>{
    router.push(`/noticeDetail?gno=${gno}`);//공지사항상세 화면으로 이동
    clodeModal()
}



 
</script>
<style scoped>
.modal-overlay {
  max-width: 80%;
  width: 600px;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0; 
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.5); /* 배경을 어둡게 처리 */
  z-index: 9999;

  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  height: max-content;

  transform: translate(-50%, -40%); /* 화면 중앙으로 이동 */ 
  top: 40%;
  left: 50%;
  border: 1px solid black;
} 
.autoHeight {
    height: 400px;
    overflow: auto;
}

@media only screen and (max-height:668px){ 
    .autoHeight {
     height: 279px; 
    }
}

</style>

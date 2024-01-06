<template>
  <event-Popup v-if="isEventModal"  :itemList="eventList" @closeModal="closeModal"/>  
  <NuxtPage></NuxtPage> 
</template>

<script setup >

import * as operateApi from '@/api'  
import { useCartStore } from './store/useCart';
import { useUserStore } from './store/useUser';
import { useModalStore } from '@/store/useModal';
import EventPopup from '@/components/common/modals/EventPopup.vue'
import { removeDimmed,dimmed,getCookie} from "@/common/utils.ts";  
const router = useRouter()


const eventList = ref([])
const state = useCartStore();
const userState = useUserStore();

onBeforeMount( async ()=>{
  
  state.get_cart_products;
  userState.getUserInfo;
  createDimmElement()
  if(router.currentRoute.value.name === 'index'){
    searchNotice()
  }
  let localIp = localStorage.getItem("IP")
  if(!localIp){
    let myIp = await operateApi.memeber_getIp() 
    localStorage.setItem("IP",myIp.data.ip)
  }
})

const isEventModal = ref(false)

const createDimmElement = () => {
 let useMOdal = useModalStore
  let body = document.getElementsByTagName("body")[0];
  let divElement = document.createElement("div");
  divElement.setAttribute('id','dimmedDiv')
  divElement.addEventListener('click',()=>{ 
    removeDimmed()
  }) 
    body.appendChild(divElement);
}
   
const closeModal = () => { // 모달 창 닫기
  isEventModal.value = false
}
 

const isShowNoticePop = () =>{ //팝업창 보여줄지 결정
        // 쿠키 값 가져오기 
    var hidePopupValue = getCookie("hidePopup");

    if (hidePopupValue !== "true") {  
        if(eventList.value.length > 0){
          
          isEventModal.value =  true
          dimmed()
        }else{
          isEventModal.value = false
        }
    }else{ 
      isEventModal.value = false
    }
}

const searchNotice = async ()=>{//공지사항 조회
  let param = { 
  }
  let dataObj = await operateApi.operate_operateNoticeSel(param)
  let data = dataObj.data;
      if (data.ResultCode == "00") {
        let notice = data.RecordSet.map(item=>{
          item.MSG = item.MSG.replaceAll("\r\n","<br>");
          return item
        })
        eventList.value = notice 
        isShowNoticePop()
      }
}
</script>
 
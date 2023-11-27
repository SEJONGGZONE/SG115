
<script setup lang="ts"> 
import { ref, computed } from 'vue';
import { useUserStore } from '@/store/useUser';
import * as operateApi from '@/api';
import * as common_utils from "@/common/utils.ts";
    
//로그인 정보
const store = useUserStore();
const nameS = ref(store.getUserInfo?.NAME);
const clcodeS = store.getUserInfo?.CLCODE;
const userNoS = store.getUserInfo?.USER_NO;
const companyNameS = store.getUserInfo?.COMPANY_NAME;
let phoneS = formatPhoneNumber(store.getUserInfo?.PHONE);
const addressS_1 = store.getUserInfo?.ADDRESS1;
const addressS_2 = store.getUserInfo?.ADDRESS2;
const userInfoPostNo = store.getUserInfo?.POST_NO
const loginType = store.getUserInfo?.TYPE;//10-일반사용자(장바구니 사용가능), 20-사업자(관심상품, 장바구니 사용가능)
const username = computed(()=>{
    return companyNameS ? companyNameS+"("+nameS.value+")" : nameS.value
})
const isLogin = computed(()=>{
    return store.isLogin
})
const noticeList = ref([])//공지사항 목록

/****************************************************************************************** mounted */
onMounted(()=>{
      doNoticeList();//배송지 목록
})

/****************************************************************************************** method */
function formatPhoneNumber(phoneNumber) {// 정규식을 사용하여 3-4-4 형식으로 변환

      if (!phoneNumber) {
        return ''; // null 또는 undefined인 경우 빈 문자열 반환
      }

      const cleaned = phoneNumber.replace(/\D/g, '');// 숫자를 제외한 모든 문자 제거
      const regex = /^(\d{3})(\d{4})(\d{4})$/;
      const parts = cleaned.match(regex);

      if (parts) {
        return `${parts[1]}-${parts[2]}-${parts[3]}`;// 변환된 형식으로 전화번호 반환
      }
      return phoneNumber;// 변환에 실패한 경우 원본 전화번호 반환
}

const router = useRouter()
const goNoticeDetail=(gno)=>{
  router.push(`/noticeDetail?gno=${gno}`);//공지사항상세 화면으로 이동
}

/****************************************************************************************** api호출 */
const doNoticeList = async ()=>{//배송지목록 가져오기
    noticeList.value = []

      let data;
      let param = { 
    }
      try {
        data = await operateApi.operate_operateNoticeSel(param)
        if(data.RecordCount > 0){
          noticeList.value.push(...data.RecordSet);
        }
      } catch (error) {
        console.error(error);
      }finally{
        
      }
}


const currentPage = ref(1);//현재 페이지 번호
const itemsPerPage = 5;//페이지 당 게시글 항목 수
const maxPageNumbers = 5;//화면에 표시될 페이지 번호의 개수
const totalPages = computed(() => Math.ceil(noticeList.value.length / itemsPerPage));//전체 페이지 수

const pages = computed(() => {
  let startPage = 0
  let endPage = 0
  
  if(noticeList.value.length <= itemsPerPage){// 총 항목 수가 itemsPerPage보다 작거나 같으면 페이지 번호가 보이지 않도록
    return 1
  }

  //startPage 관련 88,94라인 주석 해제 시, "1 2 다음"상태에서 다음버튼 클릭 하면 "이전 2 3"으로 이전, 다음 버튼 사이에 숫자가 maxPageNumbers로 일정함.
  //startPage = Math.max(currentPage.value - Math.floor(maxPageNumbers / 2), 1);
  startPage = (currentGroup.value - 1) * maxPageNumbers + 1;
  endPage = startPage + maxPageNumbers - 1;

  if (endPage > totalPages.value) {
    endPage = totalPages.value;
    //startPage = Math.max(endPage - maxPageNumbers + 1, 1)
  }
  
  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
});

const currentItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return noticeList.value.slice(start, start + itemsPerPage);
});

const setPage = (page) => {
  if (page > 0 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
// 현재 페이지 그룹을 계산합니다. 예를 들어, 7페이지의 경우 2번째 그룹에 있습니다.
const currentGroup = computed(() => Math.ceil(currentPage.value / maxPageNumbers));

// 다음과 이전 페이지 그룹으로 이동하는 메소드입니다.
const nextGroup = () => {
  let newPage = (currentGroup.value * maxPageNumbers) + 1;
  if (newPage <= totalPages.value) {
    setPage(newPage);
  }
};
const prevGroup = () => {
  let newPage = (currentGroup.value - 1) * maxPageNumbers;
  if (newPage > 0) {
    setPage(newPage);
  }
};

// '이전'과 '다음' 버튼의 활성화/비활성화를 결정하기 위한 계산 속성입니다.
const showNextButton = computed(() => currentGroup.value < Math.ceil(totalPages.value / maxPageNumbers));
const showPrevButton = computed(() => currentGroup.value > 1);
</script>

<template>
  <div class="brand__area pb-60 pt-100"> 
    <div class="container custom-container-2 cus-mtop">
      <!--화면명-->
      <div class="section__title-wrapper text-center mb-55 p-relative">
          <div class="section__title mb-10">
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">공지사항</h2>
          </div>
      </div>
      <!--화면내용-->
      <div class="basic-login pt-20 pb-20">
        <!--내용_테이블영역-->
        <div class="table-content table-responsive mb-10" style="max-height:800px;" >
              <div class="mb-30">
                <table class="table table-hover web-table" style="min-width: 700px;">
                    <thead  style="position: sticky; top: 0; height: 50px; font-size: 20px; vertical-align: middle; ">
                        <tr class="headerColor">
                              <th >순서</th>
                              <th >제목</th>
                              <!--<th >게시기간</th>-->
                              <th >작성일</th>
                        </tr>
                      </thead>
                      <tbody > 
                          <tr class="underLine" v-if="currentItems.length > 0" v-for="(item,i) in currentItems" :key="i" @click="goNoticeDetail(item.GNO)">
                            <td class="product-name">
                              <span>{{ i+1 }}</span>
                            </td>
                            <td class="product-name">
                              <span v-html="item.TITLE"></span>
                            </td>
                            <!--
                            <td class="product-name">
                                <span></span>
                            </td>
                            -->
                            <td class="product-name">
                                <span v-html="item.DATETIME"></span>
                            </td>
                          </tr>
                          <tr class="underLine" v-else><td colspan="6">등록된 공지사항이 없습니다.</td></tr>
                      </tbody>
                </table>
                <!--모바일용-->
                <div class="mobile-card">
                    <div v-if="currentItems.length > 0" v-for="(item, i) in currentItems" :key="i" @click="goNoticeDetail(item.GNO)" class="mobile-card-border">
                        <div><strong>{{ item.TITLE }}</strong></div>
                        <div>게시기간: </div>
                        <div>등록일자: {{ item.DATETIME }}</div>
                    </div>
                    <div v-if="currentItems.length === 0" style="text-align: center;">
                        등록된 공지사항이 없습니다.
                    </div>
                </div>
              </div> 
              <!--페이징-->
              <div v-if="totalPages > 1" class="mb-20" style="display: flex; justify-content: center;">
                <button class="pageBtn" @click="prevGroup()" v-if="showPrevButton">&lt;</button>
                <button class="pageBtn" v-for="page in pages" :key="page" @click="setPage(page)" :class="{ pageActive: currentPage === page }">
                  <span>
                    {{ page }}
                  </span>
                </button>
                <button class="pageBtn" @click="nextGroup()" v-if="showNextButton">&gt;</button>
              </div>
        </div>
      </div>
    </div>
  </div>
</template> 
<style scoped>

</style>

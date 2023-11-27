
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
const qnaList = ref([])//공지사항 목록

//게시판 페이징 정보
const currentPage = ref(1);//현재 페이지 번호
const itemsPerPage = 5;//페이지 당 게시글 항목 수
const maxPageNumbers = 5;//화면에 표시될 페이지 번호의 개수
const totalPages = computed(() => Math.ceil(qnaList.value.length / itemsPerPage));//전체 페이지 수

//조회 페이지 정보
const pageNum = ref(1);
const pageSize = ref(1000000);

//검색어
const searchKeyword = ref();

/****************************************************************************************** mounted */
onMounted(()=>{
      if(!isLogin.value){
            common_utils.fxAlertOk("로그인 후 이용해 주세요",{type:'warning'}).then(()=>{router.push(`/login`);});
            return
      }else{
          doQnaList();//배송지 목록
      }
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
const goQnaDetail=(geonum)=>{
      router.push(`/qnaDetail?geonum=${geonum}&searchKeyword=${searchKeyword.value || ''}`);//공지사항상세 화면으로 이동
}
const goWrite=()=>{
      if(!isLogin.value){
            common_utils.fxAlert("로그인 후 이용해 주세요");
            return
      }else{
          router.push(`/qnaWrite`);//공지사항작성 화면으로 이동
      }
}

const goSearch=()=>{//검색btn
      doQnaList();
}
const goDelQnaList=(item)=>{//삭제btn
      delQnaList(item);
}
/****************************************************************************************** api호출 */

const doQnaList = async ()=>{//게시판 목록 가져오기
      qnaList.value = []

      let data;
      let param = { 
          geonum : "",
          boardType : "10",
          keyword : searchKeyword.value ?? "",
          pageSize : pageSize.value,
          pageNum : pageNum.value,
          refNo : '0',      //메인글
          inputUser :userNoS 
      }
      try {
          data = await operateApi.operate_operateQnaSel(param)
          if(data.RecordCount > 0){
            qnaList.value.push(...data.RecordSet);
          }
      } catch (error) {
          console.error(error);
      }
}
const delQnaList = async (item)=>{//게시판 삭제

      let data;
      let param = { 
          geonum : item.GEONUM,
          inputUser :userNoS 
      }
      try {
          data = await operateApi.operate_operateQnaDel(param)
          if(data.ResultCode === "00"){
            common_utils.fxAlert("삭제되었습니다.")
          }
      } catch (error) {
          console.error(error);
      }finally{
          doQnaList();
      }
}

/****************************************************************************************** 페이징 */
const pages = computed(() => {
  let startPage = 0
  let endPage = 0
  
  if(qnaList.value.length <= itemsPerPage){// 총 항목 수가 itemsPerPage보다 작거나 같으면 페이지 번호가 보이지 않도록
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
  return qnaList.value.slice(start, start + itemsPerPage);
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
              <h2 style="font-size: 30px;font-weight: 500;line-height: unset;">1:1 문의</h2>
          </div>
      </div>
      <!--화면내용-->
      <div class="basic-login pt-20 pb-20">
        
        <!--검색 및 글쓰기 버튼-->
        <div class="nav row justify-content-between">  
          <div class="col-xl-6 col-md-6 col-sm-8 checkout-form-list d-flex align-items-center">
              <input type="text" v-model="searchKeyword" class="flex-grow-1 mr-2" @keyup.enter="goSearch()" style="height: 52px !important;">
              <button class="nav-link btn-primary active" @click="goSearch()" style="width: auto; height: 52px !important; white-space: nowrap;">
                  <i class="fas fa-search"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;검색</span>
              </button>
          </div>
          <div class="col-xl-4 col-md-6 col-sm-4 checkout-form-list d-flex justify-content-end">
              <button class="nav-link btn-primary active" @click="goWrite()" style="width: auto; height: 52px !important;">
                <i class="fas fa-pen"></i><span class="d-none d-md-inline-block" style="color: #ffffff !important;">&nbsp;글쓰기</span>
              </button>
          </div>
        </div>

        <!--내용_테이블영역-->
        <div class="table-content table-responsive mb-10" style="max-height:800px;" >
              <div class="mb-30">
                <table class="table table-hover web-table" style="min-width: 700px;">
                    <thead  style="position: sticky; top: 0; height: 50px; font-size: 20px; vertical-align: middle; ">
                        <tr class="headerColor">
                              <th width="10%" >순서</th>
                              <th width="50%" >제목</th>
                              <th width="20%" >작성자</th>
                              <th width="20%" colspan="2" >작성일</th>
                        </tr>
                      </thead>
                      <tbody > 
                          <tr class="underLine" v-if="currentItems.length > 0" v-for="(item,i) in currentItems" :key="i">
                            <td class="product-name" @click="goQnaDetail(item.GEONUM)">
                              <span>{{ item.RANK }}</span>
                            </td>
                            <td class="product-name" style="text-align: left;" @click="goQnaDetail(item.GEONUM)">
                              <span v-html="item.TITLE"></span>
                              &nbsp;<i v-if="item.REF_SEQ > 0" class="fas fa-comment-dots"></i>
                              <!--
                              <br>
                              <div style="display: flex; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; align-items: center;">
                                  <i class="fas fa-arrow-right"></i>&nbsp;&nbsp;
                                  <span v-html="item.MEMO" style="color: #848b8a;"></span>
                              </div>
                              -->
                            </td>
                            <td class="product-name" @click="goQnaDetail(item.GEONUM)">
                                <!--<span>{{ item.WS_EDTUSER ?? item.WS_NEWUSER }}</span>-->
                                <span>{{ nameS }}</span>
                            </td>
                            <td class="product-name" @click="goQnaDetail(item.GEONUM)">
                                <span>{{ item?.WS_EDTDATE ?? item?.WS_NEWDATE }}</span>
                            </td>
                            <td class="product-remove" @click="goDelQnaList(item)">
                                <a href="#"><i class="fa fa-times"></i></a>
                            </td>
                          </tr>
                          <tr class="underLine" v-else><td colspan="6">등록된 문의가 없습니다.</td></tr>
                      </tbody>
                </table>
                <!--모바일용-->
                <div class="mobile-card">
                    <div v-if="currentItems.length > 0" v-for="(item, i) in currentItems" :key="i" @click="goQnaDetail(item.GEONUM)" class="mobile-card-border">
                        <div><strong>{{ item.TITLE }}</strong></div>
                        <div>작성자: {{ item.WS_EDTUSER }}</div>
                        <div>작성일자: {{ item.WS_EDTDATE }}</div>
                    </div>
                    <div v-if="currentItems.length === 0" style="text-align: center;">
                        <span>등록된 문의가 없습니다.</span>
                    </div>
                </div>
              </div> 
              <!--페이징-->
              <div v-if="totalPages > 1" class="mb-20" style="display: flex; justify-content: center;">
                <button class="pageBtn" @click="prevGroup()" v-if="showPrevButton">이전</button>
                <button class="pageBtn" v-for="page in pages" :key="page" @click="setPage(page)" :class="{ pageActive: currentPage === page }">
                  <span>
                    {{ page }}
                  </span>
                </button>
                <button class="pageBtn" @click="nextGroup()" v-if="showNextButton">다음</button>
              </div>
        </div>
      </div>
    </div>
  </div>
</template> 
<style scoped>
@media (max-width: 768px) {  /* 예를 들어, 화면 크기가 768px 이하일 때 적용. 원하는 크기로 조절 가능 */
    .nav {
        flex-direction: column;
    }

    .checkout-form-list {
        width: 100%;  /* 컨테이너 전체 너비를 사용하도록 설정 */
        margin-bottom: 10px;  /* 아이템들 사이에 간격 추가 */
    }
}
</style>

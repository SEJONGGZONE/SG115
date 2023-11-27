<script setup>
import {
  ref,
  computed,
  reactive,
  onMounted,
  defineComponent,
  nextTick,
} from "vue";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import { productApi } from "@/api";
import {
  startLoadingBar,
  removeLoadingBar,
  addHoverClassToTr1,
  removeHoverClassFromTr1,
  doSort,
  showToast,
  addCursorClassToTr,
  removeCursorClassFromTr,
  excelDownload,
  startProgressbar,
  removeProgressbar,
  setDateFormat,
} from "@/common/utils.ts";
import { ko } from "date-fns/locale";
import Datepicker from "vue3-datepicker";

import { useAlert } from "@/composables/showAlert";
const { showAlert, showAlertSuccess } = useAlert();
//사용자 및 페이지 정보
const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userClcode = userInfo.CLCODE;
const userId = userInfo.ID;
const pageNumber = ref(1);
const pageSize = ref(10);
const pageNumberDR = ref(1);
const pageSizeDR = ref(16);
const pageNumberDL = ref(1);
const pageSizeDL = ref(16);

//대표이미지
const selectQrUrl =
  "http://sjwas.gzonesoft.co.kr:27002/api/GzoneUtilController/getQrImage?dataString=http://sjwas.gzonesoft.co.kr:27004/qr_image/index.html?itcode=";
let selectImg = ref("");

//상품목록 검색조건 중 카데고리
const useYn = ref("Y");
const itsCode1 = ref("01");
const itsCode2 = ref("01");

const catergory0data = [
  { CODE: "Y", NAME: "사용" },
  { CODE: "N", NAME: "미사용" },
];
const catergory1data = ref([]);
const catergory2data = ref([]);

//검색어
let searchKeyword = "";

//죄측 메인 그리드에서 선택한 row 정보
let rowTargetObj = "";
let selectRowData = ref([]);

//하단(D) 우측(R) 그리드에서 선택한 row 정보(= "제거"버튼 클릭 시 제거 될 내용)
let rowTargetObjDR = "";
let selectRowDataDR = ref([]);

//하단(D) 좌측(L) 그리드에서 선택한 row 정보(= 우측 상단 그리드에 추가될 내용)
let rowTargetObjDL = "";
let selectRowDataDL = ref([]);

//grid 체크박스 정보
const isAllDL = ref({});
const isAllDR = ref({});

let isDangerMsg = ref(false); //추가, 제거를 통해서 하단 오른쪽 상품리스트 변경이 있을경우 경고문구 보여줌

//게시기간
let koreanLocale = {
  lang: "ko",
  daysOfWeek: ["일", "월", "화", "수", "목", "금", "토"],
  monthNames: [
    "1월",
    "2월",
    "3월",
    "4월",
    "5월",
    "6월",
    "7월",
    "8월",
    "9월",
    "10월",
    "11월",
    "12월",
  ],
  today: "오늘",
  clear: "초기화",
  cancel: "취소",
  ok: "확인",
  select: "날짜 선택",
  prevMonth: "이전달",
  nextMonth: "다음달",
  datePickerTitle: "날짜 선택",
  year: "년",
  month: "월",
  placeholder: "날짜를 선택하세요",
  rangeSeparator: " - ",
  weekAbbreviation: "주",
  rangeHeaderText: "날짜 범위 선택",
};

const table = reactive({
  //좌측 메인 테이블 정보
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "NO",
      field: "RANK",
      width: "10%",
      sortable: true,
    },
    {
      label: "기획명",
      field: "TITLE",
      width: "80%",
      sortable: true,
      isKey: true,
    },
    {
      label: "게시상태",
      field: "SCHEDULE_USE_YN",
      width: "10%",
      sortable: true,
      isKey: true,
    },
    // {
    // label: "게시기간",
    // field: "",
    // width: "10%",
    // sortable: false,
    // isKey: true,
    // },
    // {
    // label: "대표이미지",
    // field: "IMG_URL",
    // width: "10%",
    // sortable: false,
    // isKey: true,
    // },
    {
      label: "비고/설명",
      field: "BIGO",
      width: "25%",
      sortable: false,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "RANK",
    sort: "asc",
  },
  isShowMoreBtn: false,
});
const tableDR = reactive({
  //하단(D) 오른쪽(R) 상품목록 테이블 정보
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "",
      field: "CHECK",
      width: "3%",
      isKey: true,
    },
    {
      label: "상품명",
      field: "ITNAME",
      width: "14%",
      sortable: true,
      isKey: true,
    },
    {
      label: "제조사",
      field: "ITMAKER",
      width: "8%",
      sortable: true,
      isKey: true,
    },
    {
      label: "규격",
      field: "ITSTAN",
      width: "5%",
    },
    // {
    // label: "대표이미지",
    // field: "IMAGE",
    // width: "8%",
    // },
    {
      label: "매입가",
      field: "ITEA_IPDAN",
      width: "6%",
      sortable: true,
      isKey: true,
    },
    {
      label: "판매가",
      field: "ITEASDAN",
      width: "6%",
      sortable: true,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "ITNAME",
    sort: "asc",
  },
  isShowMoreBtn: false,
});
const tableDL = reactive({
  //하단(D) 왼쪽(L) 상품목록 테이블 정보
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "",
      field: "CHECK",
      width: "3%",
      isKey: true,
    },
    {
      label: "상품명",
      field: "ITNAME",
      width: "14%",
      sortable: true,
      isKey: true,
    },
    {
      label: "제조사",
      field: "ITMAKER",
      width: "8%",
      sortable: true,
      isKey: true,
    },
    {
      label: "규격",
      field: "ITSTAN",
      width: "5%",
    },
    // {
    // label: "대표이미지",
    // field: "IMAGE",
    // width: "8%",
    // },
    {
      label: "매입가",
      field: "ITEA_IPDAN",
      width: "6%",
      sortable: true,
      isKey: true,
    },
    {
      label: "판매가",
      field: "ITEASDAN",
      width: "6%",
      sortable: true,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "ITNAME",
    sort: "asc",
  },
  isShowMoreBtn: false,
});

/******************************************************************************* onMounted */
onMounted(() => {
  pageNumber.value = 1;
  pageNumberDR.value = 1;
  pageNumberDL.value = 1;
  doSearch();
  setCategory1();
  setCategory2();
  nextTick(() => {
    setTimeout(() => {
      selectFirstRow();
    }, 500);
  });
});

/******************************************************************************* 버튼 및 액션 이벤트 start */

const selectFirstRow = () => {
  const firstRow = document.querySelector(".selected");
  if (firstRow) {
    firstRow.click();
  }
};
const tableLoadingFinish = (elements) => {
  //Table search finished event
  table.isLoading = false;
};
const handleRowClick = async (rowData, event) => {
  //좌측 메인 그리드 row click event

  if (rowTargetObj) {
    rowTargetObj.classList.remove("table-good");
  }
  selectRowData.value = JSON.parse(JSON.stringify(rowData));
  tableDR.rows = [];
  tableDL.rows = [];

  await doSearchDR();
  await doSearchDL();

  rowTargetObj = event.target.closest("tr");
  rowTargetObj.classList.add("table-good");
};
const handleRowClickDR = (obj, event) => {
  //하단(D) 오른쪽(R) 그리드 row click event

  obj["CHECK"] = !obj["CHECK"];

  /*if(rowTargetObjDR){
			rowTargetObjDR.classList.remove("table-good");
		}

		selectRowDataDR.value = JSON.parse(JSON.stringify(rowData));

		rowTargetObjDR = (event.target).closest('tr')
		rowTargetObjDR.classList.add("table-good");*/
};
const handleRowClickDL = (obj, event) => {
  //하단(D) 왼쪽(L) 그리드 row click event

  obj["CHECK"] = !obj["CHECK"];

  /*if(rowTargetObjDL){
			rowTargetObjDL.classList.remove("table-good");
		}
		
		selectRowDataDL.value = JSON.parse(JSON.stringify(rowData));

		rowTargetObjDL = (event.target).closest('tr')
		rowTargetObjDL.classList.add("table-good");*/
};
const getImageUrl = (item) => {
  if (item?.IMG_ADM_UPLOAD) {
    return item?.IMG_ADM_UPLOAD;
  } else if (item.FRONT_IMAGE) {
    return item.FRONT_IMAGE;
  } else {
    return "/assets/img/logo/noimg.png";
  }
};
const optioCodeChg0 = () => {
  tableDL.rows = [];
  useYn.value = event.target.value;
  pageNumberDL.value = 1;
  doSearchDL();
};
const optioCodeChg1 = () => {
  //카데고리1 변경

  tableDL.rows = [];
  catergory2data.value = [];
  itsCode2.value = "01";
  itsCode1.value = event.target.value;
  setCategory2();
  pageNumberDL.value = 1;
  doSearchDL();
};
const optioCodeChg2 = () => {
  //카테고리2 변경
  tableDL.rows = [];
  itsCode2.value = event.target.value;
  pageNumberDL.value = 1;
  doSearchDL();
};
const searchBtn = (keyword) => {
  //검색
  tableDL.rows = [];
  searchKeyword = keyword;
  pageNumberDL.value = 1;
  doSearchDL();
};

const isSelectCheckDL = computed(
  () => !!tableDL.rows.filter((item) => item.CHECK === true).length
);
const addBtn = () => {
  //추가

  if (!isSelectCheckDL.value) {
    showAlert("추가할 상품을 선택해주세요.");
    return;
  }

  selectRowDataDL = tableDL.rows
    .filter((item) => item.CHECK === true)
    .map((row) => ({ ...row, RANK: "신규" }));
  const existingITCODEs = tableDR.rows.map((item) => item.ITCODE);
  selectRowDataDL.forEach((row) => {
    if (!existingITCODEs.includes(row.ITCODE)) {
      tableDR.rows.push(row);
    }
  });
  tableDL.rows = tableDL.rows.filter((item) => !item.CHECK);
  isDangerMsg.value = true;
};

const isSelectCheckDR = computed(
  () => !!tableDR.rows.filter((item) => item.CHECK === true).length
);
const removeBtn = () => {
  //제거

  if (!isSelectCheckDR.value) {
    showAlert("상품을 선택해 주세요.");
    return;
  }

  let tempChkRows = tableDR.rows.filter((item) => item.CHECK);

  tableDL.rows.unshift(
    ...tempChkRows.filter((item) => {
      let categoryCode = item.CATEGORY_CODE;
      let tempCode1 = categoryCode.split("-")[0];
      let tempCode2 = categoryCode.split("-")[1];

      return tempCode1 === itsCode1.value && itsCode2.value === tempCode2;
    })
  );
  //여기 봐줘
  tableDR.rows = tableDR.rows.filter((item) => !item.CHECK);

  isDangerMsg.value = true;

  /*selectRowDataDR = tableDR.rows.filter(item => item.CHECK === true);
		const updatedRows = tableDR.rows.filter(item => !selectRowDataDR.includes(item));
  		tableDR.rows = updatedRows;
		isDangerMsg.value = true*/
};
const newBtn = () => {
  //저장 후 체크박스 해제
  isAllDR.value = false;
  tableDR.rows.forEach((row) => {
    row.CHECK = false;
  });
  isAllDL.value = false;
  tableDL.rows.forEach((row) => {
    row.CHECK = false;
  });
};
const saveBtn = () => {
  //저장
  doSave();
};
const searchMoteBtn = () => {
  //좌측 메인 테이블 더보기 버튼
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};
const searchMoteBtnDR = () => {
  //하단 오른쪽 테이블 더보기
  pageNumberDR.value = pageNumberDR.value + 1;
  doSearchDR();
};
const searchMoteBtnDL = () => {
  //하단 왼쪽 테이블 더보기
  pageNumberDL.value = pageNumberDL.value + 1;
  doSearchDL();
};
const chnageAllChkDR = (e) => {
  //하단왼쪽(DR) 헤더부 전체선택
  tableDR.rows.map((item) => (item.CHECK = isAllDR.value));
};
const chnageAllChkDL = (e) => {
  //하단오른쪽(DL) 헤더부 전체선택
  tableDL.rows.map((item) => (item.CHECK = isAllDL.value));
};

/******************************************************************************* api 호출 start */
const setCategory1 = async () => {
  //카데고리1 조회
  const param = {
    code: "",
    inputUser: userId,
    name: "",
  };
  let data;
  try {
    data = await productApi.category(param);
    catergory1data.value = data.RecordSet;
    if (data.RecordCount > 0) {
      for (let i = 0; i < data.RecordCount; i++) {
        //코드 공백 제거(기존 패키지의설계가 VARCHAR를 쓰지않고 CHAR타입이라서 DB의 문제)
        catergory1data.value[i].CODE = data.RecordSet[i].CODE.trim();
      }
    }
  } catch (error) {
    console.error(error);
  }
};
const setCategory2 = async () => {
  //카데고리2 조회
  const param = {
    code: itsCode1.value,
    inputUser: userId,
    name: "",
  };
  let data;
  try {
    data = await productApi.category(param);
    catergory2data.value = data.RecordSet;
    if (data.RecordCount > 0) {
      itsCode2.value = catergory2data.value[0].CODE;
    }
  } catch (error) {
    console.error(error);
  }
};
const doSearch = async () => {
  //상단 메인 테이블 조회
  if (table.isLoading) return;
  table.isLoading = true;
  startLoadingBar();
  const param = {
    geonum: "",
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
    inputUser: userId,
  };
  let data;
  try {
    data = await productApi.eventManagementList(param);
    table.isLoading = false;
    if (data.RecordCount > 0) {
      table.rows.push(...data.RecordSet);
      if (data.RecordCount === pageSize.value) {
        table.isShowMoreBtn = true;
      }
    } else {
      table.isShowMoreBtn = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    table.isLoading = false;
    /*var productDiv = document.getElementById('productDiv');
			setTimeout(()=>{
			productDiv.scrollTop = productDiv.scrollHeight;
			},100)*/
  }
};
const doSearchDR = async () => {
  //하단 오른쪽 상품목록 테이블 조회
  const param = {
    geonum: selectRowData?.value?.GEONUM,
    pageSize: 300,
    pageNumber: pageNumberDR.value,
    inputUser: userId,
  };
  let data;
  try {
    data = await productApi.eventProductList(param);
    if (data.RecordCount > 0) {
      tableDR.rows.push(...data.RecordSet);
      if (data.RecordCount === pageSizeDR.value) {
        tableDR.isShowMoreBtn = true;
      }
    } else {
      tableDR.isShowMoreBtn = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    tableDR.isLoading = false;
    /*var productDiv = document.getElementById('productDivDR');
			setTimeout(()=>{
			productDiv.scrollTop = productDiv.scrollHeight;
			},100)*/
  }
};
const doSearchDL = async () => {
  //하단 왼쪽 상품목록 테이블 조회
  tableDL.isShowMoreBtn = false;
  const param = {
    useYn: useYn.value,
    itsCode1: itsCode1.value,
    itsCode2: itsCode2.value,
    pageSize: 300,
    searchKeyword: searchKeyword ?? "",
    clcode: userClcode,
    pageNumber: pageNumberDL.value,
    inputUser: userId,
    itCode: "",
  };
  let data;
  try {
    data = await productApi.productManagement(param);
    if (data.RecordCount > 0) {
      let tempTableDL = data.RecordSet;
      tableDL.rows = tempTableDL.filter((bItem) => {
        return !tableDR.rows.some((aItem) => aItem.ITCODE === bItem.ITCODE);
      });
      if (data.RecordCount === pageSizeDL.value) {
        tableDL.isShowMoreBtn = true;
      }
    } else {
      tableDL.isShowMoreBtn = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    tableDL.isLoading = false;
    /*var productDiv = document.getElementById('productDivDL');
			setTimeout(()=>{
			productDiv.scrollTop = productDiv.scrollHeight;
			},100)*/
  }
};
const doSave = async () => {
  //저장

  let itemlist = "";
  if (tableDR.rows.length > 0) {
    for (let i = 0; i < tableDR.rows.length; i++) {
      itemlist += `${tableDR.rows[i].ITCODE},`;
    }
  }
  const param = {
    geonum: selectRowData?.value?.GEONUM ?? "",
    type: selectRowData?.value?.TYPE ?? "",
    itemList: itemlist ?? "",
    inputUser: userId,
  };

  let data;
  try {
    data = await productApi.eventProductSave(param);
    if (data.ResultCode === "00") {
      showAlertSuccess("저장되었습니다.");
      table.rows = [];
      doSearch();
      tableDR.rows = [];
      doSearchDR();
      isDangerMsg.value = false;
    }
  } catch (error) {
    console.error(error);
  }
  newBtn();
};
</script>
<template>
  <div
    class="section section__management"
    style="border: 0px solid red; padding-bottom: 0px"
  >
    <div class="group__title">
      <h2>기획상품관리</h2>
    </div>
    <div class="group__title" style="border: 0px solid red"></div>
    <div class="group__contents margin10" style="border: 0px solid red">
      <div class="part__data_list" style="">
        <div class="item__scroll" id="productDiv">
          <div class="unit__scroll">
            <table>
              <thead>
                <tr>
                  <th
                    v-for="(col, index) in table.columns"
                    :key="index"
                    :style="
                      Object.assign(
                        { width: col.width ? col.width : 'auto' },
                        col.headerStyles
                      )
                    "
                  >
                    <div class="unit_bundle">
                      <template v-if="col.field === 'CHECK'">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          @change="chnageAllChk"
                          v-model="isAll"
                        />
                      </template>
                      <template v-else>
                        {{ col.label }}
                        <div class="unit__buttons" v-if="col.sortable">
                          <button
                            :class="
                              table.sortable.order === col.field &&
                              table.sortable.sort === 'asc'
                                ? 'active'
                                : ''
                            "
                            @click.prevent="
                              col.sortable
                                ? doSort(col.field, table.sortable, table.rows)
                                : false
                            "
                          >
                            <i class="fa-solid fa-angle-up"></i>
                          </button>
                          <button
                            :class="
                              table.sortable.order === col.field &&
                              table.sortable.sort === 'desc'
                                ? 'active'
                                : ''
                            "
                            @click.prevent="
                              col.sortable
                                ? doSort(col.field, table.sortable, table.rows)
                                : false
                            "
                          >
                            <i class="fa-solid fa-angle-down"></i>
                          </button>
                        </div>
                      </template>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' : ''"
                  class="pointer"
                  style="text-decoration: none"
                  v-if="table.rows.length > 0"
                  v-for="(obj, index) in table.rows"
                  :key="index"
                  @mouseenter="addHoverClassToTr"
                  @mouseleave="removeHoverClassFromTr"
                  @click="handleRowClick(obj, $event)"
                >
                  <td>
                    <div>
                      <span>{{ obj.RANK }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span style="text-align: left; width: auto"
                        >{{ obj.TITLE }} ({{ obj.CNT_EVENTITEM }} 건)</span
                      >
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{
                        obj.SCHEDULE_USE_YN === "Y" ? "사용함" : "사용안함"
                      }}</span>
                    </div>
                  </td>
                  <!-- <td >
										<div><span>{{ obj.DATE1 }} ~ {{ obj.DATE2 }}</span></div>
									</td>
									<td  >
									<div class="h-50px"><img alt="" class="mw-100 mh-100" :src="obj.IMG_URL ? obj.IMG_URL : '/assets/img/logo/noimg.png'"/></div>
									</td> -->
                  <td>
                    <div>
                      <span class="ellipsis">{{ obj.BIGO }}</span>
                    </div>
                  </td>
                </tr>
                <tr v-else>
                  <td :colspan="table.columns.length">
                    <span
                      >상품관리 > 기획관리 화면에서 기획을 등록해주세요</span
                    >
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="item__buttons" v-if="table.isShowMoreBtn">
          <button @click="searchMoteBtn">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
        </div>
      </div>
    </div>
    <div class="group__contents" style="flex: 2.2">
      <div
        class="part__data_detail"
        style="overflow: auto; height: auto; flex: 1"
      >
        <div class="row mb-1" style="text-align: center; height: auto">
          <!--하단_왼쪽-->
          <div class="col-lg-5" style="width: 45%; padding: 0px">
            <div class="item__contents mb-10" style="height: auto">
              <!-- <div class="card-header h6 bg-none" style="text-align: left">
								<i class="fa fa-angles-left fa-fw text-dark text-opacity-50 me-1"></i><span style="font-size: 22px;">상품찾기</span>
							</div> -->
              <!-- 카테고리#1,2 -->
              <div class="input-group input-group-lg mb-1">
                <div class="col-xl-12 col-lg-12" style="display: flex">
                  <!-- <select class="form-select form-select-lg" @change="optioCodeChg0" :v-model="catergory0data">
										<option v-for="item in catergory0data" :value="item.CODE" :key="item.CODE"> {{ item.NAME }} </option>
									</select> -->
                  <select
                    class="form-select form-select-lg"
                    @change="optioCodeChg1"
                    :v-model="catergory1data"
                  >
                    <option
                      v-for="item in catergory1data"
                      :value="item.CODE"
                      :key="item.CODE"
                    >
                      {{ item.NAME }}
                    </option>
                  </select>
                  <select
                    class="form-select form-select-lg"
                    @change="optioCodeChg2"
                    :v-model="catergory2data"
                  >
                    <option
                      v-for="item in catergory2data"
                      :value="item.CODE"
                      :key="item.CODE"
                    >
                      {{ item.NAME }}
                    </option>
                  </select>
                </div>
              </div>
              <!-- 키워드 -->
              <div class="input-group input-group-lg mb-3">
                <input
                  type="text"
                  class="form-control input-white"
                  placeholder="상품명, 제조사를 입력하세요"
                  v-model="keyword"
                  v-on:keyup.enter="searchBtn(keyword)"
                />
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="searchBtn(keyword)"
                >
                  <i class="fa fa-search fa-fw"></i>검색
                </button>
              </div>
              <!-- 결과리스트 -->
              <div
                class="group__contents"
                style="margin-top: -10px; height: auto"
              >
                <div class="part__data_list" style="height: auto">
                  <div class="item__scroll" id="productDivDL">
                    <div class="unit__scroll">
                      <table>
                        <thead>
                          <tr>
                            <th
                              v-for="(col, index) in tableDL.columns"
                              :key="index"
                              :style="
                                Object.assign(
                                  { width: col.width ? col.width : 'auto' },
                                  col.headerStyles
                                )
                              "
                            >
                              <div class="unit_bundle">
                                <template v-if="col.field === 'CHECK'">
                                  <input
                                    class="form-check-input"
                                    type="checkbox"
                                    @change="chnageAllChkDL"
                                    v-model="isAllDL"
                                  />
                                </template>
                                <template v-else>
                                  {{ col.label }}
                                  <div
                                    class="unit__buttons"
                                    v-if="col.sortable"
                                  >
                                    <button
                                      :class="
                                        tableDL.sortable.order === col.field &&
                                        tableDL.sortable.sort === 'asc'
                                          ? 'active'
                                          : ''
                                      "
                                      @click.prevent="
                                        col.sortable
                                          ? doSort(
                                              col.field,
                                              tableDL.sortable,
                                              tableDL.rows
                                            )
                                          : false
                                      "
                                    >
                                      <i class="fa-solid fa-angle-up"></i>
                                    </button>
                                    <button
                                      :class="
                                        tableDL.sortable.order === col.field &&
                                        tableDL.sortable.sort === 'desc'
                                          ? 'active'
                                          : ''
                                      "
                                      @click.prevent="
                                        col.sortable
                                          ? doSort(
                                              col.field,
                                              tableDL.sortable,
                                              tableDL.rows
                                            )
                                          : false
                                      "
                                    >
                                      <i class="fa-solid fa-angle-down"></i>
                                    </button>
                                  </div>
                                </template>
                              </div>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            :class="
                              selectRowDataDL?.RANK === obj.RANK ? 'active' : ''
                            "
                            v-if="tableDL.rows.length > 0"
                            v-for="(obj, index) in tableDL.rows"
                            :key="index"
                            @click="handleRowClickDL(obj, $event)"
                          >
                            <td>
                              <div>
                                <input
                                  class="form-check-input"
                                  type="checkbox"
                                  v-model="obj['CHECK']"
                                />
                              </div>
                            </td>
                            <td style="text-align: left">
                              <div v-html="obj.ITNAME"></div>
                            </td>
                            <td style="text-align: left">
                              <div v-html="obj.ITMAKER"></div>
                            </td>
                            <td><div v-html="obj.ITSTAN"></div></td>
                            <!-- <td >
													<div class="h-50px" style="padding: 5px 0px;"><img alt="" class="mw-100 mh-100" :src="getImageUrl(obj)"/></div> 
												</td> -->
                            <td style="text-align: right">
                              <div v-html="obj.ITEA_IPDAN"></div>
                            </td>
                            <td style="text-align: right">
                              <div v-html="obj.ITEASDAN"></div>
                            </td>
                          </tr>
                          <tr v-else>
                            <td :colspan="tableDR.columns.length">
                              <div>조회된 데이터가 없습니다.</div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <button v-if="tableDL.isShowMoreBtn" type="button" class="btn btn-primary btn-lg" style="text-align: center" @click="searchMoteBtnDL">더보기</button> -->
            </div>
          </div>

          <!--추가, 제거 버튼-->
          <div
            class="col-lg-2 d-flex flex-column align-items-center justify-content-top"
            style="width: 10%; height: auto; gap: 10px; padding-top: 110px"
          >
            <button
              type="button"
              class="btn btn-lg btn-primary mb-1"
              @click="addBtn()"
              style="
                height: 30px;
                padding-top: 2px;
                font-size: 17px;
                text-align: left;
                padding-left: 10px;
              "
            >
              <i class="fa fa-angle-right fa-fw"></i>추가
            </button>
            <button
              type="button"
              class="btn btn-lg btn-primary mb-1"
              @click="removeBtn()"
              style="
                height: 30px;
                padding-top: 2px;
                font-size: 17px;
                text-align: left;
                padding-left: 10px;
              "
            >
              <i class="fa fa-angle-left fa-fw"></i>제거
            </button>
          </div>

          <!--하단_오른쪽-->
          <div
            class="col-lg-5"
            style="width: 45%; padding: 0px; border: 0px solid red"
          >
            <div
              class="mb-10"
              style="
                border: 0px solid red;
                height: auto;
                padding: 0px;
                margin: 0px;
              "
            >
              <!-- 타이틀/버튼 -->
              <div class="bg-none col-xl-12 col-lg-12" style="text-align: left">
                <span>&nbsp;</span>
                <div style="margin-top: -30px; margin-right: 0px">
                  <button
                    type="button"
                    class="btn btn-lg btn-primary me-1 mb-1"
                    @click="saveBtn()"
                    style="height: 30px; padding-top: 2px; font-size: 17px"
                  >
                    <i class="fa fa-circle-check fa-fw"></i> 저장
                  </button>
                  <span
                    style="font-size: 17px; text-align: end; padding-left: 10px"
                    ><B>'{{ selectRowData.TITLE }}'</B> 등록상품 ({{
                      tableDR.rows.length
                    }}건)</span
                  >
                </div>
              </div>
              <!-- 결과리스트 -->
              <div
                class="table-container-610 mb-3 table-border"
                style="
                  text-align: center;
                  overflow: auto;
                  height: auto;
                  padding: 0px;
                  margin: 0px;
                  border: 0px solid red;
                "
              >
                <div
                  class="group__contents"
                  style="height: auto; border: 0px solid red"
                >
                  <div class="part__data_list" style="height: auto">
                    <div class="item__scroll" id="productDivDR">
                      <div class="unit__scroll">
                        <table>
                          <thead>
                            <tr>
                              <th
                                v-for="(col, index) in tableDR.columns"
                                :key="index"
                                :style="
                                  Object.assign(
                                    { width: col.width ? col.width : 'auto' },
                                    col.headerStyles
                                  )
                                "
                              >
                                <div class="unit_bundle">
                                  <template v-if="col.field === 'CHECK'">
                                    <input
                                      class="form-check-input"
                                      type="checkbox"
                                      @change="chnageAllChkDR"
                                      v-model="isAllDR"
                                    />
                                  </template>
                                  <template v-else>
                                    {{ col.label }}
                                    <div
                                      class="unit__buttons"
                                      v-if="col.sortable"
                                    >
                                      <button
                                        :class="
                                          tableDR.sortable.order ===
                                            col.field &&
                                          tableDR.sortable.sort === 'asc'
                                            ? 'active'
                                            : ''
                                        "
                                        @click.prevent="
                                          col.sortable
                                            ? doSort(
                                                col.field,
                                                tableDR.sortable,
                                                tableDR.rows
                                              )
                                            : false
                                        "
                                      >
                                        <i class="fa-solid fa-angle-up"></i>
                                      </button>
                                      <button
                                        :class="
                                          tableDR.sortable.order ===
                                            col.field &&
                                          tableDR.sortable.sort === 'desc'
                                            ? 'active'
                                            : ''
                                        "
                                        @click.prevent="
                                          col.sortable
                                            ? doSort(
                                                col.field,
                                                tableDR.sortable,
                                                tableDR.rows
                                              )
                                            : false
                                        "
                                      >
                                        <i class="fa-solid fa-angle-down"></i>
                                      </button>
                                    </div>
                                  </template>
                                </div>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              :class="
                                selectRowDataDR?.RANK === obj.RANK
                                  ? 'active'
                                  : ''
                              "
                              v-if="tableDR.rows.length > 0"
                              v-for="(obj, index) in tableDR.rows"
                              :key="index"
                              @click="handleRowClickDR(obj, $event)"
                            >
                              <td>
                                <div>
                                  <input
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="obj['CHECK']"
                                  />
                                </div>
                              </td>
                              <td style="text-align: left">
                                <div v-html="obj.ITNAME"></div>
                              </td>
                              <td style="text-align: left">
                                <div v-html="obj.ITMAKER"></div>
                              </td>
                              <td><div v-html="obj.ITSTAN"></div></td>
                              <!-- <td >
														<div class="h-50px" style="padding: 5px 0px;"><img alt="" class="mw-100 mh-100" :src="getImageUrl(obj)"/></div> 
													</td> -->
                              <td style="text-align: right">
                                <div v-html="obj.ITEA_IPDAN"></div>
                              </td>
                              <td style="text-align: right">
                                <div v-html="obj.ITEASDAN"></div>
                              </td>
                            </tr>
                            <tr v-else>
                              <td :colspan="tableDR.columns.length">
                                <div>조회된 데이터가 없습니다.</div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <button v-if="tableDR.isShowMoreBtn" type="button" class="btn btn-primary btn-lg" style="text-align: center" @click="searchMoteBtnDR">더보기</button> -->
            </div>

            <!-- <div v-if="isDangerMsg" class="text-left mb-1" style="text-align: left">
									* <span class="text-danger" style="font-size:22px;">"변경사항 저장"</span> 버튼을 클릭하여 변경사항을 저장 해주세요
							</div> -->
          </div>
          <!-- <div style="text-align: end;"> 
						<button type="button" class="btn btn-lg btn-primary me-1 mb-1"  @click="saveBtn()"><i class="fa fa-circle-check fa-fw"></i>변경사항 저장</button> 
					</div> -->
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.table-container-300 {
  height: 300px; /* 원하는 높이 값으로 설정 */
  overflow-y: scroll;
  border: 1px solid gainsboro;
}
.table-container-500 {
  height: 500px; /* 원하는 높이 값으로 설정 */
  overflow-y: scroll;
}
.table-container-610 {
  height: 610px; /* 원하는 높이 값으로 설정 */
  overflow-y: scroll;
}
.ellipsis {
  width: 200px;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  display: block;
}

.table-border {
  border-collapse: collapse; /* 테두리 선이 겹치지 않도록 설정합니다. */
  border: 0px solid gainsboro; /* 테두리 선의 스타일과 색상을 지정합니다. */
}
.margin10 {
  margin-bottom: 10px;
}
</style>
<script setup>
import {
  ref,
  computed,
  reactive,
  onMounted,
  defineComponent,
  getCurrentInstance,
} from "vue";
import highlightjs from "@/components/plugins/Highlightjs.vue";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import { operateApi } from "@/api";
import {
  startLoadingBar,
  removeLoadingBar,
  addHoverClassToTr,
  removeHoverClassFromTr,
  doSort,
  showToast,
  setDateFormat,
} from "@/common/utils.ts";
import { ko } from "date-fns/locale";
import Datepicker from "vue3-datepicker";
import quillEditor from "@/components/plugins/QuillEditor.vue";

import { useAlert } from "@/composables/showAlert";
const { showAlert, showAlertSuccess } = useAlert();
let searchKeyword = "";
let selectRowData = ref([]);
let rowTargetObj = "";
const pageNumber = ref(1);
const pageSize = ref(10);
const quillEditorRef = ref(null);

const { ctx } = getCurrentInstance();
const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userId = userInfo.ID;

onMounted(() => {
  pageNumber.value = 1;
  doSearch();
});
const getText = (e) => {
  console.log(e);
};

const table = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "NO",
      field: "",
      width: "3%",
    },
    {
      label: "게시기간",
      field: "DATE1",
      width: "25%",
      sortable: true,
    },
    {
      label: "제목",
      field: "TITLE",
      width: "5%",
      sortable: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "NO",
    sort: "asc",
  },
  isShowMoreBtn: false,
});
/* Table search event*/
const doSearch = async () => {
  if (table.isLoading) return;
  table.isLoading = true;
  startLoadingBar();
  const param = {
    gno: "",
    clcode: "",
    inputUser: userId,
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
  };

  let data;
  try {
    const dataObj = await operateApi.operateManagement(param);
    data = dataObj.data;
    table.isLoading = false;
    quillEditorRef.value.setContent("");
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

const searchBtn = (keyword) => {
  table.rows = [];
  searchKeyword = keyword;
  pageNumber.value = 1;
  doSearch();
};

/*Table search finished event*/
const tableLoadingFinish = (elements) => {
  table.isLoading = false;
};

const selectDate1 = ref(null) // 달력 컴포넌트
const selectDate2 = ref(null) // 달력 컴포넌트

/* row click event */
const handleRowClick = (rowData, event) => {
  selectRowData.value = JSON.parse(JSON.stringify(rowData));
  console.log(selectRowData.value);
  quillEditorRef.value.setContent(selectRowData.value.MSG);
  console.log("1. ", selectRowData.value.DATE1);
  selectRowData.value.DATE1 = selectRowData.value.DATE1.trim()
    ? new Date(selectRowData.value.DATE1)
    : "";
  selectRowData.value.DATE2 = selectRowData.value.DATE2.trim()
    ? new Date(selectRowData.value.DATE2)
    : "";
  
  // 달력이동
  selectDate1.value.move(selectRowData.value.DATE1);
  selectDate2.value.move(selectRowData.value.DATE2);

  console.log("2. ", selectRowData.value.DATE1);
};

const newBtn = () => {
  //신규
  selectRowData.value = [];
  quillEditorRef.value.setContent("");
};

const chkBtn = () => {
  if (selectRowData.value.DAY_USE_YN === "N") {
    //체크->체크해제
    selectRowData.value.DAY_USE_YN = "Y";
  } else {
    selectRowData.value.DAY_USE_YN = "N";
    selectRowData.value.DATE1 = "";
    selectRowData.value.DATE2 = "";
  }
};

const saveBtn = () => {
  //저장

  selectRowData.value.MSG = quillEditorRef.value.getContent();

  let chk = false;
  let chkId = "";

  if (!selectRowData.value.TITLE) {
    chk = true;
    chkId = `${chkId}제목`;
  }
  if (selectRowData.value.DAY_USE_YN !== "N") {
    //체크해제된 상태->입력항목vali...
    if (!selectRowData.value.DATE1) {
      chk = true;
      chkId = !chkId ? `시작일` : `${chkId}, 시작일`;
    }
    if (!selectRowData.value.DATE2) {
      chk = true;
      chkId = !chkId ? `종료일` : `${chkId}, 종료일`;
    }
  }
  if (!selectRowData.value.MSG) {
    chk = true;
    chkId = !chkId ? `내용` : `${chkId}, 내용`;
  }

  if (chk) {
    showAlert(`입력 항목을 확인해 주세요..<br>(${chkId})`);
    event.preventDefault();
    return;
  }

  doSave();
};
const deleteBtn = async () => {
  //삭제

  const param = {
    gno: selectRowData?.value?.GNO,
    delYn: "Y",
    inputUser: userId,
  };

  let data;
  try {
    const dataObj = await operateApi.operateManagementDelete(param);
    data = dataObj.data;
    if (data.ResultCode === "00") {
      showAlertSuccess("삭제되었습니다.");
      table.rows = [];
      doSearch();
    }
  } catch (error) {
    console.error(error);
  }

  newBtn();
};

const doSave = async () => {
  const param = {
    gno: selectRowData?.value?.GNO ?? "",
    title: selectRowData?.value?.TITLE ?? "",
    memo: selectRowData?.value?.MSG ?? "",
    date1: setDateFormat(selectRowData?.value?.DATE1) ?? "",
    date2: setDateFormat(selectRowData?.value?.DATE2) ?? "",
    dayUseYn: selectRowData?.value?.DAY_USE_YN ?? "Y",
    inputUser: userId,
  };

  let data;
  try {
    const dataObj = await operateApi.operateManagementSave(param);
    data = dataObj.data;
    if (data.ResultCode === "00") {
      showAlertSuccess("저장되었습니다.");
      table.rows = [];
      quillEditorRef.value.setContent("");
      doSearch();
    }
  } catch (error) {
    console.error(error);
  }

  newBtn();
};

const searchMoteBtn = () => {
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};

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


const selectedColor = ref('red');
const attrs = ref([
  {
    key: 'test',
    highlight: true,
    dates: { start: new Date(2023, 11, 1), end: new Date(2023, 11, 19) },
  }
]);


</script>

<template>
  <div class="section section__management" style="gap: 2px">
    <!-- <div class="group__search">
      <div
        class="part__search_box"
        style="
          display: flex;
          align-items: center;
          justify-content: space-between;
        "
      >
        <div class="group__title">
          <h2>공지사항/팝업</h2>
        </div>
        <div style="display: flex">
          <button type="button" @click="newBtn()">
            <i class="fa fa-pen-to-square fa-fw"></i>신규
          </button>
          <button type="button" @click="saveBtn()">
            <i class="fa fa-circle-check fa-fw"></i>저장
          </button>
          <button type="button" @click="deleteBtn()">
            <i class="fa fa-trash-can fa-fw"></i>삭제
          </button>
        </div>
      </div>
    </div> -->
    <div class="group__contents_sungchang">
      <!-- 메인데이타 -->
      <div class="part__data_list left_side" style="flex: unset; height: auto">
        <div class="left_side_detail_title">
          <i class="icon-list"></i>&nbsp;&nbsp;운영관리 - 공지사항/팝업
        </div>
        <!-- 검색어입력/버튼 -->
        <div class="grid_searcharea" style="justify-content: end;">
          <div class="item__buttons">
            <button class="btn_search" @click="newBtn" style="width:5rem">
              <i class="fa fa-pen-to-square fa-fw"></i><span style="margin-left:0px;">신규</span>
            </button>
          </div>
        </div>
        <div class="item__scroll" id="productDiv">
          <div class="unit__scroll">
            <table>
              <thead>
                <tr>
                  <th v-for="(col, index) in table.columns" :key="index">
                    <div class="unit_bundle">
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
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr :class="selectRowData?.RANK === obj.RANK ? 'active' : obj.RANK%2 === 0?'link':''" 
                  v-if="table.rows"
                  v-for="(obj, index) in table.rows"
                  :key="index"
                  @mouseenter="addHoverClassToTr"
                  @mouseleave="removeHoverClassFromTr"
                  @click="handleRowClick(obj, $event)"
                >
                  <td>
                    <div>
                      <span>{{ index + 1 }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                      <span>{{ obj.DATE1 }}~{{ obj.DATE2 }}</span>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span> {{ obj.TITLE }}</span>
                    </div>
                  </td>
                </tr>
                <tr v-else>
                  <td :colspan="table.columns.length">
                    <span>no data</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div id="dragMe" class="resizer_h" @mousedown="mouseDownHandlerForDrag($event)"></div>
      <!-- 상세 -->
      <div class="part__data_detail right_side" style="height:auto; display: table-cell; vertical-align: top;">
        <div class="right_side_detail_title">
          <i class="icon-note"></i>&nbsp;&nbsp;상세정보
          <label style="font-size:0.5rem; font-weight:700; margin-left:0.2rem; color:#FFFFFF"
                  v-if="selectRowData.UPDATE_DATE"> - Update {{ selectRowData.UPDATE_DATE }}</label>
        </div>
        <!-- 검색어입력/버튼 -->
        <div class="grid_searcharea" style="justify-content: end; background-color:#FFFFFF;">
          <div class="item__buttons">
            <button class="btn_search" @click="saveBtn" style="width:5rem">
              <i class="fa fa-save fa-fw"></i><span style="margin-left:0px;">저장</span>
            </button>
            <button class="btn_search" @click="deleteBtn" style="width:5rem">
              <i class="fa fa-trash-can fa-fw"></i><span style="margin-left:0px;">삭제</span>
            </button>
          </div>
        </div>
        <div class="item__scroll">
          <div class="unit__scroll" style="border:0px solid red;">
            <div>
              <div class="item__contents_sungchang" style="background-image:none;">
                  <div style="border: 0px solid red;">
                    <div
                      class="row"
                      style="border: 1px solid #eaeaea; width: 100%">
                      <div>
                        <div>
                          <div>
                            <label class="form-label">제목</label>
                            <input type="text" v-model="selectRowData.TITLE" />
                          </div>
                        </div>
                        <div>
                          <label class="form-label">사용여부</label>
                          <div class="radio_group" style="gap:0 1rem;">
                            <label> <input type="radio" id="radio1-1" value="Y" v-model="selectRowData.DAY_USE_YN" /><span for="radio1-1">ON</span> </label>
                            <label> <input type="radio" id="radio1-2" value="N" v-model="selectRowData.DAY_USE_YN" /><span for="radio1-2">OFF</span> </label>
                          </div>
                        </div>
                        <!-- <div
                              class="d-flex justify-content-end align-items-center mt-2 mb-2"
                            >
                              <input
                                class="form-check-input"
                                type="checkbox"
                                id="flexCheckDefault"
                                v-model="selectRowData.DAY_USE_YN"
                                true-value="N"
                                false-value="Y"
                                @click="chkBtn"
                              />
                              <label
                                class="form-check-label"
                                for="flexCheckDefault"
                                style="margin-left: 5px"
                                >ON/OFF</label
                              >
                            </div> -->
                        <div style="border: 0px solid red; margin-top:1rem; margin-bottom:1rem;">
                          <label class="form-label">게시기간</label>
                          <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap:0 1rem;">
                            <div>
                              <VDatePicker ref="selectDate1" v-model="selectRowData.DATE1"/> 
                            </div>
                            <div style="font-size:1.5rem;">~</div>
                            <div>
                              <VDatePicker ref="selectDate2" v-model="selectRowData.DATE2"/> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <quill-editor theme="snow" ref="quillEditorRef" 
                            style="margin:0.5rem 0.4rem 0rem -0.4rem; border:0px solid red; height:11.5rem; background-color:#FFFFFF;"
                            />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</template>

<!-- CSS 전역 -->
<style>
tr {
  cursor: pointer;
}
/** 캘린더CSS 강제수정, 오늘날짜-배경,글자 */
body .vc-container .vc-day.is-today .vc-day-content {
  background-color: #FFFFFF !important;
  color: #a8a8a8 !important;
}
/** 캘린더CSS 강제수정, 선택된 날짜-배경 */
body .vc-container .vc-highlights .vc-highlight {
  background-color: #ba31ff !important;
}
/** 캘린더CSS 강제수정, 선택된 날짜-글자 */
body .vc-container .vc-highlights + .vc-day-content {
  color: #FFFFFF !important;
}

</style>

<!-- CSS 영역한정(좌우조절용) -->
<style scoped>
.left_side {
  width: 50%;
  /* 중앙 정렬 */
  display: flex;
  /*justify-content: center;*/
  min-width: 25%;
}
.right_side {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 35%;
  background-image: url('/assets/img/sungchang/logo_background_001.png');
}
</style>
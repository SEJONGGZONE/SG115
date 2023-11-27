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
      label: "제목",
      field: "TITLE",
      width: "10%",
      sortable: true,
    },
    {
      label: "게시기간",
      field: "DATE1",
      width: "10%",
      sortable: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "DATE1",
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
    data = await operateApi.operateManagement(param);
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
    data = await operateApi.operateManagementDelete(param);
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
    data = await operateApi.operateManagementSave(param);
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
</script>
<template>
  <div class="section section__management">
    <div class="group__search">
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
    </div>
    <div class="group__contents">
      <div class="part__data_list" style="flex: 1">
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
                <tr
                  :class="selectRowData?.GNO == obj.GNO ? 'active' : ''"
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
                  <td>
                    <div>
                      <span> {{ obj.TITLE }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.DATE1 }} ~ {{ obj.DATE2 }}</span>
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
      <div
        class="part__data_detail"
        style="overflow: scroll; height: 70vh; flex: 2"
      >
        <div class="item__title">
          <i class="fa-solid fa-angle-right item__angle"></i>
          <span>상세보기</span>
        </div>
        <div class="item__contents">
          <div>
            <p>제목</p>
            <input type="text" v-model="selectRowData.TITLE" />
          </div>
          <div>
            <div class="mb-3">
              <label class="form-label">게시기간</label>
              <div class="row">
                <div class="col" style="position: relative">
                  <!--<Datepicker :disabled="selectRowData.SCHEDULE_USE_YN === 'N' ? true : false" v-model="selectRowData.DATE1" placeholder="선택하세요" format='yyyy-MM-dd'  language='ko' :locale="koreanLocale" :noMinutesOverlay="true" />-->
                  <Datepicker
                    class="form-control"
                    style="height: 42px; padding-left: 2rem"
                    v-model="selectRowData.DATE1"
                    placeholder="선택하세요"
                    format="yyyy-MM-dd"
                    :locale="ko"
                  />
                  <i
                    class="far fa-calendar-check fa-lg"
                    style="
                      position: absolute;
                      left: 20px;
                      top: 50%;
                      transform: translateY(-50%);
                      z-index: 999;
                    "
                  ></i>
                </div>
                <div
                  class="col-sm-auto"
                  style="padding-right: 0px; padding-left: 0px"
                >
                  ~
                </div>
                <div class="col" style="position: relative">
                  <!--<Datepicker :disabled="selectRowData.SCHEDULE_USE_YN === 'N' ? true : false" v-model="selectRowData.DATE2" placeholder="선택하세요" format='yyyy-MM-dd'  language='ko' :locale="koreanLocale" :noMinutesOverlay="true"/>-->
                  <Datepicker
                    class="form-control"
                    style="height: 42px; padding-left: 2rem"
                    v-model="selectRowData.DATE2"
                    placeholder="선택하세요"
                    format="yyyy-MM-dd"
                    :locale="ko"
                  />
                  <i
                    class="far fa-calendar-check fa-lg"
                    style="
                      position: absolute;
                      left: 20px;
                      top: 50%;
                      transform: translateY(-50%);
                      z-index: 999;
                    "
                  ></i>
                </div>
              </div>
              <div
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
                  >사용안함</label
                >
              </div>
            </div>
          </div>
          <div>
            <p>내용</p>
            <quill-editor
              theme="snow"
              ref="quillEditorRef"
              style="border: 1px solid #ced4da; border-radius: 4px"
            />
            <div style="text-align: end; font-size: x-small">
              <label v-if="selectRowData.UPDATE_DATE"
                >Update {{ selectRowData.UPDATE_DATE }}</label
              >
            </div>
          </div>
          <div></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
</style>
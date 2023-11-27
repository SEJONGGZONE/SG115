<script setup  lang="ts">
import { reactive, onMounted, inject, ref, watch, computed } from "vue";
import { useAppOptionStore } from "@/stores/app-option";
import { onBeforeRouteLeave } from "vue-router";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import { ScrollSpy } from "bootstrap";
import { orderApi } from "@/api";
import { Toast } from "bootstrap";
import {
  startLoadingBar,
  removeLoadingBar,
  addHoverClassToTr,
  removeHoverClassFromTr,
  doSort,
  showToast,
  addCursorClassToTr,
  removeCursorClassFromTr,
  excelDownload,
  numberWithCommas,
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

//검색조건
let searchKeyword = "";
const searchSelect = ref("10"); //결제일 10, 주문확정일 20

//메인 그리드에서 선택한 row 정보
const selectRowData = ref([]);

//모달창 변수
const totalRow = ref(0); //모달_총 선택 건수
const totalAmount = ref(0); //모달_총 합계

//전체선택
const isAll = ref({});

//달력 변수
const today = new Date(); // 현재 날짜와 시간을 포함하는 Date 객체 생성
const year = today.getFullYear(); // 년도 가져오기
const month = today.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
const day = today.getDate(); // 일 가져오기
const formattedToday = new Date(
  `${year}-${month.toString().padStart(2, "0")}-${day
    .toString()
    .padStart(2, "0")}`
);
const startDate = ref(formattedToday);
const endDate = ref(formattedToday);
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
      label: "주문번호",
      field: "ORDER_NO",
      width: "7%",
    },
    {
      label: "주문일시",
      field: "ORDER_DATE",
      width: "7%",
    },
    {
      label: "주문상태",
      field: "ORDER_STATUS",
      width: "10%",
      sortable: true,
    },
    {
      label: "거래처(사용자)",
      field: "CLNAME",
      width: "10%",
      sortable: true,
    },
    {
      label: "주문내용",
      field: "ITNAME",
      width: "10%",
      sortable: true,
    },
    {
      label: "금액",
      field: "ORDER_AMT",
      width: "10%",
      sortable: true,
      format: numberWithCommas,
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
/******************************************************************************* onMounted */

onMounted(() => {
  pageNumber.value = 1;
  doSearch();
});
/******************************************************************************* api 호출 start */
const doSearch = async () => {
  if (table.isLoading) return;
  table.isLoading = true;
  startLoadingBar();
  const param = {
    // searchType: "10",
    // startDate: "2010-04-01",
    // endDate: "2015-04-02",
    // keyword: "",
    // clcode: "0000000405",
    // itcode: "",
    // pageSize: pageSize.value,
    // pageNumber: pageNumber.value,
    // inputUser: "",

    searchType: searchSelect.value,
    startDate: startDate.value,
    endDate: endDate.value,
    keyword: searchKeyword,
    clcode: "", //userClcode,
    itcode: "",
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
    inputUser: "", // userId,
  };

  let data;
  try {
    data = await orderApi.orderManagement(param);
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
    var productDiv = document.getElementById("productDiv");
    setTimeout(() => {
      productDiv.scrollTop = productDiv.scrollHeight;
    }, 100);
  }
};

const doSave = async () => {
  let data;
  let order = "";
  let code = "";
  for (let i = 0; i < selectRowData.value.length; i++) {
    code = selectRowData.value[i].ORDER_NO;
    order = !order ? code : `${order}, ${code}`;
  }
  const param = {
    orderNoList: order,
    inputUser: userId,
  };

  try {
    data = await orderApi.orderManagementConfirm(param);
    if (data.ResultMsg === "SUCCESS") {
      showAlertSuccess("저장되었습니다.");
      document.getElementById("closeButton").click();
      table.rows = [];
      pageNumber.value = 1;
      doSearch();
    } else {
      showAlert("관리자에게 문의바랍니다.");
    }
  } catch (error) {
    console.error(error);
  }
};

/******************************************************************************* api 호출 end */

/******************************************************************************* 버튼 및 액션 이벤트 start */
const searchBtn = (keyword) => {
  table.rows = [];
  searchKeyword = keyword;
  pageNumber.value = 1;

  doSearch();
};
const isSelectCheck = computed(
  () => !!table.rows.filter((item) => item.CHECK === true).length
);
const ordeConfirm = () => {
  //주문확정 버튼
  if (!isSelectCheck) {
    showAlert("확정 할 주문을 선택해주세요.");
    return;
  }
  selectRowData.value = [];
  totalRow.value = 0;
  totalAmount.value = 0;
  selectRowData.value = table.rows.filter((item) => item.CHECK === true);
  selectRowData.value.map(
    (item) => (totalAmount.value += Number(item.ORDER_AMT))
  );
  totalRow.value = selectRowData.value.length;
};
const modalSaveBtn = () => {
  //모달 내 주문확정버튼
  doSave();
};
const searchMoteBtn = () => {
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};
const excelFileDownload = () => {
  excelDownload(table.rows, "orderManagerMent");
};

const chnageAllChk = (e) => {
  table.rows.map((item) => (item.CHECK = isAll.value));
};
const setDate = (type) => {
  /** 일주일 뒤 */
  const nextWeekEndDate = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000); // 일주일 뒤의 날짜
  const nextWeekyear = nextWeekEndDate.getFullYear(); // 년도 가져오기
  const nextWeekmonth = nextWeekEndDate.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
  const nextWeekday = nextWeekEndDate.getDate(); // 일 가져오기

  const nextWeekformattedEndDate = `${nextWeekyear}-${nextWeekmonth
    .toString()
    .padStart(2, "0")}-${nextWeekday.toString().padStart(2, "0")}`;
  /** 이번달 */
  const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0); // 이번달 마지막 날
  const thisMonthyear = lastDay.getFullYear(); // 년도 가져오기
  const thismonth = lastDay.getMonth() + 1; // 월 가져오기 (0부터 시작하므로 1 더하기)
  const thismonthStartday = "01"; // 일 가져오기
  const formattedLastDay = `${lastDay.getDate().toString().padStart(2, "0")}`;
  /** 저번달 */
  const beforeFirstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1); // 저번달 1일
  const beforeLastDay = new Date(today.getFullYear(), today.getMonth(), 0); // 저번달 마지막 날
  const prevMonth = (beforeFirstDay.getMonth() + 1).toString().padStart(2, "0"); // 저번달 월 가져오기
  const prevFirstDay = "01"; // 저번달 1일
  const prevLastDay = beforeLastDay.getDate().toString().padStart(2, "0"); // 저번달 마지막 날
  /** 3개월 전 */
  const threeMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 3, 1); // 3개월 전 월 1일
  const lastDayOfThreeMonthsAgo = new Date(
    today.getFullYear(),
    today.getMonth() - 2,
    0
  ); // 3개월 전 월 마지막 날
  const threeMonthsYear = threeMonthsAgo.getFullYear(); // 년도 가져오기
  const threeMonthsprevMonth = (threeMonthsAgo.getMonth() + 1)
    .toString()
    .padStart(2, "0"); // 이전달 월 가져오기
  const threeMonthsprevFirstDay = "01"; // 이전달 1일
  const threeMonthsprevLastDay = lastDayOfThreeMonthsAgo
    .getDate()
    .toString()
    .padStart(2, "0"); // 이전달 마지막 날

  console.log(formattedToday); // 예: "2023-04-29"
  if (type === "today") {
    startDate.value = new Date(formattedToday);
    endDate.value = new Date(formattedToday);
  } else if (type === "week") {
    startDate.value = new Date(formattedToday);
    endDate.value = new Date(nextWeekformattedEndDate);
  } else if (type === "thismonth") {
    startDate.value = new Date(
      `${thisMonthyear}-${thismonth}-${thismonthStartday}`
    );
    endDate.value = new Date(
      `${thisMonthyear}-${thismonth}-${formattedLastDay}`
    );
  } else if (type === "1month") {
    startDate.value = new Date(`${year}-${prevMonth}-${prevFirstDay}`);
    endDate.value = new Date(`${year}-${prevMonth}-${prevLastDay}`);
  } else if (type === "3month") {
    startDate.value = new Date(
      `${threeMonthsYear}-${threeMonthsprevMonth}-${threeMonthsprevFirstDay}`
    );
    endDate.value = new Date(
      `${threeMonthsYear}-${thismonth}-${formattedLastDay}`
    );
  }
};
</script>
<template>
  <div class="section section__management">
    <div class="group__title">
      <h2>주문조회/확정</h2>
    </div>
    <div class="group__search" style="height: auto">
      <div class="row">
        <div class="col-lg-2">
          <select
            class="form-select form-select-lg pt-0 pb-0"
            style="height: 50px"
            v-model="searchSelect"
          >
            <option value="10">결제일</option>
            <option value="20">주문확정일</option>
          </select>
        </div>
        <div class="col"></div>
        <div class="col-auto height50 pr-0 pl-0">
          <button
            type="button"
            class="btn btn-white me-1 mb-1"
            @click="setDate('today')"
          >
            오늘
          </button>
          <button
            type="button"
            class="btn btn-white me-1 mb-1"
            @click="setDate('week')"
          >
            일주일
          </button>
          <button
            type="button"
            class="btn btn-white me-1 mb-1"
            @click="setDate('thismonth')"
          >
            이번달
          </button>
          <button
            type="button"
            class="btn btn-white me-1 mb-1"
            @click="setDate('1month')"
          >
            1개월
          </button>
          <button
            type="button"
            class="btn btn-white me-1 mb-1"
            @click="setDate('3month')"
          >
            3개월
          </button>
        </div>
        <div class="col-auto">
          <div class="row center">
            <div class="col">
              <Datepicker
                class="form-control dp__input dp__action dp__month_year_row"
                v-model="startDate"
                placeholder="선택하세요"
                format="yyyy-MM-dd"
                :locale="ko"
              />
            </div>
            <div
              class="col-sm-auto"
              style="padding-right: 0px; padding-left: 0px"
            >
              ~
            </div>
            <div class="col">
              <Datepicker
                class="form-control dp__input dp__action dp__month_year_row"
                v-model="endDate"
                placeholder="선택하세요"
                format="yyyy-MM-dd"
                :locale="ko"
              />
            </div>
          </div>
        </div>
        <div class="part__search_box" style="width: 400px; height: 50px">
          <input
            type="text"
            v-on:keyup.enter="searchBtn(keyword)"
            v-model="keyword"
            placeholder="검색어를 입력하세요"
            style="height: 50px"
          />
          <button @click="searchBtn(keyword)" style="height: 50px">
            <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
          </button>
          <!-- 카톡 : 7.19 대표님 요청 : 주문 확정/엑셀 버튼 숨김 처리 -->
          <!-- <button  type="button"  style="width:120px;" @click="ordeConfirm()"><i class="fa fa-circle-check fa-fw fa-lg"></i>  주문확정</button> 
                <button @click="excelFileDownload()">
                  <i class="fa-solid fa-download"></i><span>엑셀</span>
                </button>  -->
        </div>
      </div>
    </div>

    <div class="group__contents">
      <div class="part__data_list" style="height: 60vh">
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
                  v-if="table.rows.length > 0"
                  v-for="(obj, index) in table.rows"
                  :key="index"
                  @mouseenter="addHoverClassToTr"
                  @mouseleave="removeHoverClassFromTr"
                >
                  <td v-for="(col, j) in table.columns" :key="j">
                    <div>
                      <input
                        v-if="col.field === 'CHECK'"
                        class="form-check-input"
                        type="checkbox"
                        v-model="obj['CHECK']"
                      />
                      <span v-else-if="col.field === 'CLTEL'"
                        >{{ obj[col.field] }}<br v-if="obj[col.field]" />{{
                          obj.CLPHONE
                        }}</span
                      >
                      <span v-else>{{
                        col.format ? col.format(obj[col.field]) : obj[col.field]
                      }}</span>
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

        <div class="item__buttons" v-if="table.isShowMoreBtn">
          <button @click="searchMoteBtn">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
        </div>
      </div>

      <!--관심수 modal-->
      <div
        class="modal fade"
        id="modalAccount"
        style="z-index: 1051 !important"
      >
        <div class="modal-dialog setModalCenter">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">주문확정</h5>
              <a href="#" class="btn-close" data-bs-dismiss="modal"></a>
            </div>
            <div class="modal-body">
              <div class="vtl vtl-card">
                <div class="vtl-card-body">
                  <div class="vtl-row">
                    <div class="col-sm-12" style="text-align: center">
                      <table
                        class="vtl-table vtl-table-hover vtl-table-bordered vtl-table-responsive vtl-table-responsive-sm table-striped table"
                      >
                        <thead class="vtl-thead">
                          <tr class="vtl-thead-tr">
                            <th class="vtl-thead-th">
                              <div class="vtl-thead-column">NO</div>
                            </th>
                            <th class="vtl-thead-th">
                              <div class="vtl-thead-column">주문번호</div>
                            </th>
                            <th class="vtl-thead-th">
                              <div class="vtl-thead-column">거래처명</div>
                            </th>
                            <th class="vtl-thead-th">
                              <div class="vtl-thead-column">금액</div>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(obj, index) in selectRowData"
                            :key="index"
                          >
                            <td>
                              <div>{{ index + 1 }}</div>
                            </td>
                            <td>
                              <div>{{ obj.ORDER_NO }}</div>
                            </td>
                            <td>
                              <div>{{ obj.CLNAME }}</div>
                            </td>
                            <td>
                              <div>{{ numberWithCommas(obj.ORDER_AMT) }}</div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div style="width: 100%; text-align: center">
                <h4>
                  주문 : {{ totalRow }}건 / 금액 :
                  {{ numberWithCommas(totalAmount) }}원
                </h4>
              </div>
              <div style="width: 100%; text-align: center">
                <span>위 주문에 대해 확정하시겠습니까?</span>
              </div>
              <a
                href="#"
                class="btn btn-white"
                data-bs-dismiss="modal"
                id="closeButton"
                ><i class="fa fa-circle-xmark fa-fw fa-lg"></i> 닫기</a
              >
              <button
                type="button"
                class="btn btn-primary"
                id="saveBtn"
                @click="modalSaveBtn()"
              >
                <i class="fa fa-circle-check fa-fw fa-lg"></i> 주문확정
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scope>
.center {
  display: flex;
  justify-content: center;
  align-items: center;
}
.dp__input_wrap input {
  height: 44px;
}

.dp__button {
  display: none;
}

.height50 button {
  height: 50px;
}
.v3dp__input_wrapper input {
  height: 50px;
}
</style>
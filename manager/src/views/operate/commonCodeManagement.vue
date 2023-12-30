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
import { operateApi } from "@/api";
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
const { showAlert, showAlertSuccess, showConfirm } = useAlert();

//공통코드 마스터
const masterCode = ref([]);
//공통코드 상세코드
const detailCode = ref([]);

const selectRow = ref(0); //선택 행의 자리수

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
const itsCode1 = ref("01");
const itsCode2 = ref("01");
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

const isMAsterDisabled = computed(() => !!selectRowDataDL.value.CODE_NO);
const isSubDisabled = computed(() => !!selectRowDataDR.value.CODE_NO);

const tableDR = reactive({
  //하단(D) 오른쪽(R) 상품목록 테이블 정보
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "상위코드",
      field: "CODE_CLASS",
      width: "10%",
    },
    {
      label: "코드번호",
      field: "CODE_CD",
      width: "10%",
      sortable: true,
      isKey: true,
    },
    {
      label: "코드명",
      field: "CODE_NAME",
      width: "8%",
      sortable: true,
      isKey: true,
    },
    {
      label: "순서",
      field: "SORT_NUM",
      width: "8%",
      sortable: true,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "CODE_CD",
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
      label: "그룹코드",
      field: "CODE_CLASS",
      width: "8%",
    },
    {
      label: "코드번호",
      field: "CODE_CD",
      width: "8%",
      sortable: true,
      isKey: true,
    },
    {
      label: "코드명",
      field: "CODE_NAME",
      width: "8%",
      sortable: true,
      isKey: true,
    },
    {
      label: "순서",
      field: "SORT_NUM",
      width: "8%",
      sortable: true,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "CODE_CD",
    sort: "asc",
  },
  isShowMoreBtn: false,
});

/******************************************************************************* onMounted */
onMounted(() => {
  nextTick(async () => {
    tableDR.rows = [];
    await selectCommonCode("master");
  });
});

/******************************************************************************* 버튼 및 액션 이벤트 start */

const chnageAllChkDR = (e) => {
  //하단왼쪽(DR) 헤더부 전체선택
  tableDR.rows.map((item) => (item.CHECK = isAllDR.value));
};
const inputCode = (input) => {
  //숫자만 입력
  var pattern = /^\d*$/;
  var value = input?.value;
  if (!pattern.test(value)) {
    input.value = value?.replace(/\D/g, "");
  }
};
const masterInitCode = () => {
  //마스터 코드 초기화
  selectRowDataDL.value = {};
  selectRowDataDR.value = {};
  tableDR.rows = [];
};
const masterSaveCode = async () => {
  //마스터 코드 저장
  if (!selectRowDataDL.value.CODE_CD) {
    showAlert("코드값을 입력해주세요.");
    return;
  }
  if (!selectRowDataDL.value.CODE_NAME) {
    showAlert("코드명을 입력해주세요.");
    return;
  }

  if (!selectRowDataDL.value.CODE_NO) {
    const findIndex = tableDL.rows.findIndex(
      (item) => item.CODE_CD === selectRowDataDL.value.CODE_CD
    );
    if (findIndex > 0) {
      showAlert("동일한 코드가 등록되어있습니다.");
      return;
    }
  }

  let params = {
    codeNo: "",
    codeClass: "0000",
    codeCd: selectRowDataDL.value.CODE_CD,
    codeName: selectRowDataDL.value.CODE_NAME,
    desc01: selectRowDataDL.value.DESC_01,
    desc02: selectRowDataDL.value.DESC_02,
    sortNum: selectRowDataDL.value.SORT_NUM,
    inputUser: userId,
  };
  if (selectRowDataDL.value.CODE_NO) {
    selectRow.value = tableDL.rows.findIndex(
      (item) => item.CODE_NO === selectRowDataDL.value.CODE_NO
    );
  } else {
    selectRow.value = tableDL.rows.length;
  }
  const resultObj = await operateApi.commCodeSave(params);
  const result = resultObj.data;
  console.log(result);
  if (result.ResultCode === "00") {
    showAlert("저장되었습니다.");
    tableDR.rows = [];
    selectRowDataDR.value = {};
    selectRowDataDL.value = {};
    selectCommonCode("master");
  }
};
const masterDelCode = () => {
  //마스터 코드 삭제
  if (tableDR.rows.length > 0) {
    showAlert("하위 코드가 존재합니다.");
    return;
  }

  showConfirm("삭제하시겠습니까?").then(async (result) => {
    if (result.isConfirmed) {
      let params = {
        codeClass: "0000",
        codeCd: selectRowDataDL.value.CODE_CD,
        inputUser: userId,
      };

      selectRow.value = 0;
      const resultObj = await operateApi.commCodeDel(params);
      const result = resultObj.data;
      if (result.ResultCode === "00") {
        showAlert("삭제 되었습니다.");
        tableDR.rows = [];
        selectRowDataDR.value = {};
        selectRowDataDL.value = {};
        selectCommonCode("master");
      }
    }
  });
};
const subInitCode = () => {
  //서브 코드 초기화
  selectRowDataDR.value = {};
};
const subSaveCode = async () => {
  //서브 코드 저장
  if (!selectRowDataDR.value.CODE_CD) {
    showAlert("코드값을 입력해주세요.");
    return;
  }
  if (!selectRowDataDR.value.CODE_NAME) {
    showAlert("코드명을 입력해주세요.");
    return;
  }

  const masterCode = selectRowDataDL.value.CODE_CD;

  if (!selectRowDataDR.value.CODE_NO) {
    const findIndex = tableDR.rows.findIndex(
      (item) => item.CODE_CD === selectRowDataDR.value.CODE_CD
    );
    if (findIndex > 0) {
      showAlert("동일한 코드가 등록되어있습니다.");
      return;
    }
  }
  let params = {
    codeNo: "",
    codeClass: masterCode,
    codeCd: selectRowDataDR.value.CODE_CD,
    codeName: selectRowDataDR.value.CODE_NAME,
    desc01: selectRowDataDR.value.DESC_01,
    desc02: selectRowDataDR.value.DESC_02,
    sortNum: selectRowDataDR.value.SORT_NUM,
    inputUser: userId,
  };
  const resultObj = await operateApi.commCodeSave(params);
  const result = resultObj.data;
  if (result.ResultCode === "00") {
    showAlert("저장되었습니다.");
    tableDR.rows = [];
    selectRowDataDR.value = {};
    selectCommonCode(masterCode);
  }
};
const subDelCode = () => {
  //서브 코드 삭제

  const masterCode = selectRowDataDL.value.CODE_CD;
  showConfirm("해당 코드를 삭제하시겠습니까?").then(async (result) => {
    if (result.isConfirmed) {
      let params = {
        codeClass: masterCode,
        codeCd: selectRowDataDR.value.CODE_CD,
        inputUser: userId,
      };
      const resultObj = await operateApi.commCodeDel(params);
      const result = resultObj.data;
      if (result.ResultCode === "00") {
        showAlert("삭제 되었습니다.");
        tableDR.rows = [];
        selectRowDataDR.value = {};
        selectCommonCode(masterCode);
      }
    }
  });
};

const selectFirstRowLeft = () => {
  const rows = document.querySelectorAll(".selectedLeft");
  if (rows) {
    rows[selectRow.value].click();
  }
};
const selectFirstRowRight = () => {
  const firstRow = document.querySelector(".selectedRight");
  if (firstRow) {
    firstRow.click();
  }
};
const handleRowClickDR = (rowData, event) => {
  //하단(D) 오른쪽(R) 그리드 row click event
  if (rowTargetObjDR) {
    rowTargetObjDR.classList.remove("table-good");
  }

  selectRowDataDR.value = JSON.parse(JSON.stringify(rowData));

  rowTargetObjDR = event.target.closest("tr");
  rowTargetObjDR.classList.add("table-good");
};
const handleRowClickDL = (rowData, event) => {
  //하단(D) 왼쪽(L) 그리드 row click event

  if (rowTargetObjDL) {
    rowTargetObjDL.classList.remove("table-good");
  }

  selectRowDataDL.value = JSON.parse(JSON.stringify(rowData));

  rowTargetObjDL = event.target.closest("tr");
  rowTargetObjDL.classList.add("table-good");
  selectRowDataDR.value = {};
  selectCommonCode(rowData.CODE_CD);
};

/******************************************************************************* api 호출 start */

const selectCommonCode = async (codeClass) => {
  let codeClassStr = "0000";
  if (codeClass !== "master") {
    codeClassStr = codeClass;
  }
  const param = {
    codeClass: codeClassStr,
    inputUser: userId,
    codeCd: "",
  };

  let data;
  try {
    const dataObj = await operateApi.commCodeSel(param);
    data = dataObj.data;
    if (data.ResultCode === "00") {
      if (codeClass === "master") {
        tableDL.rows = data.RecordSet;
        setTimeout(() => {
          selectFirstRowLeft();
        }, 500);
      } else {
        tableDR.rows = data.RecordSet;
        if (tableDR.rows.length > 0) {
          setTimeout(() => {
            selectFirstRowRight();
          }, 500);
        } else {
          selectRowDataDR.value = {};
        }
      }
    }
  } catch (error) {
    console.error(error);
  }
};
</script>
<template>
  <!-- 코드값 조회 영역 -->
  <div class="section section__management" style="gap: 2px 0px; height: 100%; padding-bottom: 0rem;">
    <!-- <div class="group__title">
      <h2>공통코드관리</h2>
    </div>
    <div></div> -->
    <div class="group__contents_sungchang">
      <!-- 상위코드 영역 -->
      <div class="part__data_list" style="height: 100vh;">
        <div class="left_side_detail_title">
          <i class="icon-list"></i>&nbsp;&nbsp;운영관리 - 공통코드 관리(상위)
        </div>
        <div class="item__scroll">
          <div class="unit__scroll">
            <table>
              <thead>
                <tr>
                  <th v-for="(col, index) in tableDL.columns" :key="index">
                    <div class="unit_bundle">
                      {{ col.label }}
                      <div class="unit__buttons" v-if="col.sortable">
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
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="selectedLeft"
                  :class="
                    selectRowDataDL?.CODE_CD == obj.CODE_CD ? 'active' : ''
                  "
                  v-if="tableDL.rows"
                  v-for="(obj, index) in tableDL.rows"
                  :key="index"
                  @click="handleRowClickDL(obj, $event)"
                >
                  <td
                    :class="col.class"
                    v-for="(col, j) in tableDL.columns"
                    :key="j"
                    v-html="obj[col.field]"
                  ></td>
                </tr>
                <tr v-else>
                  <td :colspan="tableDL.columns.length">
                    <span>조회된 데이터가 없습니다.</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- 버튼 -->
        <div class="grid_searcharea" style="justify-content: end; margin-top: 0.5rem;">
          <div class="item__buttons" >
            <button class="btn_search" @click="masterInitCode" style="width:5rem">
              <i class="fa fa-pen-to-square fa-fw"></i><span style="margin-left:0px;">신규</span>
            </button>
            <button class="btn_search" @click="masterSaveCode" style="width:5rem">
              <i class="fa fa-save fa-fw"></i><span style="margin-left:0px;">저장</span>
            </button>
            <button class="btn_search" @click="masterDelCode" style="width:5rem">
              <i class="fa fa-trash-can fa-fw"></i><span style="margin-left:0px;">삭제</span>
            </button>
          </div>
        </div>

        <div class="part__data_detail">
          <div class="item__title" style="font-size:1rem;">
            <i class="fa-solid fa-angle-right item__angle"></i>
            <span style="font-size:1rem;">상위코드</span>
          </div>
          <div class="item__contents">
            <div style="display: inline-flex; gap: 0px 20px">
              <div>
                <p>코드번호</p>
                <input
                  type="text"
                  :disabled="isMAsterDisabled"
                  v-model="selectRowDataDL.CODE_CD"
                  @input="inputCode(this)"
                  maxlength="3"
                />
              </div>
              <div>
                <p>코드명</p>
                <input type="text" v-model="selectRowDataDL.CODE_NAME" />
              </div>
              <div>
                <p>순서</p>
                <input
                  type="text"
                  v-model="selectRowDataDL.SORT_NUM"
                  @input="inputCode(this)"
                  maxlength="1"
                />
              </div>
            </div>
            <div style="display: inline-flex; gap: 0px 20px;">
              <div>
                <p>설명1</p>
                <input type="text" v-model="selectRowDataDL.DESC_01" />
              </div>
              <div>
                <p>설명2</p>
                <input type="text" v-model="selectRowDataDL.DESC_02" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- 하위코드 영역 -->
      <div class="part__data_list right_side" style="height: 100vh;">
        <div class="left_side_detail_title" style="">
          <i class="icon-list"></i>&nbsp;&nbsp;
          <label style="font-weight:600; margin-left:0.2rem; color:#f6f6f6"
                 v-if="selectRowDataDL.CODE_NAME"> '{{ selectRowDataDL.CODE_NAME }}'</label>
            <span style="font-size:0.6rem;"> - 하위코드</span>
        </div>
        <div class="item__scroll">
          <div class="unit__scroll">
            <table>
              <thead>
                <tr>
                  <th v-for="(col, index) in tableDR.columns" :key="index">
                    <div class="unit_bundle">
                      {{ col.label }}
                      <div class="unit__buttons" v-if="col.sortable">
                        <button
                          :class="
                            tableDR.sortable.order === col.field &&
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
                            tableDR.sortable.order === col.field &&
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
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="selectedRight"
                  :class="
                    selectRowDataDR?.CODE_CD == obj.CODE_CD ? 'active' : ''
                  "
                  v-if="tableDR.rows"
                  v-for="(obj, index) in tableDR.rows"
                  :key="index"
                  @click="handleRowClickDR(obj, $event)"
                >
                  <td
                    :class="col.class"
                    v-for="(col, j) in tableDR.columns"
                    :key="j"
                    v-html="obj[col.field]"
                  ></td>
                </tr>
                <tr v-else>
                  <td :colspan="tableDR.columns.length">
                    <span>조회된 데이터가 없습니다.</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div
              v-if="isDangerMsg"
              class="text-left mb-1"
              style="text-align: left"
            >
              *
              <span class="text-danger" style="font-size: 22px"
                >"변경사항 저장"</span
              >
              버튼을 클릭하여 변경사항을 저장 해주세요
            </div>
          </div>
        </div>

        <!-- 버튼 -->
        <div class="grid_searcharea" style="justify-content: end; margin-top: 0.5rem;">
          <div class="item__buttons" >
            <button class="btn_search" @click="subInitCode" style="width:5rem">
              <i class="fa fa-pen-to-square fa-fw"></i><span style="margin-left:0px;">신규</span>
            </button>
            <button class="btn_search" @click="subSaveCode" style="width:5rem">
              <i class="fa fa-save fa-fw"></i><span style="margin-left:0px;">저장</span>
            </button>
            <button class="btn_search" @click="subDelCode" style="width:5rem">
              <i class="fa fa-trash-can fa-fw"></i><span style="margin-left:0px;">삭제</span>
            </button>
          </div>
        </div>
        <!-- 상세내용 -->
        <div class="part__data_detail">
          <div class="item__title" style="font-size:1rem;">
            <i class="fa-solid fa-angle-right item__angle"></i>
            <span style="font-size:1rem;">하위코드</span>
          </div>
          <div class="item__contents" style="border: 0px solid red;">
            <div style="display: inline-flex; gap: 0px 20px">
              <div>
                <p>코드번호</p>
                <input
                  type="text"
                  :disabled="isSubDisabled || !isMAsterDisabled"
                  v-model="selectRowDataDR.CODE_CD"
                  @input="inputCode(this)"
                  maxlength="3"
                />
              </div>
              <div>
                <p>코드명</p>
                <input
                  type="text"
                  :disabled="!isMAsterDisabled"
                  v-model="selectRowDataDR.CODE_NAME"
                />
              </div>
              <div>
                <p>순서</p>
                <input
                  type="text"
                  :disabled="!isMAsterDisabled"
                  v-model="selectRowDataDR.SORT_NUM"
                  @input="inputCode(this)"
                  maxlength="1"
                />
              </div>
            </div>
            <div style="display: inline-flex; gap: 0px 20px">
              <div>
                <p>설명1</p>
                <input
                  type="text"
                  :disabled="!isMAsterDisabled"
                  v-model="selectRowDataDR.DESC_01"
                />
              </div>
              <div>
                <p>설명2</p>
                <input
                  type="text"
                  :disabled="!isMAsterDisabled"
                  v-model="selectRowDataDR.DESC_02"
                />
              </div>
              <div></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.left_side {
  width: 50%;
  /* 중앙 정렬 */
  display: flex;
  /*justify-content: center;*/
  min-width: 50%;
}
.right_side {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 50%;
}
</style>